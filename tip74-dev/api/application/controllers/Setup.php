<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setup extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
	}


	/*
	Description: 	Use to get Get single category.
	URL: 			/api/setup/getGroups
	Input (Sample JSON): 		
	*/
	public function getGroups_post()
	{
		$GroupData = $this->Common_model->getUserTypes('', array("Permitted" => TRUE), TRUE);
		if (!empty($GroupData)) {
			$this->Return['Data'] = $GroupData['Data'];
		}
	}


	/*
	Description: 	Use to get Get single category.
	URL: 			/api/setup/getGroup
	Input (Sample JSON): 		
	*/
	public function getGroup_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('UserTypeGUID', 'UserTypeGUID', 'trim|required|callback_validateUserTypeGUID');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */

		$GroupData = $this->Common_model->getUserTypes('', array("UserTypeID" => $this->UserTypeID));
		if (!empty($GroupData)) {
			$this->Return['Data'] = $GroupData;
		}
	}

	/*
	Description: 	Use to get add group.
	URL: 			/api/setup/addGroup
	Input (Sample JSON): 		
	*/
	public function addGroup_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('GroupName', 'GroupName', 'trim|required|is_unique[tbl_users_type.UserTypeName]|max_length[20]');
		$this->form_validation->set_message('is_unique', 'User Type is already exist.');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		$UserTypeData = $this->Common_model->saveUserType($this->Post);	
		if ($UserTypeData) {
			$this->Return['UserTypeData'] =	$UserTypeData;
			$this->Return['ResponseCode'] 	=	200;
			$this->Return['Message']      	=	"Success.";
		}
	}


	/*
	Description: 	Use to get edit group.
	URL: 			/api/setup/editGroup
	Input (Sample JSON): 		
	*/
	public function editGroup_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('UserTypeGUID', 'UserTypeGUID', 'trim|required|callback_validateUserTypeGUID');
		$this->form_validation->set_rules('GroupName', 'GroupName', 'trim|required|max_length[20]|callback_validateUserTypeUnique');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */
		$this->Common_model->editUserType($this->UserTypeID, $this->Post);
		$GroupData = $this->Common_model->getUserTypes('', array("UserTypeID" => $this->UserTypeID, "Permitted" => TRUE));
		$this->Return['Data'] = $GroupData;
	}

	/*
	Description: 	Use to get delete group.
	URL: 			/api/setup/DeleteGroup
	Input (Sample JSON): 		
	*/
	public function DeleteGroup_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('UserTypeGUID','UserTypeGUID', 'trim|required|callback_validateUserTypeGUID');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */
		$this->Common_model->deleteUserType($this->UserTypeID);
	}

	/*------------------------------*/
	/*------------------------------*/
	function validateUserTypeGUID($UserTypeGUID)
	{
		$UserTypeData = $this->Common_model->getUserTypes('UserTypeID', array("UserTypeGUID" => $UserTypeGUID));
		if ($UserTypeData) {
			$this->UserTypeID = $UserTypeData['UserTypeID'];
			return TRUE;
		}
		$this->form_validation->set_message('validateUserTypeGUID', 'Invalid {field}.');
		return FALSE;
	}
	
	/*------------------------------*/
	/*------------------------------*/
	function validateUserTypeUnique($GroupName)
	{
		$ExistUserTypeId = $this->Common_model->CheckUserTypeUnique($GroupName);
		if ($ExistUserTypeId && $ExistUserTypeId!=$this->UserTypeID) {
			$this->form_validation->set_message('validateUserTypeUnique', 'User Type is already exist.');
			return FALSE;
		}else{			
			return TRUE;
		}
	}
}
