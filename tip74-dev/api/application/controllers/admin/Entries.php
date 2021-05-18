<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entries extends API_Controller_Secure {

    function __construct() {
        parent::__construct();
        $this->load->model('Entries_model');

    }

    /*
      Name : game entries packages list
      Description : User to get game entries packages list
      URL : /admin/entries/packages/
    */
    public function packages_post() {

        /* Validation section */
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $EntriesData = $this->Entries_model->packagesList((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), $this->Post, (!empty($this->Post['EntriesID']) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($EntriesData) {
            $this->Return['Data'] = (!empty($this->Post['EntriesID'])) ? $EntriesData :  $EntriesData['Data'];
        }
    }

    /*
      Name : add game entries package
      Description : User to add game entries package
      URL : /admin/entries/addPackage/
    */
    public function addPackage_post() {

        /* Validation section */
        $this->form_validation->set_rules('NoOfEntries', 'NoOfEntries', 'trim|required|greater_than[0]|is_unique[tbl_game_entries_packages.NoOfEntries]|callback_validatePredictionDoubleUps');
        $this->form_validation->set_rules('NoOfPrediction', 'NoOfPrediction', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('EntriesAmount', 'EntriesAmount', 'trim|required|greater_than[0]|numeric');
        $this->form_validation->set_rules('NoOfDoubleUps', 'NoOfDoubleUps', 'trim|required|greater_than_equal_to[0]');
        $this->form_validation->set_message('is_unique', 'NoOfEntries is already created with same value.');
        $this->form_validation->set_message('greater_than', 'The {field} field value should be greater than to {param}.');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!$this->Entries_model->addPackage($this->Post)) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "An error occurred, please try again later.";
        } else {
            $this->Return['Message'] = "Entries package created successfully.";
        }
    }

    /*
      Name : edit game entries package
      Description : User to edit game entries package
      URL : /admin/entries/editPackage/
    */
    public function editPackage_post() {

        /* Validation section */
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|required|callback_validateEntriesID[Edit]');
        $this->form_validation->set_rules('NoOfEntries', 'NoOfEntries', 'trim|required|greater_than[0]|callback_validatePredictionDoubleUps');
        $this->form_validation->set_rules('NoOfPrediction', 'NoOfPrediction', 'trim|required|greater_than[0]');
        $this->form_validation->set_rules('EntriesAmount', 'EntriesAmount', 'trim|required|greater_than[0]|numeric');
        $this->form_validation->set_rules('NoOfDoubleUps', 'NoOfDoubleUps', 'trim|required|greater_than_equal_to[0]');
        $this->form_validation->set_message('greater_than', 'The {field} field value should be greater than to {param}.');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Entries_model->editPackage($this->Post);
        $this->Return['Message'] = "Entries package updated successfully.";
    }

    /*
      Name:         deletePackage
      Description:  Use to delete entries package to system.
      URL:          /admin/entries/deletePackage/
     */

    public function deletePackage_post() {
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|required|callback_validateEntriesID[Delete]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete Entries Package Data */
        $this->Entries_model->deletePackage($this->Post['EntriesID']);
        $this->Return['Message'] = "Entries package deleted successfully.";
    }

    /**
     * Function Name: validateEntriesID
     * Description:   To validate entries ID
     */
    public function validateEntriesID($EntriesID,$Type) {
        $EntriesData = $this->Entries_model->packagesList('NoOfEntries,NoOfPrediction,EntriesAmount,NoOfDoubleUps', array('EntriesID' => $EntriesID));
        if (!$EntriesData) {
            $this->form_validation->set_message('validateEntriesID', 'Invalid {field}.');
            return FALSE;
        }
        if($Type == 'Edit'){
            $EntriesID = $this->db->query('SELECT EntriesID FROM tbl_game_entries_packages WHERE EntriesID != '.$EntriesID.' AND NoOfEntries = '.$this->Post['NoOfEntries'].' LIMIT 1');
            if($EntriesID->num_rows() > 0){
                $this->form_validation->set_message('validateEntriesID', 'NoOfEntries is already created with same value.');
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Function Name: validatePredictionDoubleUps
     * Description:   To validate prediction double ups
     */
    public function validatePredictionDoubleUps($NoOfEntries) {
        list($IntPrediction,$DecimalPrediction) = explode('.',round($this->Post['NoOfPrediction'] / $this->Post['NoOfEntries'],2));
        if($DecimalPrediction > 0){
            $this->form_validation->set_message('validatePredictionDoubleUps', 'No Of Entries multiply with N number, should be equals to No Of Predictions.');
            return FALSE;
        }

        list($IntDoubleUps,$DecimalDoubleUps) = explode('.',round($this->Post['NoOfDoubleUps'] / $this->Post['NoOfEntries'],2));
        if($DecimalDoubleUps > 0){
            $this->form_validation->set_message('validatePredictionDoubleUps', 'No Of Entries multiply with N number, should be equals to No Of Double Ups.');
            return FALSE;
        }
        return TRUE;
    }

}
