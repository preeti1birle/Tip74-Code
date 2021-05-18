<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Management_model');
	}

	/*
	Name: 			addHorse
	Description: 	Use to add Horse data.	
	*/
	public function addHorse_post()
	{
        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
        $this->form_validation->set_rules('HorseName', 'Horse Name', 'trim|required|callback_validateHorseName');
        $this->form_validation->set_rules('Age', 'Age', 'trim|required');
        $this->form_validation->set_rules('MediaGUIDs', 'MediaGUIDs', 'trim');
		$this->form_validation->set_rules('Description', 'Description', 'trim');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		$HorseID = $this->Management_model->addHorse($this->Post);
		if (!$HorseID) {
			$this->Return['ResponseCode']	= 500; 
			$this->Return['Message']      	= "Something went wrong! Please try again later."; 
		}else{

            if (!empty($this->Post['MediaGUIDs'])) {
                $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
                foreach ($MediaGUIDsArray as $MediaGUID) {
                    $EntityData = $this->Entity_model->getEntity('E.EntityID MediaID', array('EntityGUID' => $MediaGUID, 'EntityTypeID' => 17));
                    if ($EntityData) {
                        $this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $HorseID);
                    }
                }
            }
			$this->Return['Message']      	= "Horses added successfully."; 
		}

	}

	/*
	Name: 			getHorseList
	Description: 	Use to get horse data.
	*/
	public function getHorseList_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('HorseGUID', 'HorseGUID', 'trim|callback_validateEntityGUID[Race,HorseID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$HorseData = $this->Management_model->getHorseList((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'HorseID' => @$this->HorseID)), (!empty($this->HorseID)) ? FALSE : TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($HorseData) {
            $this->Return['Data'] = (!empty($this->HorseID)) ? $HorseData :  $HorseData['Data'];
        }
	}

	/*
	Name: 			editHorse
	Description: 	Use to update horse data.	
	*/
	public function editHorse_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('HorseGUID', 'HorseGUID', 'trim|required|callback_validateEntityGUID[Race,HorseID]');
        $this->form_validation->set_rules('HorseName', 'Horse Name', 'trim|required');
		$this->form_validation->set_rules('Description', 'Description', 'trim');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
		$this->form_validation->validation($this);  /* Run validation */		
        /* Validation - ends */
        
        if($this->Management_model->updateHorseData($this->HorseID, $this->Post)){
            $this->Return['Data']    = $this->Management_model->getHorseList('HorseName,Description,Status,Age',array('HorseID' => $this->HorseID));
            $this->Return['Message'] = "Horse Data updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] =  "Something went wrong, please try again later";
        }
    }

    /*
	Name: 			addJockey
	Description: 	Use to add Jockey data.	
	*/
	public function addJockey_post()
	{
        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
		$this->form_validation->set_rules('JockeyName', 'Jockey Name', 'trim|required|callback_validateJockeyName');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		
		if (!$this->Management_model->addJockey($this->Post)) {
			$this->Return['ResponseCode']	= 500; 
			$this->Return['Message']      	= "Something went wrong! Please try again later."; 
		}else{
			$this->Return['Message']      	= "Jockey added successfully."; 
		}

    }
    
    /*
	Name: 			getJockeyList
	Description: 	Use to get jockey data.
	*/
	public function getJockeyList_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('JockeyGUID', 'JockeyGUID', 'trim|callback_validateEntityGUID[Race,JockeyID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$JockeyData = $this->Management_model->getJockeyList((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'JockeyID' => @$this->JockeyID)), (!empty($this->JockeyID)) ? FALSE : TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($JockeyData) {
            $this->Return['Data'] = (!empty($this->JockeyID)) ? $JockeyData :  $JockeyData['Data'];
        }
    }
    

    /*
	Name: 			editJockey
	Description: 	Use to update jockey data.	
	*/
	public function editJockey_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('JockeyGUID', 'JockeyGUID', 'trim|required|callback_validateEntityGUID[Race,JockeyID]');
        $this->form_validation->set_rules('JockeyName', 'Jockey Name', 'trim|required');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
		$this->form_validation->validation($this);  /* Run validation */		
        /* Validation - ends */
        
        if($this->Management_model->updateJockeyData($this->JockeyID, $this->Post)){
            $this->Return['Data']    = $this->Management_model->getJockeyList('JockeyName,Status',array('JockeyID' => $this->JockeyID));
            $this->Return['Message'] = "Jockey Data updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] =  "Something went wrong, please try again later";
        }
    }

    /*
	Name: 			addTrainer
	Description: 	Use to add Trainer data.	
	*/
	public function addTrainer_post()
	{
        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
		$this->form_validation->set_rules('TrainerName', 'Trainer Name', 'trim|required|callback_validateTrainerName');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		
		if (!$this->Management_model->addTrainer($this->Post)) {
			$this->Return['ResponseCode']	= 500; 
			$this->Return['Message']      	= "Something went wrong! Please try again later."; 
		}else{
			$this->Return['Message']      	= "Trainer added successfully."; 
		}

    }

    /*
	Name: 			getTrainerList
	Description: 	Use to get trainer data.
	*/
	public function getTrainerList_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('TrainerGUID', 'TrainerGUID', 'trim|callback_validateEntityGUID[Race,TrainerID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$TrainerData = $this->Management_model->getTrainerList((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'TrainerID' => @$this->TrainerID)), (!empty($this->TrainerID)) ? FALSE : TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($TrainerData) {
            $this->Return['Data'] = (!empty($this->TrainerID)) ? $TrainerData :  $TrainerData['Data'];
        }
    }


    /*
	Name: 			editTrainer
	Description: 	Use to update trainer data.	
	*/
	public function editTrainer_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('TrainerGUID', 'TrainerGUID', 'trim|required|callback_validateEntityGUID[Race,TrainerID]');
        $this->form_validation->set_rules('TrainerName', 'Trainer Name', 'trim|required');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
		$this->form_validation->validation($this);  /* Run validation */		
        /* Validation - ends */
        
        if($this->Management_model->updateTrainerData($this->TrainerID, $this->Post)){
            $this->Return['Data']    = $this->Management_model->getTrainerList('TrainerName,Status',array('TrainerID' => $this->TrainerID));
            $this->Return['Message'] = "Trainer Data updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] =  "Something went wrong, please try again later";
        }
	}
	
	public function validateHorseName()
	{
		/* To Check Match Status */
		$HorseDetails = $this->Management_model->getHorseList('HorseName',array('HorseName' => $this->Post['HorseName']));
        if ($HorseDetails) {
            $this->form_validation->set_message('validateHorseName', 'This name is already exist.');
            return FALSE;
		}
		return TRUE;
	}

	public function validateJockeyName()
	{
		/* To Check Match Status */
		$Details = $this->Management_model->getJockeyList('JockeyName',array('JockeyName' => $this->Post['JockeyName']));
        if ($Details) {
            $this->form_validation->set_message('validateJockeyName', 'This name is already exist.');
            return FALSE;
		}
		return TRUE;
	}

	public function validateTrainerName()
	{
		/* To Check Match Status */
		$Details = $this->Management_model->getTrainerList('TrainerName',array('TrainerName' => $this->Post['TrainerName']));
        if ($Details) {
            $this->form_validation->set_message('validateTrainerName', 'This name is already exist.');
            return FALSE;
		}
		return TRUE;
	}

    

}