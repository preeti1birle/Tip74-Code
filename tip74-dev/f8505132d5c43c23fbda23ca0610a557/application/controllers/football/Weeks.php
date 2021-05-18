<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Weeks extends Admin_Controller_Secure {
	public function index()
	{
		$load['js']=array(
			'asset/js/football/week.js',
			'asset/plugins/jquery.form.js'
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('football/week/week_list');
		$this->load->view('includes/footer');
	}
}