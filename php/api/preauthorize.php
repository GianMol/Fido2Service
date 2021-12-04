<?php
session_start();
include_once("../../constants.php");

if(isset($_SESSION['username']) && $_SESSION['username'] !== ''){

    $txid;
    $conn = mysqli_connect("localhost", "fido2service", "fido", "fido2service"); //connection to mysql database
    mysqli_query($conn, "set character set 'utf8'");

    $query = "SELECT max(txid) as txid FROM transactions WHERE 1"; //query to be executed in database
    $res = mysqli_query($conn, $query); //execution of the query
    if(mysqli_num_rows($res) === 0){ //if no row are returned, then this transaction is the first
        $txid = '1';
    }
    else{
        $txid = mysqli_fetch_assoc($res)['txid'] + 1;
	$txid = strval($txid);
    }
    mysqli_free_result($res); //freeing results and closing database
    mysqli_close($conn);

    /*
    "data" = {
    "svcinfo": {
        "did": 1,
        "protocol": "FIDO2_0",
        "authtype": "PASSWORD",
        "svcusername": "svcfidouser",
        "svcpassword": "Abcd1234!"
    },
    "payload": {
        "username": "johndoe",
        "txid": "ABC123",
        "txpayload": "ABBBBCCCDDD",
        "options": {}
    }
    }

    */

    $empty_obj = new stdClass();
    $txpayload = bin2hex(random_bytes(10));//generate cryptographically secure random strings
    $data = array(  //preparing data to send to the FIDO2 server
        'svcinfo' => SVCINFO,
        'payload' => array(
            'username' => $_SESSION['username'],
            'txid' => $txid,
            'txpayload' => $txpayload,
            'options' => $empty_obj,
        ),
    );

    $post_data = json_encode($data); //encoding data to be correctly put in the body of the request
    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_PREAUTHORIZE_PATH; //preparing the correct endpoint of the FIDO2 server

    $crl = curl_init($url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); //returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); //tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true); //does a regular HTTP POST
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); //sets the body of the POST request
    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); //sets the server port 
    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); //sets the path of server certificate
    curl_setopt($crl, CURLOPT_SSLCERT, CLIENT_CERTIFICATE_PATH);
    curl_setopt($crl, CURLOPT_SSLKEY, CLIENT_KEY_PATH);

    curl_setopt($crl, CURLOPT_HTTPHEADER, array( //sets headers
        'Content-Type: application/json', //data has to be read as json
        'Content-Length: ' . strlen($post_data) //sets the lenght of data
    ));

    $result = curl_exec($crl); //executing the request
    if($result === false){ //this is the error case
        $msg = "Preauthorize endpoint not found";
        $err = array(
            "status" => "404",
            'statusText' => $msg
        );
        echo json_encode($err);
        /*
        //verbose
        printf("cUrl error (#%d): %s<br>\n", curl_errno($crl), htmlspecialchars(curl_error($crl)));
        rewind($streamVerboseHandle);
        $verboseLog = stream_get_contents($streamVerboseHandle);
        console_log($verboseLog);*/

    }
    else{ //this is the success case
	if(str_contains(strtolower(json_encode($result)), 'error')){
	    $err = array(
		'status' => '500',
		'statusText' => $result
	    );
	    echo json_encode($err);
	    exit;
	}
        $send = array(
            "status" => "200",
            "result" => $result
        );
        $_SESSION['txid'] = $txid;
        $_SESSION['txpayload'] = $txpayload;
        echo json_encode($send);
    }
}
else{
   $msg = "Not Authorized";
   $err = array(
	"status" => "401", //Not Authorized
	"statusText" => $msg
   );
   echo json_encode($err);
}
?>
