<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Broadcast extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function index()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css'
		);
		$load['js']=array(
			'asset/js/'.$this->ModuleData['ModuleName'].'.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
			'asset/js/tinymce.min.js',
		);	
		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('broadcast/broadcast_list');
		$this->load->view('includes/footer');
	}


	public function scheduling()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css',
			'asset/plugins/datepicker/css/bootstrap-datetimepicker.css'
		);
		$load['js']=array(
			'asset/js/broadcast.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
			'asset/js/tinymce.min.js',
			'asset/plugins/datepicker/js/bootstrap-datetimepicker.min.js'
		);	
		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('broadcast/broadcast_schedule_list');
		$this->load->view('includes/footer');
	}

	public function crons()
	{
		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css',
			'asset/plugins/datepicker/css/bootstrap-datetimepicker.css'
		);
		$load['js']=array(
			'asset/js/broadcast.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
			'asset/js/tinymce.min.js',
			'asset/plugins/datepicker/js/bootstrap-datetimepicker.min.js'
		);	
		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('broadcast/crons');
		$this->load->view('includes/footer');
	}
}
