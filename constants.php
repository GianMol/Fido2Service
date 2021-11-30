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

define('CERTIFICATE_PATH', 'C:\xampp\htdocs\fido2service\Fido2Service\certificate\centos-home.pem');
define('PRE_SKFS_HOSTNAME', 'https://');
define('SKFS_HOSTNAME', 'centos.home');//192.168.1.65
define('SKFS_PORT', '8181');
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
define('METADATA_VERSION', '1.0');
define('METADATA_LOCATION', 'Catania, CT');

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

?>