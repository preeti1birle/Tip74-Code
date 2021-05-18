<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}

	
    /*
      Description: To get all game entries packages list
     */
    function packagesList($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'NoOfEntries'     => 'E.NoOfEntries',
                'NoOfPrediction'  => 'E.NoOfPrediction',
                'EntriesAmount'   => 'E.EntriesAmount',
                'NoOfDoubleUps'   => 'E.NoOfDoubleUps',
                'CreatedDate'     => 'E.CreatedDate'
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('E.EntriesID');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_game_entries_packages E');
        if (!empty($Where['EntriesID'])) {
            $this->db->where("E.EntriesID", $Where['EntriesID']);
        }
        if (!empty($Where['NoOfEntries'])) {
            $this->db->where("E.NoOfEntries", $Where['NoOfEntries']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else {
            $this->db->order_by('E.NoOfEntries', 'ASC');
        }

        /* Total records count only if want to get multiple records */
        if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            if ($PageNo != 0) {
                $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
            }
        } else {
            $this->db->limit(1);
        }
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                if (in_array('PerDoubleUpPrice', $Params)) {
                    $Return['Data']['PerDoubleUpPrice'] = $this->db->query('SELECT `ConfigTypeValue` FROM `set_site_config` WHERE `ConfigTypeGUID` = "PerDoubleUpPrice" LIMIT 1')->row()->ConfigTypeValue;
                }
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('PerDoubleUpPrice', $Params)) {
                    $Record['PerDoubleUpPrice'] = $this->db->query('SELECT `ConfigTypeValue` FROM `set_site_config` WHERE `ConfigTypeGUID` = "PerDoubleUpPrice" LIMIT 1')->row()->ConfigTypeValue;
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description: To get all game entries purchase list
     */
    function purchaseEntriesLists($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 100){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'EntryNo' => 'E.EntryNo',
                'WeekGUID' => 'W.WeekGUID',
                'WeekCount' => 'W.WeekCount',
                'WeekStartDate' => 'W.WeekStartDate',
                'WeekEndDate' => 'W.WeekEndDate',
                'AllowedPredictions' => 'E.AllowedPredictions',
                'ConsumedPredictions' => 'E.ConsumedPredictions',
                'AllowedPurchaseDoubleUps' => 'E.AllowedPurchaseDoubleUps',
                'TotalPurchasedDoubleUps' => 'E.TotalPurchasedDoubleUps',
                'ConsumeDoubleUps'=> 'E.ConsumeDoubleUps',
                'ModifiedDate' => 'E.ModifiedDate'
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('E.GameEntryID');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_users_game_entries E');
        $this->db->join('football_sports_season_weeks W', 'E.WeekID = W.WeekID', 'left');
        if (!empty($Where['GameEntryID'])) {
            $this->db->where("E.GameEntryID", $Where['GameEntryID']);
        }
        if (!empty($Where['EntryNo'])) {
            $this->db->where("E.EntryNo", $Where['EntryNo']);
        }
        if (!empty($Where['SessionUserID'])) {
            $this->db->where("E.UserID", $Where['SessionUserID']);
        }
        if (!empty($Where['WeekID'])) {
            $this->db->where("W.WeekID", $Where['WeekID']);
        }
        if (!empty($Where['Filter']) && $Where['Filter'] == 'Assigned') {
            $this->db->where("E.WeekID IS NOT NULL", null, false);
        }
        if (!empty($Where['Filter']) && $Where['Filter'] == 'UnAssigned') {
            $this->db->where("E.WeekID IS NULL", null, false);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else {
            $this->db->order_by('E.EntryNo', 'ASC');
        }

        /* Total records count only if want to get multiple records */
        if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            if ($PageNo != 0) {
                $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
            }
        } else {
            $this->db->limit(1);
        }
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                return $Return;
            } else {
                return $Query->row_array();
            }
        }
        return FALSE;
    }

    /*
      Description: To purchase entries
     */
    function purchaseEntries($Input = array(), $SessionUserID){

        $this->db->trans_start();

        /* Add Transaction History */
        $InsertData = array(
            "Amount"          => $Input['EntriesAmount'],
            "WalletAmount"    => $Input['EntriesAmount'],
            "TransactionType" => 'Dr',
            "TransactionID"   => substr(hash('sha256', mt_rand() . microtime()), 0, 20),
            "Narration"       => 'Purchase Entries',
            "EntryDate"       => date("Y-m-d H:i:s")
        );
        $WalletID = $this->Users_model->addToWallet($InsertData, $SessionUserID, 5);
        if(!$WalletID){
            return FALSE;
        }

         /* Insert User Entries Details */
        $PurchaseData = array(
            "PurchasedEntries"     => $Input['NoOfEntries'],
            "PurchasedPredictions" => $Input['NoOfPrediction'],
            "PurchasedDoubleups"   => $Input['NoOfDoubleUps'],
            "UserID" => $SessionUserID,
            "PurchaseDate" => date('Y-m-d H:i:s')
        );
        $this->db->insert('tbl_users_entries', $PurchaseData);

        $PerEntryPrediciton = $Input['NoOfPrediction'] / $Input['NoOfEntries'];
        $PerEntryDoublUps   = $Input['NoOfDoubleUps'] / $Input['NoOfEntries'];
        $LastEntryNo = 0;

        $Query = $this->db->query('SELECT EntryNo FROM `tbl_users_game_entries` WHERE UserID = '.$SessionUserID.' ORDER BY `EntryNo` DESC LIMIT 1');
        if($Query->num_rows() > 0){
            $LastEntryNo = $Query->row()->EntryNo;
        }
        for ($i = 1; $i <= $Input['NoOfEntries']; $i++) { 
           $EntriesArr = array(
                            'UserID' => $SessionUserID,
                            'EntryNo' => $LastEntryNo + $i,
                            'AllowedPredictions' => $PerEntryPrediciton,
                            'AllowedPurchaseDoubleUps' => $PerEntryDoublUps,
                            'EntryDate' => date("Y-m-d H:i:s")
                        );
            if(isset($Input['PurchaseType']) && $Input['PurchaseType'] == 'Direct'){
                $EntriesArr['WeekID']   = @$Input['WeekID'];
            }
            $this->db->insert('tbl_users_game_entries', $EntriesArr);
        }
       // $this->db->insert_batch('tbl_users_game_entries', $EntriesArr);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        /* Add Notification */
        $this->Notification_model->addNotification('PurchaseEntries', 'Purchase Entries', $SessionUserID, $SessionUserID, '', $Input['NoOfEntries'].' Game entries successfully purchased, of '.DEFAULT_CURRENCY.' '.$Input['EntriesAmount']);
        return TRUE;
    }

    /*
      Description: To assign entries
     */
    function assignEntries($Input = array(), $SessionUserID){

        /* Update User Game Entries Details */
        $this->db->set('WeekID', $Input['WeekID']);
        $this->db->set('ModifiedDate',date('Y-m-d H:i:s'));
        $this->db->where('UserID', $SessionUserID);
        $this->db->where('GameEntryID', $Input['GameEntryID']);
        $this->db->limit(1);
        $this->db->update('tbl_users_game_entries');
        return TRUE;
    }

    /*
      Description: To purchase game packages
     */
    function purchasePackage($Input = array(), $SessionUserID){

    	$this->db->trans_start();
    	/* Add Transaction History */
        $InsertData = array(
            "Amount"          => $Input['TotalRequiredAmount'],
            "WalletAmount"    => $Input['TotalRequiredAmount'],
            "TransactionType" => 'Dr',
            "TransactionID"   => substr(hash('sha256', mt_rand() . microtime()), 0, 20),
            "Narration"       => 'Purchase Entries',
            "EntryDate"       => date("Y-m-d H:i:s")
        );
        $WalletID = $this->Users_model->addToWallet($InsertData, $SessionUserID, 5);
        if(!$WalletID){
        	return FALSE;
        }

        $this->db->select('UserID');
		$this->db->from('tbl_users_game_entries');
		$this->db->where(array('UserID'=> $SessionUserID,'WeekID' => $Input['WeekID']));
		$this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {

             /* Update User Game Entries Details */
            $this->db->set('PurchasedEntries', 'PurchasedEntries+' . $Input['NoOfEntries'], FALSE);
            $this->db->set('AllowedPredictions', 'AllowedPredictions+' . $Input['NoOfPrediction'], FALSE);
            $this->db->set('AllowedPurchaseDoubleUps', 'AllowedPurchaseDoubleUps+' . $Input['NoOfDoubleUps'], FALSE);
            if($Input['IsDoubleUps'] == 'Yes'){
                $this->db->set('TotalPurchasedDoubleUps', 'TotalPurchasedDoubleUps+' . $Input['NoOfDoubleUps'], FALSE);
            }
            $this->db->set('UpdatedDate',date('Y-m-d H:i:s'));
            $this->db->where('UserID', $SessionUserID);
            $this->db->where('WeekID', $Input['WeekID']);
            $this->db->limit(1);
            $this->db->update('tbl_users_game_entries');
        }else{
            /* Insert User Game Entries Details */
            $PurchaseData = array(
                "PurchasedEntries"   => @$Input['NoOfEntries'],
                "AllowedPredictions" => @$Input['NoOfPrediction'],
                "AllowedPurchaseDoubleUps"   => @$Input['NoOfDoubleUps'],
                "UserID" => $SessionUserID,
                "WeekID" => $Input['WeekID']
            );
            if($Input['IsDoubleUps'] == 'Yes'){
                $PurchaseData['TotalPurchasedDoubleUps']   = @$Input['NoOfDoubleUps'];
            }
            $this->db->insert('tbl_users_game_entries', $PurchaseData);

        }

    	$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        /* Add Notification */
        $this->Notification_model->addNotification('PurchaseEntries', 'Purchase Entries', $SessionUserID, $SessionUserID, '', $Input['NoOfEntries'].' Game entries successfully purchased, of '.DEFAULT_CURRENCY.' '.$Input['TotalRequiredAmount']);
        return $this->getUserEntriesBalance($SessionUserID,$Input['WeekID']);
    }

    /*
      Description: To purchase game double ups
     */
    function purchaseDoubleUps($Input = array(), $SessionUserID){
        $this->db->trans_start();

        /* Add Transaction History */
        $InsertData = array(
            "Amount"          => $Input['TotalRequiredAmount'],
            "WalletAmount"    => $Input['TotalRequiredAmount'],
            "TransactionType" => 'Dr',
            "TransactionID"   => substr(hash('sha256', mt_rand() . microtime()), 0, 20),
            "Narration"       => 'Purchase Double UP',
            "EntryDate"       => date("Y-m-d H:i:s")
        );
        $WalletID = $this->Users_model->addToWallet($InsertData, $SessionUserID, 5);
        if(!$WalletID){
            return FALSE;
        }

        $this->db->select('UserID');
		$this->db->from('tbl_users_game_entries');
		$this->db->where(array('UserID'=> $SessionUserID,'WeekID' => $Input['WeekID']));
		$this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {

             /* Update User Game Entries Details */
             $this->db->set('TotalPurchasedDoubleUps', 'TotalPurchasedDoubleUps+' . $Input['NoOfDoubleUps'], FALSE);
             $this->db->set('ModifiedDate',date('Y-m-d H:i:s'));
             $this->db->where('UserID', $SessionUserID);
             $this->db->where('WeekID', $Input['WeekID']);
             $this->db->where('GameEntryID', $Input['GameEntryID']);
             $this->db->limit(1);
             $this->db->update('tbl_users_game_entries');

        }else{

            /* Insert User Game Entries Details */
            $PurchaseData = array(
                "TotalPurchasedDoubleUps" => $Input['NoOfDoubleUps'],
                "UserID" => $SessionUserID,
                "WeekID" => $Input['WeekID']
            );
            $this->db->insert('tbl_users_game_entries', $PurchaseData);

        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        /* Add Notification */
        $this->Notification_model->addNotification('PurchaseDoubleUps', 'Purchase Double Ups', $SessionUserID, $SessionUserID, '', $Input['NoOfDoubleUps'].' Game double ups successfully purchased, of '.DEFAULT_CURRENCY.' '.$Input['TotalRequiredAmount']);
        return $this->getUserEntriesBalance($SessionUserID,$Input['WeekID']);
    }

    /*
      Description: To get user game entries details
     */
    function getUserEntriesBalance($UserID,$WeekID,$EntryNo="") {
        $Sql = 'SELECT EntryNo, AllowedPredictions,ConsumedPredictions,AllowedPurchaseDoubleUps,TotalPurchasedDoubleUps,(AllowedPurchaseDoubleUps-TotalPurchasedDoubleUps) RemainingPurchaseDoubleUps,ConsumeDoubleUps FROM `tbl_users_game_entries` WHERE `UserID` =' . $UserID . ' AND WeekID = '.$WeekID.'';
        if(!empty($EntryNo))
        {
          $Sql .= ' AND EntryNo = '.$EntryNo.'';
        }
          $Sql .= ' ORDER BY EntryNo DESC LIMIT 1';
        $Query = $this->db->query($Sql);
        if($Query->num_rows() > 0){
            return $Query->row();
        }else{
            return array('EntryNo' => 0,'AllowedPredictions' => 0,'ConsumedPredictions' => 0,'AllowedPurchaseDoubleUps' => 0,'TotalPurchasedDoubleUps' => 0,'RemainingPurchaseDoubleUps' => 0,'ConsumeDoubleUps' => 0);
        }
    }

    /*
      Description: To get user game purchased entries details
     */
    function getUserPurchasedEntriesBalance($UserID) {
        return array('PurchasedEntries' => 0,'AssignedEntries' => 0,'UnAssignedEntries' => 0,'PurchasedPredictions' => 0,'AssignedPredictions' => 0,'UnAssignedPredictions' => 0,'PurchasedDoubleups' => 0,'AssignedDoubleups' => 0,'UnAssignedDoubleups' => 0);

        $Query = $this->db->query('SELECT PurchasedEntries,AssignedEntries,(PurchasedEntries-AssignedEntries) UnAssignedEntries,PurchasedPredictions,AssignedPredictions,(PurchasedPredictions-AssignedPredictions) UnAssignedPredictions,PurchasedDoubleups,AssignedDoubleups,(PurchasedDoubleups-AssignedDoubleups) UnAssignedDoubleups FROM `tbl_users_entries` WHERE `UserID` =' . $UserID . ' LIMIT 1');
        if($Query->num_rows() > 0){
            return $Query->row_array();
        }else{
            return array('PurchasedEntries' => 0,'AssignedEntries' => 0,'UnAssignedEntries' => 0,'PurchasedPredictions' => 0,'AssignedPredictions' => 0,'UnAssignedPredictions' => 0,'PurchasedDoubleups' => 0,'AssignedDoubleups' => 0,'UnAssignedDoubleups' => 0);
        }
    }

    /*
      Description: ADD entries package
    */
    function addPackage($Input = array()) {

        /* Insert Data */
        $InsertData = array(
                        'NoOfEntries'    => $Input['NoOfEntries'],
                        'NoOfPrediction' => $Input['NoOfPrediction'],
                        'EntriesAmount'  => $Input['EntriesAmount'],
                        'NoOfDoubleUps'  => $Input['NoOfDoubleUps'],
                        'CreatedDate'    => date('Y-m-d H:i:s')
                    );
        $this->db->insert('tbl_game_entries_packages', $InsertData);
        if(!$this->db->insert_id()){
            return FALSE;
        }
        return TRUE;
    }

    /*
      Description: Edit entries package
    */
    function editPackage($Input = array()) {

        /* Update Data */
        $UpdateData = array(
                        'NoOfEntries'    => $Input['NoOfEntries'],
                        'NoOfPrediction' => $Input['NoOfPrediction'],
                        'EntriesAmount'  => $Input['EntriesAmount'],
                        'NoOfDoubleUps'  => $Input['NoOfDoubleUps'],
                        'UpdatedDate'    => date('Y-m-d H:i:s')
                    );
        $this->db->where('EntriesID', $Input['EntriesID']);
        $this->db->limit(1);
        $this->db->update('tbl_game_entries_packages', $UpdateData);
        return TRUE;
    }

    /*
      Description: Delete entries package to system.
    */
    function deletePackage($EntriesID) {
        $this->db->where('EntriesID', $EntriesID);
        $this->db->limit(1);
        $this->db->delete('tbl_game_entries_packages');
    }






}

