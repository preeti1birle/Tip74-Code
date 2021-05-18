<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Management extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function horseManagement()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css'
		);
		$load['js']=array(
			'asset/js/management.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('management/horseManagement_list');
		$this->load->view('includes/footer');
	}

	public function jockeyManagement()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css'
		);
		$load['js']=array(
			'asset/js/management.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('management/jockeyManagement_list');
		$this->load->view('includes/footer');
	}

	public function trainerManagement()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css'
		);
		$load['js']=array(
			'asset/js/management.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('management/trainerManagement_list');
		$this->load->view('includes/footer');
	}



}
