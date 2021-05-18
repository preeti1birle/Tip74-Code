<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Setup extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function index()
	{
		$load['css']=array(
		
		);
		$load['js']=array(
			'asset/js/'.$this->ModuleData['ModuleName'].'.js',
			
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('includes/footer');
	}
	public function group()
	{
		$load['css']=array(
			
		);
		$load['js']=array(
			'asset/js/group.js',			
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('setup/group');
		$this->load->view('includes/footer');
	}

}
