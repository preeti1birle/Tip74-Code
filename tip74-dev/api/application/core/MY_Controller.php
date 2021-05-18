<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MAIN_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();		
	}
}

class MY_Controller_public extends MAIN_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('SessionUserID')) {redirect(base_url().'dashboard');exit();}		
	}
}

class MY_Controller_Secure extends MAIN_Controller
{
	public function __construct()
	{
		parent::__construct();
		/*ensure already signed in*/
		if (empty($this->session->userdata('SessionUserID'))){
			redirect(base_url()); exit;
		}
		else{
			$this->SessionUserID = $this->session->userdata('SessionUserID');//get User ID from session
		}
	}
}

include(APPPATH.'core/MY_API_Controller.php');