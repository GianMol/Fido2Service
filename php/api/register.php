<?php
include_once("../../constants.php");

if( empty($_POST)){
    $_POST = json_decode(file_get_contents('php://input', true));
}


$intent = "";
$username = "";
$firstname = "";
$lastname = "";
$id = "";
$rawId = "";
$type = "";
$attestationObject = "";
$clientDataJSON = "";
$reqOrigin = "";

if(isset($_POST->intent)){
    $intent = $_POST->intent;
}
if(isset($_POST->username)){
    $username = $_POST->username;
}
if(isset($_POST->firstname)){
    $firstname = $_POST->firstname;
}
if(isset($_POST->lastname)){
    $lastname = $_POST->lastname;
}
if(isset($_POST->id)){
    $id = $_POST->id;
}
if(isset($_POST->rawId)){
    $rawId = $_POST->rawId;
}
if(isset($_POST->type)){
    $type = $_POST->type;
}
if(isset($_POST->attestationObject)){
    $attestationObject = $_POST->attestationObject;
}
if(isset($_POST->clientDataJSON)){
    $clientDataJSON = $_POST->clientDataJSON;
}
$reqOrigin = $_SERVER['HTTP_HOST'];


if($firstname !== "" && $lastname !== "" && $username !== "" && $intent !== "" && $id !== "" && 
$rawId !== "" && $type !== "" && $attestationObject !== "" && $clientDataJSON !== "" && $reqOrigin !== ""){ //checking if all the information are correctly set


    /*
    data: {
        "svcinfo": {
            "did": 1,
            "protocol": "FIDO2_0",
            "authtype": "PASSWORD",
            "svcusername": "svcfidouser",
            "svcpassword": "Abcd1234!"
        },
        "payload": {
            "strongkeyMetadata": {
                "version": "1.0",
                "create_location": "Sunnyvale, CA",
                "username": "johndoe",
                "origin": "https://demo4.strongkey.com"
            },
            "publicKeyCredential": {
                "id": "79U433x2hykUyf-h02qXwEkpyLN15N61MhYDTlM6AuWi-rmrO7kA0LdP3nSJNYedw6AqAh6RZiWjIyh5b1npW4oMJRS1sYMJVkRbNVlwBpSy_0OW2pRKLvVSRjxzT7LXsGV_i4r7KRE83ItVOS_cDKbYn3axDcYiUNaRXAR1DfHC5UP3hpystaKsOKvfCop2oA0rfrymTsUmF7RGKP-MNCiMP_Z5EnO8hHntAs41kTg",
                "rawId": "79U433x2hykUyf-h02qXwEkpyLN15N61MhYDTlM6AuWi-rmrO7kA0LdP3nSJNYedw6AqAh6RZiWjIyh5b1npW4oMJRS1sYMJVkRbNVlwBpSy_0OW2pRKLvVSRjxzT7LXsGV_i4r7KRE83ItVOS_cDKbYn3axDcYiUNaRXAR1DfHC5UP3hpystaKsOKvfCop2oA0rfrymTsUmF7RGKP-MNCiMP_Z5EnO8hHntAs41kTg",
                "response": {
                    "attestationObject": "o2NmbXRmcGFja2VkZ2F0dFN0bXSjY2FsZyZjc2lnWEcwRQIhAKh568CoVnRo3MIwVyLbYTiXuO7FTbsKfuqin4vhpu9YAiAEWQuISPN74PyBD_tpWmjKix9gg_sQjf7xj0hO096XDGN4NWOBWQHkMIIB4DCCAYOgAwIBAgIEbCtY8jAMBggqhkjOPQQDAgUAMGQxCzAJBgNVBAYTAlVTMRcwFQYDVQQKEw5TdHJvbmdBdXRoIEluYzEiMCAGA1UECxMZQXV0aGVudGljYXRvciBBdHRlc3RhdGlvbjEYMBYGA1UEAwwPQXR0ZXN0YXRpb25fS2V5MB4XDTE5MDcxODE3MTEyN1oXDTI5MDcxNTE3MTEyN1owZDELMAkGA1UEBhMCVVMxFzAVBgNVBAoTDlN0cm9uZ0F1dGggSW5jMSIwIAYDVQQLExlBdXRoZW50aWNhdG9yIEF0dGVzdGF0aW9uMRgwFgYDVQQDDA9BdHRlc3RhdGlvbl9LZXkwWTATBgcqhkjOPQIBBggqhkjOPQMBBwNCAAQx9IY-uvfEvZ9HaJX3yaYmOqSIYQxS3Oi3Ed7iw4zXGR5C4RaKyOQeIu1hK2QCgoq210KjwNFU3TpsqAMZLZmFoyEwHzAdBgNVHQ4EFgQUNELQ4HBDjTWzj9E0Z719E4EeLxgwDAYIKoZIzj0EAwIFAANJADBGAiEA7RbR2NCtyMQwiyGGOADy8rDHjNFPlZG8Ip9kr9iAKisCIQCi3cNAFjTL03-sk7C1lij7JQ6mO7rhfdDMfDXSjegwuWhhdXRoRGF0YVkBNPgUPcPowj_96fevjVCLWyuOXtHPc57ItRHBr0kyY4M-QQAAAAAAAAAAAAAAAAAAAAAAAAAAALDv1TjffHaHKRTJ_6HTapfASSnIs3Xk3rUyFgNOUzoC5aL6uas7uQDQt0_edIk1h53DoCoCHpFmJaMjKHlvWelbigwlFLWxgwlWRFs1WXAGlLL_Q5balEou9VJGPHNPstewZX-LivspETzci1U5L9wMptifdrENxiJQ1pFcBHUN8cLlQ_eGnKy1oqw4q98KinagDSt-vKZOxSYXtEYo_4w0KIw_9nkSc7yEee0CzjWROKUBAgMmIAEhWCDyaCL1FRBjx_tJLFlnzwTSys214ccamb3iM8ioevGOEiJYIG_S-DmdODz6_GN6nOT4nlcmu55QbWFZXu7anb-KQgdI",
                    "clientDataJSON": "eyJ0eXBlIjoid2ViYXV0aG4uY3JlYXRlIiwiY2hhbGxlbmdlIjoiTENkbXlPQ2ZEUzltZDVJZkFYTzhtZyIsIm9yaWdpbiI6Imh0dHBzOi8vcWEtaW5mb3N5cy1maWRvLTIuc3Ryb25na2V5LmNvbTo4MTgxIn0"
                },
                "type": "public-key"
            }
        }
    }
    
    */
    
    $metadataObj = array(
        'version' => METADATA_VERSION,
        'create_location' => METADATA_LOCATION,
        'username' => $username,
        'origin' => "https://" . $reqOrigin
    );

    $responseObj = array(
        'id' => $id,
        'rawId' => $rawId,
        'response' => array(
            'attestationObject' => $attestationObject,
            'clientDataJSON' => $clientDataJSON
        ),
        'type' => $type
    );

    $data = array(
        'svcinfo' => SVCINFO,
        'payload' => array(
            'strongkeyMetadata' => $metadataObj,
            'publicKeyCredential' => $responseObj
        )
    );
    $post_data = json_encode($data); //encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_REGISTRATION_PATH; //preparing the correct endpoint of the FIDO2 server
    
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
        $msg = "Registration failed";
        $err = array(
            "status" => "500",
            'error' => $msg
        );
        echo json_encode($err);}
    else{ //this is the success case
        if(str_contains(strtolower(json_encode($result)), 'error')){
            $err = array(
                "status" => "500",
                'error' => $result
            );
            echo json_encode($err);
            exit;
        }

        $conn = mysqli_connect("localhost", "root", "", "fido2service"); //connection to mysql database
        mysqli_query($conn, "set character set 'utf8'");
        
        $username = mysqli_real_escape_string($conn, $username); //sanitizing information sent by the client
        $firstname = mysqli_real_escape_string($conn, $firstname);
        $lastname = mysqli_real_escape_string($conn, $lastname);

    
        $query = "SELECT MAX(id) FROM users WHERE 1"; //query to be executed in database
        $res = mysqli_query($conn, $query); //execution of the query
        if(mysqli_num_rows($res) === 0){ //if no row is returned
            mysqli_free_result($res); //freeing results and closing database
            mysqli_close($conn);
            $msg = "DB error";
            $err = array(
                "status" => "500",
                'statusText' => $msg
            );
            echo json_encode($err);
            exit;
        }
        else{
            $id = mysqli_fetch_assoc($res)['MAX(id)'] + 1;
            $query = "INSERT INTO users(id, username, first_name, last_name) VALUES('".$id."', '".$username."', '".$firstname."', '".$lastname."')"; //query to be executed in database
            mysqli_query($conn, $query); //execution of the query
            mysqli_close($conn);
            $send = array(
                "status" => "200",
                "result" => $result
            );
            echo json_encode($send);
        }
    }
}
else{
    $msg = "Missing parameters";
    $err = array(
        "status" => "400",
        "statusText" => $msg
    );
    echo json_encode($err);
}
?>