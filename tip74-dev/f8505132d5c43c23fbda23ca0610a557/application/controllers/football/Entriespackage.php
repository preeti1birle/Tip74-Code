<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Entriespackage extends Admin_Controller_Secure {
	public function index()
	{
		$load['js']=array(
			'asset/js/football/entries.js',
			'asset/plugins/jquery.form.js'
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('football/entries/entries_list');
		$this->load->view('includes/footer');
	}
}