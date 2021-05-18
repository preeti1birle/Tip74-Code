<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main_Controller extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			$Input = file_get_contents("php://input");
			$this->Post =  json_decode($Input,1);
			if(empty($this->Post)){
				parse_str($Input,$this->Post);
			}
		}	
	}
}

class Admin_Controller extends Main_Controller {
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('UserData')) {redirect(base_url().'dashboard');exit();}	
	}
}

class Admin_Controller_Secure extends Main_Controller {
	public function __construct()
	{
		parent::__construct();
		/* Ensure already Signed in */
		if (empty($this->session->userdata('UserData'))) {
			redirect(base_url()); exit;
		}
		$this->SessionData 		=	$this->session->userdata('UserData'); 
		if($this->uri->segment(1) != 'dashboard'){
			$this->ModuleData 	= 	ValidateUserAccess($this->SessionData['PermittedModules'],$this->uri->uri_string);
		}else{
			$this->ModuleData 	= 	ValidateUserAccess($this->SessionData['PermittedModules'],'dashboard');
		}
		$this->SessionKey		=	$this->SessionData['SessionKey'];	
		$this->UserGUID			=	$this->SessionData['UserGUID'];
		$this->Menu				=	$this->SessionData['Menu'];
		$this->UserTypeID 		= 	$this->SessionData['UserTypeID'];
	}
	
}