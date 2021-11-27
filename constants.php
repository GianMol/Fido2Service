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


define('SKFS_HOSTNAME', 'centos.home');
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
define('METADATA_LOCATION', 'Cupertino, CA');

function console_log($output, $with_script_tags = true){
    $js_code = 'console.log('.json_encode($output, JSON_HEX_TAG).');';
    echo '<script>'.$js_code.'</script>';
}


?>