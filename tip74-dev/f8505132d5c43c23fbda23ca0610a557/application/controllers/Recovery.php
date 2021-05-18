<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Recovery extends Admin_Controller {

	public function index()
	{
		/* load view */
		$load['js']=array(
			'asset/js/recovery.js'
		);	
		$this->load->view('includes/header',$load);
		$this->load->view('recovery/recovery');
		$this->load->view('includes/footer');
	}

	public function reset()
	{
		/* load view */
		$load['js']=array(
			'asset/js/recovery.js'
		);	
		$this->load->view('includes/header',$load);
		$this->load->view('recovery/reset');
		$this->load->view('includes/footer');

	}








	

}
