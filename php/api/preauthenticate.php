<?php
session_start();
include_once("../../constants.php");

if( empty($_POST)){
    $_POST = json_decode(file_get_contents('php://input', true));
}

$username = "";

if(isset($_POST->username)){
    $username = $_POST->username;
}


if($username !== ""){ //checking if all the information are correctly set

    $conn = mysqli_connect("localhost", "root", "", "fido2service"); //connection to mysql database
    mysqli_query($conn, "set character set 'utf8'");
    $username = mysqli_real_escape_string($conn, $username); //sanitizing information sent by the client

    $query = "SELECT * FROM users WHERE username = '".$username."'"; //query to be executed in database
    $res = mysqli_query($conn, $query); //execution of the query
    if(mysqli_num_rows($res) === 0){ //if no row are returned, then the given username is not registered
        mysqli_free_result($res); //freeing results and closing database
        mysqli_close($conn);
        $msg = "Username not registered";
        $err = array(
            "status" => "409", //Conflict
            'statusText' => $msg
        );
        echo json_encode($err);
        exit;
    }
    else{ //in case the username is valid
        $id = mysqli_fetch_object($res)->id;
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
                "options": "{}"
            }
        }
        
        */
        $empty_obj = new stdClass();
        $data = array(  //preparing data to send to the FIDO2 server
            'svcinfo' => SVCINFO,
            'payload' => array(
                'username' => $username,
                'options' => $empty_obj,
            ),
        );
        $post_data = json_encode($data); //encoding data to be correctly put in the body of the request
        $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_PREAUTHENTICATE_PATH; //preparing the correct endpoint of the FIDO2 server

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
            $msg = "Preauthenticate endpoint not found";
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
            $send = array(
                "status" => "200",
                "result" => $result
            );
            $_SESSION['userId'] = $id;
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