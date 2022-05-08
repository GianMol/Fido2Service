<?php
session_start(); // get information about the session
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
$txpayload = "";

// assignment of all data needed
if(isset($_POST->txpayload)){
    $txpayload = $_POST->txpayload;
}

if(isset($_SESSION['username']) && $_SESSION['username'] !== '' && $txpayload !== ''){ // checking whether some needed data are missing
    if($conn = mysqli_connect(FIDO2SERVICE_DB_HOSTNAME, FIDO2SERVICE_DB_USERNAME, FIDO2SERVICE_DB_PASSWORD, FIDO2SERVICE_DB_DATABASE)){ // if the connection succeeds, then it is possible to make queries
        if(mysqli_query($conn, "set character set 'utf8'")){ // setting the format
            $txid; // declaration of transaction id. This is here needed since, depending on the existance of at least a transaction in the database, $txid will be equal to 1 (in case no transaction exists) or equal to the maximum value of txid attribute present in the database increased by 1
            // this query has two purposes: first, checking whether the database contains at least a transaction; second, obtaining the value of the maximum of txid attribute inside the database so that to increase it and assign this value to the txid of the new transaction
            $query = "SELECT max(txid) as txid FROM transactions WHERE 1";
            if($res = mysqli_query($conn, $query)){ // execution of the query and check of the return value.
                // success case
                if(mysqli_num_rows($res) === 0){ // if no row are returned, then this transaction is the first one 
                    $txid = '1';
                }
                else{ // if a row is returned, then the new transaction must have its identifier equal to the maximum one present in the database + 1
                    $txid = mysqli_fetch_assoc($res)['txid'] + 1;
                    $txid = strval($txid);
                }
                mysqli_free_result($res); // freeing the result

                /* An example of data structure to be sent as input for SKFS preauthorize endpoint is:

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

                // generating data to be sent to SKFS preauthorize endpoint
                $empty_obj = new stdClass();
                $data = array(
                    'svcinfo' => SVCINFO,
                    'payload' => array(
                        'username' => $_SESSION['username'],
                        'txid' => $txid,
                        'txpayload' => $txpayload,
                        'options' => $empty_obj,
                    ),
                );
                $post_data = json_encode($data); // encoding data to be correctly put in the body of the request
                $url = PRE_SKFS_HOSTNAME . SKFS_HOSTNAME . SKFS_PREAUTHORIZE_PATH; // preparing the correct endpoint of the FIDO2 server, which in this case is the preauthorize one
            
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
                    echo json_encode($err);
                }
                else{ //this is the success case
                    if(str_contains(strtolower(json_encode($result)), 'error')){ // check if the endpoint answered with an error
                        // in this case, an error object is generated and sent back to the client
                        $err = array(
                        'status' => '500',
                        'statusText' => $result
                        );
                        echo json_encode($err);
                    }
                    else{ // in case the endpoint gives back a positive result
                        // some temporary session attributes are set. These values are needed to have the assurance of the reference of the transaction
                        $_SESSION['txid'] = $txid;
                        $_SESSION['txpayload'] = $txpayload;

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
else{ // in case some needed data are missing
    // an error object is generated and sent back to the client
   $msg = "Not Authorized";
   $err = array(
	"status" => "401", //Not Authorized
	"statusText" => $msg
   );
   echo json_encode($err);
}
?>
