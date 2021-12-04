<?php
//define('DID', '1');
//define('PROTOCOL', 'FIDO2_0');
//define('AUTHTYPE', 'PASSWORD');
//define('SVCUSERNAME', 'svcfidouser');
//define('SVCPASSWORD', 'Abcd1234!');
const DID = 1;
const PROTOCOL = "FIDO2_0";
const AUTHTYPE = "PASSWORD";
const SVCUSERNAME = "svcfidouser";
const SVCPASSWORD = "Abcd1234!";

define('CLIENT_KEY_PATH', '/etc/apache2/ssl/apache.key');
define('CLIENT_CERTIFICATE_PATH', '/etc/apache2/ssl/cert.pem');
define('CERTIFICATE_PATH', '/var/www/html/certificate/fidoserver.pem');
define('PRE_SKFS_HOSTNAME', 'https://');
define('SKFS_HOSTNAME', 'fido2server.strongkey.com');//192.168.1.65
define('SKFS_PORT', '443');
define('SVCINFO', array(
    'did' => DID,
    'protocol' => PROTOCOL,
    'authtype' => AUTHTYPE,
    'svcusername' => SVCUSERNAME,
    'svcpassword' => SVCPASSWORD,
));
define('SKFS_PREAUTHENTICATE_PATH', '/skfs/rest/preauthenticate');
define('SKFS_AUTHENTICATE_PATH', '/skfs/rest/authenticate');
define('SKFS_PREREGISTRATION_PATH', '/skfs/rest/preregister');
define('SKFS_REGISTRATION_PATH', '/skfs/rest/register');
define('SKFS_GET_KEYS_INFO_PATH', '/skfs/rest/getkeysinfo');
define('SKFS_DEREGISTER_PATH', '/skfs/rest/deregister');
define('SKFS_PREAUTHORIZE_PATH', '/skfs/rest/preauthorize');
define('SKFS_AUTHORIZE_PATH', '/skfs/rest/authorize');
define('METADATA_VERSION', '1.0');
define('METADATA_LOCATION', 'Catania, CT');

define('FIDO2SERVICE_REGISTRATION_PATH', '/php/registration.php');
define('FIDO2SERVICE_LOGIN_PATH', '/php/login.php');
define('FIDO2SERVICE_RESOURCE_PATH', '/php/resource.php');
define('FIDO2SERVICE_HOME_PATH', '/');
define('FIDO2SERVICE_DEREGISTER_PATH', '/php/api/deregister.php');
define('FIDO2SERVICE_LOGOUT_PATH', '/php/api/logout.php');
define('FIDO2SERVICE_TRANSACTIONS_PATH', '/php/transactions.php');

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

?>
