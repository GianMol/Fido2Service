<?php
session_start(); // get information about the session
require_once("../../constants.php"); // constants.php is here mandatory for constants used for the communication with the FIDO2 server
if($_SERVER['REQUEST_METHOD'] !== 'POST'){ // if POST method is not used, then the user cannot access to this endpoint
    // if it happens, the user is redirected to the homepage
    header("location: ".FIDO2SERVICE_HOME_PATH);
    exit;
}

// declaration of all data needed
$id = "";
$username = "";

// assignment of all data needed
if(isset($_SESSION['id'])){
    $id = $_SESSION['id'];
}
else{
    $id = "id";
}
if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
}

if($id !== "" && $username !== ""){ // checking whether some needed data are missing

    /* An example of data structure to be sent as input for SKFS getkeysinfo endpoint is:

    "data" = {
        "svcinfo": {
            "did": 1,
            "protocol": "FIDO2_0",
            "authtype": "PASSWORD",
            "svcusername": "svcfidouser",
            "svcpassword": "Abcd1234!"
        },
        "payload": {
            "username": "johndoe"
        }
    }
    
    */

    // generating data to be sent to SKFS getkeysinfo endpoint
    $data = array(
        'svcinfo' => SVCINFO,
        'payload' => array(
            'username' => $username
        )
    );
    $post_data = json_encode($data); // encoding data to be correctly put in the body of the request

    $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_GET_KEYS_INFO_PATH; // preparing the correct endpoint of the FIDO2 server
    
    $crl = curl_init($url); // initialization of curl
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); // returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
    curl_setopt($crl, CURLINFO_HEADER_OUT, true); // tracks the handle's request string
    curl_setopt($crl, CURLOPT_POST, true); // sets the method of HTTP as POST
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
            "status" => "404",
            'statusText' => $msg
        );
        echo json_encode($err);}
    else{ //this is the success case

        /* An example of data structure to be sent as input for SKFS deregister endpoint is:

            "data" = {
                "svcinfo": {
                    "did": 1,
                    "protocol": "FIDO2_0",
                    "authtype": "PASSWORD",
                    "svcusername": "svcfidouser",
                    "svcpassword": "Abcd1234!"
                },
                "payload": {
                    "keyid": "79U433x2hykUyf-h02qXwEkpyLN15N61MhYDTlM6AuWi-rmrO7kA0LdP3nSJNYedw6AqAh6RZiWjIyh5b1npW4oMJRS1sYMJVkRbNVlwBpSy_0OW2pRKLvVSRjxzT7LXsGV_i4r7KRE83ItVOS_cDKbYn3axDcYiUNaRXAR1DfHC5UP3hpystaKsOKvfCop2oA0rfrymTsUmF7RGKP-MNCiMP_Z5EnO8hHntAs41kTg"
                }
            }
        */

        // generating data to be sent to SKFS deregister endpoint
        $data = array(
            'svcinfo' => SVCINFO,
            'payload' => array(
                'keyid' => json_decode($result)->Response->keys[0]->keyid
            )
        );

        $post_data = json_encode($data); // encoding data to be correctly put in the body of the request

        $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_DEREGISTER_PATH; // preparing the correct endpoint of the FIDO2 server

        $crl = curl_init($url); // initialization of curl
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true); // returns the transfer as a string of the return value of curl_exec() instead of outputting it directly
        curl_setopt($crl, CURLINFO_HEADER_OUT, true); // tracks the handle's request string
        curl_setopt($crl, CURLOPT_POST, true); // sets the method of HTTP as POST
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
                "status" => "404", // Not found
                'statusText' => $msg
            );
            echo json_encode($err);
        }
        else{// this is the success case
            //connection to mysql database
            if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
                if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
                    // sanitizing information about the user
                    $username = mysqli_real_escape_string($conn, $username);
                    $id = mysqli_real_escape_string($conn, $id);
                    $query = "DELETE FROM users WHERE id = '".$id."'"; // deletion of the user from the web application database
                    if($res = mysqli_query($conn, $query)){ // execution of the query and check of the return value
                        // in case of success, a success object is generated and sent back to the client
                        $send = array(
                            "status" => "200",
                            "result" => $result
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
session_destroy(); // destroying session
?>
