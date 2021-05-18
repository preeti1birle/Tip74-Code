<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Page_model');
	}

	/*
	Name: 			addPage
	Description: 	Use to add page data.	
	*/
	public function addPage_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('Title', 'Page Title', 'trim|required');
		$this->form_validation->set_rules('PageGUID', 'PageGUID', 'trim|required');
		$this->form_validation->set_rules('Content', 'Content', 'trim');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		
		if (!$this->Page_model->addPage($this->Post)) {
			$this->Return['ResponseCode']	= 500; 
			$this->Return['Message']      	= "Something went wrong! Please try again later."; 
		}else{
			$this->Return['Message']      	= "Page added successfully."; 
		}

	}

	/*
	Name: 			getPage
	Description: 	Use to get page data.
	*/
	public function getPage_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('PageGUID', 'PageGUID', 'trim');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$PageData = $this->Page_model->getPage('PageGUID,PageID,Title,Content',array("PageGUID" => @$this->Post['PageGUID']),TRUE,0);
		if($PageData){
			if (!empty($PageData['Content'])) {
				$PageData['Content'] = htmlentities($PageData['Content']);
			}
			$this->Return['Data'] = $PageData;
		}	
	}

	/*
	Name: 			savePage
	Description: 	Use to update page data.	
	*/
	public function editPage_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('PageGUID', 'PageGUID', 'trim|required');
		$this->form_validation->set_rules('Title', 'Page Title', 'trim');
		$this->form_validation->set_rules('Content', 'Content', 'trim');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$this->Page_model->editPage($this->Post['PageGUID'], array('Title'=>$this->Post['Title'], 'Content'=>$this->Post['Content']));

		$this->Return['Message']  =	"Updated successfully."; 
	}
}