<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entries extends API_Controller_Secure {

    function __construct() {
        parent::__construct();
        $this->load->model('Entries_model');
        $this->load->model('Notification_model');
    }

    /*
      Name : game entries packages list
      Description : User to get game entries packages list
      URL : /entries/packages/
    */
    public function packages_post() {

        /* Validation section */
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $EntriesData = $this->Entries_model->packagesList((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), $this->Post, (!empty($this->Post['EntriesID']) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($EntriesData) {
            $this->Return['Data'] = $EntriesData;
        }
    }

    /*
      Name : assign game entries
      Description : User to assign game entries 
      URL : /entries/assign/
    */
    public function assign_post() {

        /* Validation section */
        $this->form_validation->set_rules('GameEntryID', 'GameEntryID', 'trim|required');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|required|callback_validateEntityGUID[Weeks,WeekID]|callback_validateGameEntryID');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $PurchaseData = $this->Entries_model->assignEntries(array_merge($this->Post,array("WeekID" => $this->WeekID)), $this->SessionUserID);
        if ($PurchaseData) {
            $this->Return['Data'] = $PurchaseData;
        }
    }

    /*
      Name : get assigned game entries
      Description : User to get assign game entries list
      URL : /entries/assignedEntriesList/
    */
    public function assignedEntriesList_post() {
        $Query = $this->db->query('SELECT U.PurchasedEntries,U.AllowedPredictions,U.TotalPurchasedDoubleUps,W.WeekGUID,W.WeekCount FROM tbl_users_game_entries U, `football_sports_season_weeks` W WHERE U.WeekID = W.WeekID AND U.UserID = '.$this->SessionUserID.' ORDER BY W.WeekCount ASC');
        if ($Query->num_rows() > 0) {
            $this->Return['Data'] = $Query->result_array();
        }
    }

    /*
      Name : purchase game entries
      Description : User to purchase game entries
      URL : /entries/purchase/
    */
    public function purchase_post() {

        /* Validation section */
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|required|integer|callback_validateEntriesID|callback_validateWalletAmount');
        $this->form_validation->set_rules('PurchaseType', 'PurchaseType', 'trim');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */
         if(isset($this->Post['PurchaseType']) && $this->Post['PurchaseType'] == 'Direct')
        {
            /* To Validate Remaining Consume Predictions */
            $Query = $this->db->query('SELECT EntryNo,(AllowedPredictions-ConsumedPredictions) RemainingPredicitons FROM `tbl_users_game_entries` WHERE UserID = '.$this->SessionUserID.' AND WeekID = '.$this->WeekID.' ORDER BY EntryNo DESC LIMIT 1');
            if($Query->num_rows() > 0 && $Query->row()->RemainingPredicitons > 0){

            	$this->Return['ResponseCode'] = 500;
                $this->Return['Message'] = $Query->row()->RemainingPredicitons.' predictions is already remaining for entry '.$Query->row()->EntryNo.'. First please use remaining predicitons, then you can purchase for new entry';
                exit;
            }
            $this->Post['WeekID'] = $this->WeekID;
        }
        $PurchaseData = $this->Entries_model->purchaseEntries($this->Post, $this->SessionUserID);
        if ($PurchaseData) {
            $this->Return['Data'] = $PurchaseData;
        }
    }

    /*
      Name : purchase game entries list
      Description : User to purchase game entries list
      URL : /entries/list/
    */
    public function list_post() {

        /* Validation section */
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $EntriesData = $this->Entries_model->purchaseEntriesLists($this->Post['Params'],array_merge($this->Post,array('SessionUserID' => $this->SessionUserID)),TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($EntriesData) {
            $this->Return['Data'] = $EntriesData['Data'];
        }
    }

    /*
      Name : purchase game packages
      Description : User to purchase game packages
      URL : /entries/purchasePackage/
    */
    public function purchasePackage_post() {

        /* Validation section */
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|required|integer|callback_validateEntriesID|callback_validateWalletAmount');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|required|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('IsDoubleUps', 'IsDoubleUps', 'trim|required|in_list[Yes,No]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */
        $PurchaseData = $this->Entries_model->purchasePackage(array_merge($this->Post,array("WeekID" => $this->WeekID)), $this->SessionUserID);
        if ($PurchaseData) {
            $this->Return['Data'] = $PurchaseData;
        }
    }

    /*
      Name : purchase game doubles ups
      Description : User to purchase game double ups
      URL : /entries/purchaseDoubleUps/
    */
    public function purchaseDoubleUps_post() {

        /* Validation section */
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|required|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('NoOfDoubleUps', 'NoOfDoubleUps', 'trim|required|is_natural_no_zero|callback_validateDoubleUpsLimit');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $PurchaseData = $this->Entries_model->purchaseDoubleUps(array_merge($this->Post,array("WeekID" => $this->WeekID)), $this->SessionUserID);
        if ($PurchaseData) {
            $this->Return['Data'] = $PurchaseData;
        }
    }

    /*
      Name : get user entries balance
      Description : User to get user entries balance
      URL : /entries/getUserBalance/
    */
    public function getUserBalance_post() {
        /* Validation section */
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|required|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Return['Data'] = $this->Entries_model->getUserEntriesBalance($this->SessionUserID, $this->WeekID,@$this->Post['EntryNo']);
    }

    /*
      Name : get user entries balance
      Description : User to get user entries balance
      URL : /entries/getUserEntriesBalance/
    */
    public function getUserEntriesBalance_post() {
        $this->Return['Data'] = $this->Entries_model->getUserPurchasedEntriesBalance($this->SessionUserID);
    }

    /**
     * Function Name: validateEntriesID
     * Description:   To validate entries ID
     */
    public function validateEntriesID($EntriesID) {
        $EntriesData = $this->Entries_model->packagesList('NoOfEntries,NoOfPrediction,EntriesAmount,NoOfDoubleUps,PerDoubleUpPrice', array('EntriesID' => $EntriesID));
        if (!$EntriesData) {
            $this->form_validation->set_message('validateEntriesID', 'Invalid {field}.');
            return FALSE;
        } else {
            $this->Post['NoOfEntries']      = $EntriesData['NoOfEntries'];
            $this->Post['NoOfPrediction']   = $EntriesData['NoOfPrediction'];
            $this->Post['EntriesAmount']    = $EntriesData['EntriesAmount'];
            $this->Post['NoOfDoubleUps']    = $EntriesData['NoOfDoubleUps'];
            $this->Post['PerDoubleUpPrice'] = $EntriesData['PerDoubleUpPrice'];
            return TRUE;
        }
    }

    /**
     * Function Name: validateEntriesBalance
     * Description:   To validate entries balance
     */
    public function validateEntriesBalance($NoOfEntries) {
        $EntriesData = $this->Entries_model->getUserPurchasedEntriesBalance($this->SessionUserID);
        if (empty($EntriesData['UnAssignedEntries'])) {
            $this->form_validation->set_message('validateEntriesID', 'You dont have any entries. Please purchase entries.');
            return FALSE;
        } else {
            $this->Post['NoOfPrediction'] = $this->Post['NoOfEntries'] * ($EntriesData['PurchasedPredictions'] / $EntriesData['PurchasedEntries']);
            $this->Post['NoOfDoubleUps']  = ($EntriesData['PurchasedDoubleups'] > 0) ?  $this->Post['NoOfEntries'] * ($EntriesData['PurchasedDoubleups'] / $EntriesData['PurchasedEntries']) : 0;
            return TRUE;
        }
    }

    /**
     * Function Name: validateWalletAmount
     * Description:   To validate wallet sufficient amount
     */
    public function validateWalletAmount($EntriesID) {

        /* To Validate Wallet Sufficient Amount */
        $WalletAmount = $this->db->query('SELECT WalletAmount FROM `tbl_users` WHERE UserID = '.$this->SessionUserID.' LIMIT 1')->row()->WalletAmount;
        if ($WalletAmount < $this->Post['EntriesAmount']) {
            $this->form_validation->set_message('validateWalletAmount', 'Please add money in your wallet to purchase Entires.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Function Name: validateDoubleUpsLimit
     * Description:   To validate use double ups purchase limit
     */
    public function validateDoubleUpsLimit($NoOfDoubleUps) {
        
        /* To Get User Entries Details */
        $UserEntries = $this->Entries_model->getUserEntriesBalance($this->SessionUserID, $this->WeekID);
        if($UserEntries->RemainingPurchaseDoubleUps == 0){
            $this->form_validation->set_message('validateDoubleUpsLimit', "You doesn't have any double ups, please purchase the entries packages to get the double ups.");
            return FALSE;
        }
        if ($UserEntries->RemainingPurchaseDoubleUps < $this->Post['NoOfDoubleUps']) {
            $this->form_validation->set_message('validateDoubleUpsLimit', 'You can purchase maximum '.$UserEntries->RemainingPurchaseDoubleUps.' double ups.');
            return FALSE;
        }

        /* To Validate Wallet Sufficient Amount */
        $WalletAmount = $this->db->query('SELECT WalletAmount FROM `tbl_users` WHERE UserID = '.$this->SessionUserID.' LIMIT 1')->row()->WalletAmount;

        /* To Get Per Double Price */
        $PerDoubleUpPrice = $this->db->query('SELECT `ConfigTypeValue` FROM `set_site_config` WHERE `ConfigTypeGUID` = "PerDoubleUpPrice" LIMIT 1')->row()->ConfigTypeValue;
        $this->Post['TotalRequiredAmount'] = $this->Post['NoOfDoubleUps'] * $PerDoubleUpPrice;
        if ($WalletAmount < $this->Post['TotalRequiredAmount']) {
            $this->form_validation->set_message('validateDoubleUpsLimit', 'Please add money in your wallet to purchase Entires or Double Ups.');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Function Name: validateGameEntryID
     * Description:   To validate game entry id
     */
    public function validateGameEntryID($GameEntryID) {

        /* To Validate Remaining Consume Predictions */
        $Query = $this->db->query('SELECT EntryNo,(AllowedPredictions-ConsumedPredictions) RemainingPredicitons FROM `tbl_users_game_entries` WHERE UserID = '.$this->SessionUserID.' AND WeekID = '.$this->WeekID.' ORDER BY EntryNo DESC LIMIT 1');
        if($Query->num_rows() > 0 && $Query->row()->RemainingPredicitons > 0){
            $this->form_validation->set_message('validateGameEntryID', $Query->row()->RemainingPredicitons.' predictions is already remaining for entry '.$Query->row()->EntryNo.'. First please use remaining predicitons, then you can assign for new entry');
            return FALSE;
        }

        /* To Validate Game EntryID */
        $Query = $this->db->query('SELECT * FROM `tbl_users_game_entries` WHERE GameEntryID = '.$this->Post['GameEntryID'].' LIMIT 1');
        if($Query->num_rows() == 0){
            $this->form_validation->set_message('validateGameEntryID', 'Invalid GameEntryID !!');
            return FALSE;
        }
        return TRUE;
    }

}
