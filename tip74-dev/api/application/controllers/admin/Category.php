<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Category_model');
	}



	/*
	Description: 	Use to add new category
	URL: 			/api_admin/category/add	
	*/
	public function add_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
		$this->form_validation->set_rules('CategoryTypeName', 'Category Type', 'trim|required|callback_validateCategoryTypeName');
		$this->form_validation->set_rules('CategoryName', 'Category Name', 'trim|required');
		$this->form_validation->set_rules('ParentCategoryGUID', 'Parent Category', 'trim|callback_validateEntityGUID[Category,ParentCategoryID]');
		$this->form_validation->set_rules('Status', 'Status', 'trim|required|callback_validateStatus');

		$this->form_validation->set_rules('MediaGUIDs', 'MediaGUIDs', 'trim'); /* Media GUIDs */
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$CategoryData = $this->Category_model->addCategory(@$this->ParentCategoryID, $this->SessionUserID, $this->CategoryTypeID, $this->Post['CategoryName'], $this->StatusID);
		if($CategoryData){
			$this->Return['Data']['CategoryGUID'] = $CategoryData['CategoryGUID'];

			/* check for media present - associate media with this Post */
			if(!empty($this->Post['MediaGUIDs'])){
				$MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
				foreach($MediaGUIDsArray as $MediaGUID){
					$EntityData=$this->Entity_model->getEntity('E.EntityID MediaID',array('EntityGUID'=>$MediaGUID, 'EntityTypeID'=>4));
					if ($EntityData){
						$this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $CategoryData['CategoryID']);
					}
				}
			}
			/* check for media present - associate media with this Post - ends */
			$this->Return['Message']      	=	"New category added successfully."; 
		}
	}






	/*
	Name: 			updateUserInfo
	Description: 	Use to update user profile info.
	URL: 			/user/updateProfile/	
	*/
	public function editCategory_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
		$this->form_validation->set_rules('CategoryGUID', 'CategoryGUID', 'trim|required|callback_validateEntityGUID[Category,CategoryID]');
		$this->form_validation->set_rules('CategoryName', 'Category Name', 'trim|required');
		$this->form_validation->set_rules('Status', 'Status', 'trim|required|callback_validateStatus');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		
		$this->Category_model->editCategory($this->CategoryID, array('CategoryName'=>$this->Post['CategoryName'], 'StatusID'=>$this->StatusID));

		/* check for media present - associate media with this Post */
		if(!empty($this->Post['MediaGUIDs'])){
			$MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
			foreach($MediaGUIDsArray as $MediaGUID){
				$EntityData=$this->Entity_model->getEntity('E.EntityID MediaID',array('EntityGUID'=>$MediaGUID, 'EntityTypeID'=>4));
				if ($EntityData){
					$this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $this->CategoryID);
				}
			}
		}
		/* check for media present - associate media with this Post - ends */


		$CategoryData = $this->Category_model->getCategories('',
			array("CategoryID"=>$this->CategoryID));
		$this->Return['Data'] = $CategoryData;
		$this->Return['Message']      	=	"Category updated successfully."; 
	}


	/*
	Description: 	use to get list of filters
	URL: 			/api_admin/entity/getFilterData	
	*/
	public function getFilterData_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('ParentCategoryGUID', 'Parent Category', 'trim|callback_validateEntityGUID[Category,ParentCategoryID]');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */


		$CategoryTypes = $this->Category_model->getCategoryTypes('',array("ParentCategoryID"=>@$this->ParentCategoryID),true,1,250);
		if($CategoryTypes){
			$Return['CategoryTypes'] = $CategoryTypes['Data']['Records'];			
		}
		$this->Return['Data'] = $Return;
	}
}
