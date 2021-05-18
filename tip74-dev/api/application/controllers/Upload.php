<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Media_model');
		$this->load->model('users_model');
	}


	/*
	Name: 			cropimage
	Description: 	Use to crom upload images.
	URL: 			/api/cromimage	
	*/
	public function cropimage_post()
	{
		/* Validation section */	
		$this->form_validation->set_rules('MediaGUIDs', 'MediaGUID', 'trim|required|callback_validateEntityGUID[Media,MediaID]');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */
		$MediaData = $this->Media_model->getMedia('MS.SectionFolderPath, MS.SectionThumbSize, MS.SectionMaintainRatio, M.MediaName, M.MediaExt',array("MediaID" => $this->MediaID));

		$this->load->library('image_lib');

		$image_config['image_library'] = 'gd2';
		$image_config['source_image'] = $MediaData['SectionFolderPath'].$MediaData['MediaName'];
		$image_config['new_image'] = $MediaData['SectionFolderPath'].$MediaData['MediaName'];
		$image_config['quality'] = "100%";
		$image_config['maintain_ratio'] = FALSE;
		$image_config['width'] = $this->Post['thumb-width'];
		$image_config['height'] = $this->Post['thumb-height'];
		$image_config['x_axis'] = $this->Post['x1-axis'];
		$image_config['y_axis'] = $this->Post['y1-axis'];
		$this->image_lib->clear();
		$this->image_lib->initialize($image_config); 
		$this->image_lib->crop();


		/*create thumbnail*/
		$this->Media_model->resizePicture(
			realpath($MediaData['SectionFolderPath'].$MediaData['MediaName']),
			FCPATH.$MediaData['SectionFolderPath'],
			$MediaData['MediaExt'],
			$MediaData['MediaName'],
			explode(",",$MediaData['SectionThumbSize']),
			$MediaData['SectionMaintainRatio']
		);

		$this->Return['Data']['MediaURL'] = BASE_URL.$MediaData['SectionFolderPath'].$MediaData['MediaName'];
	}


	/*
	Name: 			upload
	Description: 	Use to upload files.
	URL: 			/api/upload	/image
	*/
	public function image_post()
	{
		/* Validation section */	
		$this->form_validation->set_rules('Section', 'Section', 'trim|required|callback_validateSection');
		$this->form_validation->set_rules('File', '', 'callback_validateImage');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */

		/*first uploads in Temp DIR*/
		$MediaDetails =  $this->Media_model->uploadFile($this->SessionUserID, $this->SectionData['SectionID'], APPPATH.'../uploads/temp/','gif|jpg|png',@$this->Post);
		if(!$MediaDetails){
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"An error occurred, please try again later.";  
		}else{
			/*create thumbnail*/
			$this->Media_model->resizePicture($MediaDetails['MediaURL'],FCPATH.$this->SectionData['SectionFolderPath'], $MediaDetails['MediaExt'], $MediaDetails['MediaName'], explode(",",$this->SectionData['SectionThumbSize']), $this->SectionData['SectionMaintainRatio']);

			switch ($this->Post['Section']) {
				case 'ProfilePic':
				case 'ProfileCoverPic':
					/* assign media to user */
					$this->Media_model->addMediaToEntity($MediaDetails['MediaID'], $this->SessionUserID, $this->SessionUserID);

					/* update media for user profile or cover picture */
					$this->Users_model->updateUserInfo($this->SessionUserID, array($this->Post['Section']=>$MediaDetails['MediaName']));
					break;
				case 'File':
					/* assign media to user */
					$this->Media_model->addMediaToEntity($MediaDetails['MediaID'], $this->SessionUserID, $this->SessionUserID);
					break;

				default:
					break;
			}
			
			/*set return path*/
			$this->Return['Data']['MediaURL'] = BASE_URL.$this->SectionData['SectionFolderPath'].$MediaDetails['MediaName']; 	
			unlink($MediaDetails['MediaURL']); /* remove original from temp */
			$this->Return['Data']['MediaGUID'] = $MediaDetails['MediaGUID'];
			$this->Return['Message']      	=	"Successfully updated.";
		}
	}







	/*
	Name: 			upload
	Description: 	Use to upload files.
	URL: 			/api/upload/file
	*/
	public function file_post()
	{
		/* Validation section */	
		$this->form_validation->set_rules('Section', 'Section', 'trim|required|callback_validateSection');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */

		$this->EntityID = (!empty($this->EntityID) ? $this->EntityID : $this->SessionUserID);

		/*first uploads in Temp DIR*/
		$MediaDetails =  $this->Media_model->uploadFile($this->SessionUserID, $this->SectionData['SectionID'], FCPATH.$this->SectionData['SectionFolderPath'], '*');
		if(!$MediaDetails){
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"An error occurred, please try again later.";  
		}else{
			/*set return path*/
			$this->Return['Data']['MediaURL'] = BASE_URL.$this->SectionData['SectionFolderPath'].$MediaDetails['MediaName'];
			$this->Return['Data']['MediaGUID'] = $MediaDetails['MediaGUID'];
		}
	}













	/*
	Description: 	Use to delete entity.
	URL: 			/api/upload/delete/	
	*/
	public function delete_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('MediaGUID', 'MediaGUID', 'trim|required|callback_validateEntityGUID[Media,MediaID]');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$UserData=$this->Users_model->getUsers('IsAdmin',array('UserID'=>$this->SessionUserID));
		$MediaData = $this->Media_model->getMedia('M.UserID, M.MediaName,MS.SectionFolderPath, MS.SectionThumbSize',array("MediaID" => $this->MediaID));


		if($UserData['IsAdmin']!="Yes" && $MediaData['UserID']!=$this->SessionUserID){
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"You don't have permission to perform this action.";
			return FALSE;	
		}


		$SectionThumbSize = explode(',',$MediaData['SectionThumbSize']);
		/*delete primary image*/
		@unlink($MediaData['SectionFolderPath'].$MediaData['MediaName']);

		/*delete other images*/
		foreach($SectionThumbSize as $Value){
			@unlink($MediaData['SectionFolderPath'].$Value."_".$MediaData['MediaName']);
		}

		$this->Entity_model->deleteEntity($this->MediaID);
		$this->Return['Message']      	=	"Deleted successfully.";
	}




}
