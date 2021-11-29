<?php
include_once("../../constants.php");

if( empty($_POST)){
    $_POST = json_decode(file_get_contents('php://input', true));
}


$id = "";
$username = "";


if(isset($_POST->id)){
    $id = $_POST->id;
}
if(isset($_POST->username)){
    $username = $_POST->username;
}


if($id !== "" && $username !== ""){ //checking if all the information are correctly set

    $conn = mysqli_connect("localhost", "root", "", "fido2service"); //connection to mysql database
    mysqli_query($conn, "set character set 'utf8'");

    $username = mysqli_real_escape_string($conn, $username); //sanitizing information sent by the client
    $id = mysqli_real_escape_string($conn, $id);

    $query = "DELETE FROM users WHERE id = '".$id."'"; //query to be executed in database
    mysqli_query($conn, $query);
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
        }
    }
    
    */

    $data = array(
        'svcinfo' => SVCINFO,
        'payload' => array(
            'username' => $username
        )
    );
    $post_data = json_encode($data); //encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_GET_KEYS_INFO_PATH; //preparing the correct endpoint of the FIDO2 server
    
    $crl = curl_init($url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); //returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); //tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true); //does a regular HTTP POST
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); //sets the body of the POST request
    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); //sets the server port 
    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); //sets the path of server certificate
    curl_setopt($crl, CURLOPT_HTTPHEADER, array( //sets headers
        'Content-Type: application/json', //data has to be read as json
        'Content-Length: ' . strlen($post_data) //sets the lenght of data
    ));

    $result = curl_exec($crl); //executing the request
    
    if($result === false){ //this is the error case
        $msg = "Get keys endpoint not found";
        $err = array(
            "status" => "404",
            'statusText' => $msg
        );
        echo json_encode($err);}
    else{ //this is the success case
        $data = array(
            'svcinfo' => SVCINFO,
            'payload' => array(
                'keyid' => $result->Response->keys[0]->keyid
            )
        );

        $post_data = json_encode($data); //encoding data to be correctly put in the body of the request

        $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_DEREGISTER_PATH; //preparing the correct endpoint of the FIDO2 server
        
        $crl = curl_init($url);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); //returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
        curl_setopt($crl, CURLINFO_HEADER_OUT, true); //tracks the handle's request string
        curl_setopt($crl, CURLOPT_POST, true); //does a regular HTTP POST
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); //sets the body of the POST request
        curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); //sets the server port 
        curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); //sets the path of server certificate
        curl_setopt($crl, CURLOPT_HTTPHEADER, array( //sets headers
            'Content-Type: application/json', //data has to be read as json
            'Content-Length: ' . strlen($post_data) //sets the lenght of data
        ));

        $result = curl_exec($crl); //executing the request
        if($result === false){ //this is the error case
            $msg = "Deregister endpoint not found";
            $err = array(
                "status" => "404",
                'statusText' => $msg
            );
            echo json_encode($err);
        }
        else{
            $send = array(
                "status" => "200",
                "result" => $result
            );
            echo json_encode($send);
        }
    }
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