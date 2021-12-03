<?php
include_once("../../constants.php");

if( empty($_POST)){
    $_POST = json_decode(file_get_contents('php://input', true));
}

$firstname = "";
$lastname = "";
$username = "";
$displayname = "";

if(isset($_POST->firstname)){
    $firstname = $_POST->firstname;
}
if(isset($_POST->lastname)){
    $lastname = $_POST->lastname;
}
if(isset($_POST->username)){
    $username = $_POST->username;
}
if(isset($_POST->displayname)){
    $displayname = $_POST->displayname;
}


if($firstname !== "" && $lastname !== "" && $username !== "" && $displayname !== ""){ //checking if all the information are correctly set

    $conn = mysqli_connect("localhost", "fido2service", "fido", "fido2service"); //connection to mysql database
    mysqli_query($conn, "set character set 'utf8'");
    $username = mysqli_real_escape_string($conn, $username); //sanitizing information sent by the client

    $query = "SELECT * FROM users WHERE username = '".$username."'"; //query to be executed in database
    $res = mysqli_query($conn, $query); //execution of the query
    if(mysqli_num_rows($res) !== 0){ //if a row is returned, then the chosen username is already in use
        mysqli_free_result($res); //freeing results and closing database
        mysqli_close($conn);
        $msg = "Username is already in use";
        $err = array(
            "status" => "409", //Conflict
            'statusText' => $msg
        );
        echo json_encode($err);
        exit;
    }
    else{ //in case the username is free to be used
        $firstname = mysqli_real_escape_string($conn, $firstname); //sanitizing information sent by the client
        $lastname = mysqli_real_escape_string($conn, $lastname);
        $displayname = mysqli_real_escape_string($conn, $displayname);

        //$empty_obj = new stdClass();

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
                "displayname": "johndoe_dn",
                "options": {
                    "attestation": "direct"
                },
                "extensions": "{}"
            }
        }
        
        */
        $data = array(  //preparing data to send to the FIDO2 server
            'svcinfo' => SVCINFO,
            'payload' => array(
                'username' => $username,
                'displayname' => $displayname,
                'options' => array(
                    'attestation' => 'direct',
                ),
                'extensions' => "{}",
            ),
        );
        $post_data = json_encode($data); //encoding data to be correctly put in the body of the request
        $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_PREREGISTRATION_PATH; //preparing the correct endpoint of the FIDO2 server
        
        $crl = curl_init($url);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); //returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
        curl_setopt($crl, CURLINFO_HEADER_OUT, true); //tracks the handle's request string
        curl_setopt($crl, CURLOPT_POST, true); //does a regular HTTP POST
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); //sets the body of the POST request
        curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); //sets the server port 
        curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); //sets the path of server certificate
	curl_setopt($crl, CURLOPT_SSLCERT, CLIENT_CERTIFICATE_PATH); //sets the path of the client certificate
	curl_setopt($crl, CURLOPT_SSLKEY, CLIENT_KEY_PATH); //sets the path of the client key

	curl_setopt($crl, CURLOPT_HTTPHEADER, array( //sets headers
            'Content-Type: application/json', //data has to be read as json
            'Content-Length: ' . strlen($post_data) //sets the lenght of data
        ));



/*
        //verbose
        curl_setopt($crl, CURLOPT_VERBOSE, true);
        $streamVerboseHandle = fopen('php://temp', 'w+');
        curl_setopt($crl, CURLOPT_STDERR, $streamVerboseHandle);
        $report = curl_getinfo($crl);
        console_log($report);
*/


        $result = curl_exec($crl); //executing the request

        if($result === false){ //this is the error case
            $msg = "Preregister endpoint not found";
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
            console_log($verboseLog);
*/
        }
        else{ //this is the success case
            $send = array(
                "status" => "200",
                "result" => $result
            );
            echo json_encode($send);
        }
    }

    mysqli_free_result($res);
    mysqli_close($conn);
}
else{
    $msg = "Unprocessable Entity";
    $err = array(
        "status" => "422", //Unprocessable Entity
        "statusText" => $msg
    );
    echo json_encode($err);
}
?>
