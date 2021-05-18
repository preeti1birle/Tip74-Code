<?php
if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

/*------------------------------*/
/*------------------------------*/	
function ValidateUserAccess($PermittedModules, $Path) {
	if(!empty($PermittedModules)){
	foreach($PermittedModules as $Value){
		if($Value['ModuleName'] == $Path){
			return $Value;
		}
	}}
	$Obj =& get_instance();
	$Obj->session->sess_destroy();
	exit("You do not have permission to access this module.");
	return false;
}

/*------------------------------*/
/*------------------------------*/	
function APICall($URL, $JSON='') {
	$CH = curl_init();
	$Headers = array('Accept: application/json', 'Content-Type: application/json');

	curl_setopt($CH, CURLOPT_URL, $URL);
	if ($JSON != '') {
		//curl_setopt($CH, CURLOPT_POST, count($JSON));
		curl_setopt($CH, CURLOPT_POSTFIELDS, $JSON);
	}

	curl_setopt($CH, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($CH, CURLOPT_CONNECTTIMEOUT, 50);
	curl_setopt($CH, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($CH, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($CH, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

	$Response = curl_exec($CH);

	$Response = json_decode($Response,true);
	curl_close($CH);
	return $Response;
}


/*------------------------------*/
/*------------------------------*/	
if (!function_exists('response')) {
	function response($data) {
		header('Content-type: application/json');
	echo json_encode($data/*,JSON_NUMERIC_CHECK*/);
	exit;
}
}

