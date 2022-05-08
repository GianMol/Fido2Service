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
$firstname = "";
$lastname = "";
$username = "";
$displayname = "";

// assignment of all data needed
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

if($firstname !== "" && $lastname !== "" && $username !== "" && $displayname !== ""){ // checking whether some needed data are missing
    if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
        if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
            // sanitizing information sent by the client. For performances reasons, initially only username is sanitized and, in case of success of the first query, the other data will be sanitized afterwards
            $username = mysqli_real_escape_string($conn, $username);
            // the purpose of this query is just checking whether the database contains the username chosen; if it does, then an error occurs
            $query = "SELECT username FROM users WHERE username = '".$username."'";
            if($res = mysqli_query($conn, $query)){ // execution of the query and check of the return value.
                // success case

                if(mysqli_num_rows($res) !== 0){ // if a row is returned, then the chosen username is already in use
                    // error case: an error message is generated and sent back to the client
                    $msg = "Username is already in use";
                    $err = array(
                        "status" => "409", //Conflict
                        'statusText' => $msg
                    );
                    echo json_encode($err);
                }
                else{ // in case the username is free to be used
                    // success case
                    // sanitizing other information sent by the client
                    $firstname = mysqli_real_escape_string($conn, $firstname);
                    $lastname = mysqli_real_escape_string($conn, $lastname);
                    $displayname = mysqli_real_escape_string($conn, $displayname);
            
                    /* An example of data structure to be sent as input for SKFS preregister endpoint is:

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

                    // generating data to be sent to SKFS preauthorize endpoint
                    $data = array(
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
            
                    $post_data = json_encode($data); // encoding data to be correctly put in the body of the request
                    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_PREREGISTRATION_PATH; // preparing the correct endpoint of the FIDO2 server
            
                    $crl = curl_init($url); // initialization of curl
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); // returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
                    curl_setopt($crl, CURLINFO_HEADER_OUT, true); // tracks the handle's request string
                    curl_setopt($crl, CURLOPT_POST, true); // sets the method of HTTP as POST
                    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data); // sets the body of the POST request
                    curl_setopt($crl, CURLOPT_PORT, SKFS_PORT); // sets the server port
                    curl_setopt($crl, CURLOPT_CAINFO, CERTIFICATE_PATH); // sets the path of server certificate

                    // in case FIDO2 server requires client authentication, here the application sets the path of client certificate and key
                    curl_setopt($crl, CURLOPT_SSLCERT, CLIENT_CERTIFICATE_PATH); // sets the path of the client certificate
                    curl_setopt($crl, CURLOPT_SSLKEY, CLIENT_KEY_PATH); // sets the path of the client key
            
                    curl_setopt($crl, CURLOPT_HTTPHEADER, array( // sets headers
                        'Content-Type: application/json', // data has to be read as json
                        'Content-Length: ' . strlen($post_data) // sets the lenght of data
                    ));
            
                    $result = curl_exec($crl); // executing the request

                    //test
                    file_log("curl", $url, $post_data, $result);
            
                    if($result === false){ //this is the error case
                        // an error object is generated and sent back to the client
                        $msg = "Error in connection with the server";
                        $err = array(
                            "status" => "404",
                            'statusText' => $msg
                        );
                        echo json_encode($err);
            
                    }
                    else{ //this is the success case
                        // a success object is generated and sent back to the client
                        $send = array(
                            "status" => "200",
                            "result" => $result
                        );
                        echo json_encode($send);
                    }
                }
                mysqli_free_result($res); // freeing the result of the query
            }
            else{
                // in case of error of the query related to the transaction, an error object is generated and sent back to the client
                $msg = "DB error";
                $err = array(
                    "status" => "500", //Internal server error
                    'statusText' => $msg
                );
                echo json_encode($err);
            }
            mysqli_close($conn); // closing the connection
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
else{
    $msg = "Unprocessable Entity";
    $err = array(
        "status" => "422", //Unprocessable Entity
        "statusText" => $msg
    );
    echo json_encode($err);
}
?>
