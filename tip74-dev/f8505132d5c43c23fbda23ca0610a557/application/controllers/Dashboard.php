<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function index()
	{
		$load['css']=array();
		$load['js']=array();

		/* load view */
		$load['js']=array(
			'asset/js/'.$this->ModuleData['ModuleName'].'.js',
		);

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('dashboard/dashboard');
		$this->load->view('includes/footer');
	}
	/*------------------------------*/
	/*------------------------------*/	
	public function signout($SessionKey='')  
	{  
		$JSON = json_encode(array(
			"SessionKey" 	=> $SessionKey
		));
		APICall(API_URL.'signin/signout/', $JSON); /* call API and get response */
		$this->session->sess_destroy();
		redirect(base_url(),'refresh');exit();  		
	} 
	



}
