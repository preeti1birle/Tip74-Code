<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Points extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Sports_model');
	}

	/*
	Name: 			update
	Description: 	Use to update sports point.
	URL: 			/points/update/	
	*/
	public function update_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('PointsCategory', 'PointsCategory', 'trim|required|in_list[Normal,InPlay,Reverse]');
		/* Validation - ends */
		
		$this->Sports_model->updatePoints($this->Post);
		$this->Return['Data'] = array();
	}

}
