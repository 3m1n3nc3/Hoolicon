<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

$CI = & get_instance(); 

$api = $CI->session->userdata('license'); 

$config['hoolicon_purchase_code'] = $CI->enc_lib->dycrypt($api['T284VS9zbjdKbXhmQ05DUXJQMEdNQT09']); // $api['response'];
$config['hoolicon_username']      = $CI->enc_lib->dycrypt($api['ckpRSmY1OXlaaCtCMnFzdmZoM0tvZz09']); // $api['username'];
$config['SSLK'] = $CI->enc_lib->dycrypt($api['T284VS9zbjdKbXhmQ05DUXJQMEdNQT09']); // $api['response']; 

$regex = "/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}-/";
if (!preg_match($regex, $config['SSLK'])) {
	$config['SSLK'] = ''; 
}

if (!$config['SSLK'] || !$CI->session->has_userdata('license')) {

	$url = $CI->curler->ApiUrl('license/activation');
	$api_init = $CI->curler->validate($url);

	$api_encrypt = array(
		$CI->enc_lib->encrypt('username') => $CI->enc_lib->encrypt($api_init['username']),
		$CI->enc_lib->encrypt('response') => $CI->enc_lib->encrypt($api_init['response']) 

	);
	$CI->session->set_userdata('license', $api_encrypt);

}

$check_license = $CI->curler->get_licence(); 

if (!$check_license['purchase_code']) {
	$CI->session->unset_userdata('license'); 
}
