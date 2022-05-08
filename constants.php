<?php

// constants needed for authentication of RP with FIDO2 server
const DID = 1;
const PROTOCOL = "FIDO2_0";
const AUTHTYPE = "PASSWORD";
const SVCUSERNAME = "fido2service";
const SVCPASSWORD = "F1d02s3rv1c3_p4ssw0rd!";

// path of client key and certificate files, needed in case FIDO2 server requires TLS client authentication
define('CLIENT_KEY_PATH', '/etc/apache2/ssl/apache.key');
define('CLIENT_CERTIFICATE_PATH', '/etc/apache2/ssl/apache.pem');

define('CERTIFICATE_PATH', '/var/www/html/certificate/fidoserver.pem'); // path of server certificate file

// information about the location of FIDO2 server
define('PRE_SKFS_HOSTNAME', 'https://');
define('SKFS_HOSTNAME', 'fido2server.strongkey.com');
define('SKFS_PORT', '8181');

//define('API_VERSION', 'SK3_0');

// authentication object needed in the communication with FIDO2 server
define('SVCINFO', array(
    'did' => DID,
    'protocol' => PROTOCOL,
    'authtype' => AUTHTYPE,
    'svcusername' => SVCUSERNAME,
    'svcpassword' => SVCPASSWORD,
));

// FIDO2 server APIs paths
define('SKFS_PREAUTHENTICATE_PATH', '/skfs/rest/preauthenticate');
define('SKFS_AUTHENTICATE_PATH', '/skfs/rest/authenticate');
define('SKFS_PREREGISTRATION_PATH', '/skfs/rest/preregister');
define('SKFS_REGISTRATION_PATH', '/skfs/rest/register');
define('SKFS_GET_KEYS_INFO_PATH', '/skfs/rest/getkeysinfo');
define('SKFS_DEREGISTER_PATH', '/skfs/rest/deregister');
define('SKFS_PREAUTHORIZE_PATH', '/skfs/rest/preauthorize');
define('SKFS_AUTHORIZE_PATH', '/skfs/rest/authorize');

// metadata information needed in the communication with FIDO2 server
define('METADATA_VERSION', '1.0');
define('METADATA_LOCATION', 'Catania, CT');

// web application interfaces and APIs paths
define('FIDO2SERVICE_REGISTRATION_PATH', '/php/registration.php');
define('FIDO2SERVICE_LOGIN_PATH', '/php/login.php');
define('FIDO2SERVICE_RESOURCE_PATH', '/php/resource.php');
define('FIDO2SERVICE_HOME_PATH', '/');
define('FIDO2SERVICE_DEREGISTER_PATH', '/php/api/deregister.php');
define('FIDO2SERVICE_LOGOUT_PATH', '/php/api/logout.php');
define('FIDO2SERVICE_TRANSACTIONS_PATH', '/php/transactions.php');
define('FIDO2SERVICE_ADMIN_PATH', '/php/admin.php');

// web application database credentials and information
define('FIDO2SERVICE_DB_HOSTNAME', 'localhost');
define('FIDO2SERVICE_DB_USERNAME', 'fido2service');
define('FIDO2SERVICE_DB_PASSWORD', 'fido');
define('FIDO2SERVICE_DB_DATABASE', 'fido2service');

// LOG constants
define('FIDO2SERVICE_LOG_PATH', '/var/www/html/server.log'); // path of the log file
define('FIDO2SERVICE_ENABLE_LOG', TRUE); // boolean enabling logging

function console_log($output, $with_script_tags = true){
    $js_code = 'console.log('.json_encode($output, JSON_HEX_TAG).');';
    echo '<script>'.$js_code.'</script>';
}

function alert($msg) {
    echo "<script type='text/javascript'>alert('$msg');</script>";
}

function hidden($msg){
    $res = '<div class = "hidden">'.$msg.'</div>';
    echo $res;
}

function str_contains(string $haystack, string $needle):bool{
    return '' === $needle || false !== strpos($haystack, $needle);
}


function file_log(string $purpose, string $url, string $data, string $result){
    if(FIDO2SERVICE_ENABLE_LOG && $fp = fopen(FIDO2SERVICE_LOG_PATH, 'a')){ // opening the log file in append mode only if the FIDO2SERVICE_ENABLE_LOG boolean is set to true
        $date = date("d/m/Y H:i:s"); // exact date of log
        switch($purpose){ // switching basing on the purpose of the log
            case "curl": // case of curl: the web application has executed a curl to the FIDO2 server and logs the details
                // in this case, $url contains the API path, $data contains the data of the request, $result contains the data of the response
                fwrite($fp, "Curl executed at ".$date.", path: ".$url."\n\tRequest: ".$data."\n\tResponse: ".$result."\n");
                break;
            case "interface": // case of page shown: the user has done request to access to a page
                // in this case, $url contains information about the page viewed, $data contains information about the user, $result contains the name of the interface
                fwrite($fp, "Page viewed at ".$date.", path: ".$url.", page: ".$result.", username: ".$data."\n");
                break;
            case "logout": // case of logout
                // in this case, $url contains the path of the logout path, $data contains information about the user who wants to logout
                fwrite($fp, "Logout at ".$date.", path: ".$url.", username: ".$data."\n");
                break;
            default: // default case
                // this case is used to log custom messager, $data contains the whole message
                fwrite($fp, $data."\n");
        }
        fclose($fp); // closing the log file
    }
}

?>
