<?php
require_once("../../constants.php"); // constants.php is here mandatory for constants used for the communication with the FIDO2 server
if($_SERVER['REQUEST_METHOD'] !== 'POST'){ // if POST method is not used, then the user cannot access to this endpoint
    // if it happens, the user is redirected to the homepage
    header("location: ".FIDO2SERVICE_HOME_PATH);
    exit;
}
if(empty($_POST)){ // if POST data are not correctly obtained, then decode them 
    $_POST = json_decode(file_get_contents('php://input', true));
}

// declaration of all data needed
$username = "";
$firstname = "";
$lastname = "";
$displayname = "";
$id = "";
$rawId = "";
$type = "";
$attestationObject = "";
$clientDataJSON = "";
$reqOrigin = "";

// assignment of all data needed
if(isset($_POST->username)){
    $username = $_POST->username;
}
if(isset($_POST->firstname)){
    $firstname = $_POST->firstname;
}
if(isset($_POST->lastname)){
    $lastname = $_POST->lastname;
}
if(isset($_POST->displayname)){
    $displayname = $_POST->displayname;
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


if($firstname !== "" && $lastname !== "" && $username !== "" && $displayname !== "" && $id !== "" && 
$rawId !== "" && $type !== "" && $attestationObject !== "" && $clientDataJSON !== "" && $reqOrigin !== ""){  // checking whether some needed data are missing


    /* An example of data structure to be sent as input for SKFS preregister endpoint is:

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

    // generating data to be sent to SKFS preauthorize endpoint
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
    $post_data = json_encode($data); // encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_REGISTRATION_PATH; // preparing the correct endpoint of the FIDO2 server

    $crl = curl_init($url); // initialization of curl
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); // returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); // tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true);  // sets the method of HTTP as POST
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); // sets the body of the POST request
    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); // sets the server port 
    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); // sets the path of server certificate

    // in case FIDO2 server requires client authentication, here the application sets the path of client certificate and key
    curl_setopt($crl, CURLOPT_SSLCERT, CLIENT_CERTIFICATE_PATH);
    curl_setopt($crl, CURLOPT_SSLKEY, CLIENT_KEY_PATH);

    curl_setopt($crl, CURLOPT_HTTPHEADER, array( // sets headers
        'Content-Type: application/json', // data has to be read as json
        'Content-Length: ' . strlen($post_data) // sets the lenght of data
    ));

    $result = curl_exec($crl); // executing the request

    //test
    file_log("curl", $url, $post_data, $result);

    if($result === false){ // this is the error case
        // an error object is generated and sent back to the client
        $msg = "Error in connection with the server";
        $err = array(
            "status" => "404", //Not found
            'statusText' => $msg
        );
        echo json_encode($err);}
    else{ //this is the success case
        if(str_contains(strtolower(json_encode($result)), 'error')){ // check if the endpoint answered with an error
            // in this case, an error object is generated and sent back to the client
            $err = array(
                "status" => "500", //Internal server error
                'statusText' => $result
            );

            echo json_encode($err);
        }
        else{ // in case the endpoint gives back a positive result
            //connection to mysql database
            if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
                if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
                    //sanitizing information sent by the client
                    $username = mysqli_real_escape_string($conn, $username);
                    $firstname = mysqli_real_escape_string($conn, $firstname);
                    $lastname = mysqli_real_escape_string($conn, $lastname);
                    $displayname = mysqli_real_escape_string($conn, $displayname);
                    $newid; // declaration of user id. This is here needed since, depending on the existance of at least a user in the database, $id will be equal to 1 (in case no user exists) or equal to the maximum value of id attribute present in the database increased by 1

                    // this query has two purposes: first, checking whether the database contains at least a user; second, obtaining the value of the maximum of id attribute inside the database so that to increase it and assign this value to the id of the new user
                    $query = "SELECT MAX(id) FROM users WHERE 1";
                    if($res = mysqli_query($conn, $query)){ // execution of the query and check of the return value.
                        // success case
                        if(mysqli_num_rows($res) === 0){ // if no row are returned, then this user is the first one 
                            $newid = '1';
                        }
                        else{ // if a row is returned, then the new user must have its identifier equal to the maximum one present in the database + 1
                            $newid = mysqli_fetch_assoc($res)['MAX(id)'] + 1;
                            $newid = strval($newid);
                        }
                        mysqli_free_result($res); // freeing the result
                        // the aim of this query is to insert the new user
                        $query = "INSERT INTO users(id, username, first_name, last_name, display_name) VALUES('".$newid."', '".$username."', '".$firstname."', '".$lastname."', '".$displayname."')";
                        if($res = mysqli_query($conn, $query)){ // execution of the query and check of the return value.
                            // in case of success, a success object is generated and sent back to the client
                            $send = array(
                                "status" => "200",
                                "statusText" => $result
                            );
                            echo json_encode($send);
                        }
                        else{
                            // in case of error, an error object is generated and sent back to the client
                            $msg = "DB error";
                            $err = array(
                                "status" => "500", //Internal server error
                                'statusText' => $msg
                            );
                            echo json_encode($err);
                        }
                        mysqli_close($conn); // closing the connection
                    }
                    else{ // error case of the query related to the gather of the maximum id in the database
                        mysqli_close($conn); // closing the connection 

                        // in this case, an error object is generated and sent back to the client
                        $msg = "DB error";
                        $err = array(
                            "status" => "500", //Internal server error
                            'statusText' => $msg
                        );
                        echo json_encode($err);
                    }
                }
                else{ // error case of the query related to format setting
                    mysqli_close($conn); // closing the connection

                    // in this case, an error object is generated and sent back to the client
                    $msg = "DB error";
                    $err = array(
                        "status" => "500", //Internal server error
                        'statusText' => $msg
                    );
                    echo json_encode($err);
                }
            }
            else{ // if the result of mysqli_connect is false, then the connection does not succeed and an error occurs
                // in this case, an error object is generated and sent back to the client
                $msg = "DB error";
                $err = array(
                    "status" => "500", //Internal server error
                    'statusText' => $msg
                );
                echo json_encode($err);
            }
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
