<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DepositHistory extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function index()
	{
		$load['css']=array();
		$load['js']=array();

		/* load view */
		$load['js']=array(
			'asset/js/dashboard.js',
		);

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('dashboard/depositHistory');
		$this->load->view('includes/footer');
	}
	/*------------------------------*/
	/*------------------------------*/	
}