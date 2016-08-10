<?php


global $CFG, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/pfc/lib.php');
require_once $CFG->dirroot . '/local/pfc/lib/OpenIDConnectPHP/OpenIDConnectClient.php';


echo local_pfc_config::API_IDENTITY_PROVIDER_URL;

try{
    $oidc = new OpenIDConnectClient(local_pfc_config::API_IDENTITY_PROVIDER_URL,
        local_pfc_config::API_CLIENT_ID,
        local_pfc_config::API_CLIENT_SECRET);
    $oidc->authenticate();
    $name = $oidc->requestUserInfo('given_name');
    echo $name;
}catch (Exception $e){
    echo $e->getMessage();
}