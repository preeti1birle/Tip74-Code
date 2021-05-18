<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Settings_model');
	}

	/*
	Name: 			getSettings
	Description: 	Use to get settings list.
	*/
	public function getSettings_post()
	{
		$SettingData = $this->Settings_model->getSettings($this->SessionUserID);
		if($SettingData){
			$this->Return['Data'] = $SettingData;
		}	
	}


	/*
	Name: 			updateSettings
	Description: 	Use to update user setting.	
	*/
	public function updateSettings_post()
	{
		$this->Settings_model->updateSettings($this->SessionUserID, $this->Post);
		$SettingData = $this->Settings_model->getSettings($this->SessionUserID);
		if($SettingData){
			$this->Return['Data'] = $SettingData;
		}	
	}




}
