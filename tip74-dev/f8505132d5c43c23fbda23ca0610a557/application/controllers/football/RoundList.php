<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class RoundList extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function index()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css'		);
		$load['js']=array(
			'asset/js/football/leagues.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js'
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('football/leagues/round_list');
		$this->load->view('includes/footer');
	}



}
