<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Entity extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
	}




	/*
	Name: 			action
	Description: 	Use to Liked,Flagged,Saved entity.
	URL: 			/api/entity/action	
	*/
	public function action_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('EntityGUID', 'EntityGUID', 'trim|required|callback_validateEntityGUID');
		$this->form_validation->set_rules('Action', 'Action', 'trim|required|in_list[Liked,Flagged,Saved,Blocked,Vote]');
		$this->form_validation->set_rules('Text1', 'Text1', 'trim');
		$this->form_validation->set_rules('Text2', 'Text2', 'trim');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$ReturnMsg = $this->Entity_model->action($this->SessionUserID, $this->EntityID,$this->Post['Action'],
			array("Text1"=>@$this->Post['Text1'], "Text2"=>@$this->Post['Text2']));
		if($ReturnMsg== 'Added'){
			$this->Return['Data']['Undo'] = '';
			if($this->Post['Action']=='Liked'){
				$this->Return['Message'] = 'Liked';
			}
			elseif($this->Post['Action']=='Flagged'){
				$this->Return['Message'] = 'Inappropriate content flagged.';
			}
			elseif($this->Post['Action']=='Saved'){
				$this->Return['Message'] = 'Saved';
			}
			elseif($this->Post['Action']=='Blocked'){
				$this->Return['Message'] = 'Blocked';
			}
			elseif($this->Post['Action']=='Vote'){
				$this->Return['Message'] = 'Voted';
			}
		}else{
			$this->Return['Data']['Undo'] = 'Yes';
		}

		/*get count*/
		$EntityData=$this->Entity_model->getEntity('E.'.$this->Post['Action'].'Count Count',array('EntityID'=>$this->EntityID));
		$this->Return['Data'][$this->Post['Action'].'Count'] = $EntityData['Count'];
	}
	/*
	Description: 	Use to delete entity.
	URL: 			/api_admin/entity/delete/	
	*/
	public function deleteBanner_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('BannerID', 'BannerID', 'trim|required');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		$this->Entity_model->deleteEntity($this->Post['BannerID']);
		$this->Return['Message']      	=	"Deleted successfully.";

	}


	/*
	Description: 	Use to delete entity.
	URL: 			/api_admin/entity/delete/	
	*/
	public function delete_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('EntityGUID', 'EntityGUID', 'trim|required|callback_validateEntityGUID');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		$this->Entity_model->deleteEntity($this->EntityID);
		$this->Return['Message']      	=	"Deleted successfully.";

	}

}