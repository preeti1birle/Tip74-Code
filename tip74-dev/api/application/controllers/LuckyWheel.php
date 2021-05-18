<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class LuckyWheel extends API_Controller_Secure {

    function __construct() {
        parent::__construct();
        $this->load->model('LuckyWheel_model');
    }

    public function index_post() {
        $points = $this->db->query("SELECT *, IF( Image IS NULL ,CONCAT('" . BASE_URL . "','uploads/luckywheel/','default.jpg'), CONCAT('" . BASE_URL . "','uploads/luckywheel/', Image)) as Image FROM tbl_lucky_wheel ORDER BY Points ASC ");
        if ($points->num_rows() > 0) {
            $this->Return['Data']['TotalRecords'] = $points->num_rows();
            $this->Return['Data']['Records'] = $points->result_array();
            $this->Return['Message'] = "Data found sucessfully.";
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Record not found.";
        }
    }

    public function getWheelPoints_post() {
        $points = $this->db->query("SELECT *, IF( Image IS NULL ,CONCAT('" . BASE_URL . "','uploads/luckywheel/','default.jpg'), CONCAT('" . BASE_URL . "','uploads/luckywheel/', Image)) as Image FROM tbl_lucky_wheel WHERE Status = 'Yes' ORDER BY Points ASC ");
        if ($points->num_rows() > 0) {

            /* Spin wheel valid or not  */
            $Query = $this->db->query("SELECT * FROM tbl_lucky_wheel_transaction WHERE UserID = '" . $this->SessionUserID . "' ORDER BY EntryDate DESC LIMIT 1");

            if ($Query->num_rows() > 0) {

                $WheelTime = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'LuckyWheelTime' AND StatusID = 2 ");
                if ($WheelTime->row()->ConfigTypeValue) {
                    $WheelTimel = $WheelTime->row()->ConfigTypeValue;
                } else {
                    $WheelTimel = 86400;
                }

                $Data = $Query->row();
                $LastSpinDate = strtotime($Data->EntryDate);
                $CurrentDateTime = strtotime(date('Y-m-d H:i:s'));
                if (($CurrentDateTime - $LastSpinDate) > $WheelTimel) { // 1 day check
                    $strips = array();
                    // admin wheel amount
                    $TotalWheelAmount = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'TotalWheelAmount' AND StatusID = 2 ")->row()->ConfigTypeValue;
                    if ($TotalWheelAmount) {
                        // Today total amount 
                        $TodayTotalAmoutn = $this->db->query("SELECT SUM(Value) as TodayTotalAmoutn FROM tbl_lucky_wheel_transaction WHERE DATE(EntryDate) = '" . date('Y-m-d') . "' ORDER BY EntryDate")->row()->TodayTotalAmoutn;

                        // checking if reaming amount is available
                        if ($TotalWheelAmount > $TodayTotalAmoutn) {

                            $remaining = $TotalWheelAmount - $TodayTotalAmoutn;

                            foreach ($points->result_array() as $key => $value) {
                                $strips[] = $value;
                                if ($remaining >= $value['Points']) { // chnage status if value is not available
                                    $strips[$key]['Pick'] = 'Yes';
                                } else {
                                    $strips[$key]['Pick'] = 'No';
                                }
                            }
                        } else {
                            foreach ($points->result_array() as $key => $value) {
                                $strips[] = $value;
                                if (($value['PointsID']) == 4) { // chnage status if value is not available
                                    $strips[$key]['Pick'] = 'Yes';
                                } else {
                                    $strips[$key]['Pick'] = 'No';
                                }
                            }
                        }
                    }
                    // print_r($strips);exit;

                    $this->Return['Data']['RenewTime'] = 0;
                    $this->Return['Data']['TotalRecords'] = $points->num_rows();
                    $this->Return['Data']['Records'] = (empty($strips) ? $points->result_array() : $strips);
                    $this->Return['Message'] = "Data found sucessfully.";
                    $this->Return['Data']['IsPlay'] = 'Yes';
                    $LuckyWheelActive = $this->db->query("SELECT ConfigTypeValue,StatusID FROM set_site_config WHERE ConfigTypeGUID = 'LuckyWheel'");
                    $this->Return['Data']['LuckyWheelActive'] = ($LuckyWheelActive->row()->StatusID == 2) ? "Yes" : "No";
                    $this->Return['Data']['LuckyWheelMessage'] = ($LuckyWheelActive->row()->StatusID == 6) ? "LuckyWheel Inactive" : "";
                    exit;
                }
                $this->Return['Data']['RenewTime'] = ($WheelTimel - ($CurrentDateTime - $LastSpinDate));
                $this->Return['Data']['TotalRecords'] = $points->num_rows();
                $this->Return['Data']['Records'] = $points->result_array();
                $this->Return['Data']['IsPlay'] = 'No';
                $LuckyWheelActive = $this->db->query("SELECT ConfigTypeValue,StatusID FROM set_site_config WHERE ConfigTypeGUID = 'LuckyWheel'");
                $this->Return['Data']['LuckyWheelActive'] = ($LuckyWheelActive->row()->StatusID == 2) ? "Yes" : "No";
                $this->Return['Data']['LuckyWheelMessage'] = ($LuckyWheelActive->row()->StatusID == 6) ? "LuckyWheel Inactive" : "";
                $this->Return['Message'] = "Data found sucessfully.";
            } else {

                $this->Return['Data']['TotalRecords'] = $points->num_rows();
                $this->Return['Data']['Records'] = $points->result_array();
                $this->Return['Data']['IsPlay'] = 'Yes';
                $LuckyWheelActive = $this->db->query("SELECT ConfigTypeValue,StatusID FROM set_site_config WHERE ConfigTypeGUID = 'LuckyWheel'");
                $this->Return['Data']['LuckyWheelActive'] = ($LuckyWheelActive->row()->StatusID == 2) ? "Yes" : "No";
                $this->Return['Data']['LuckyWheelMessage'] = ($LuckyWheelActive->row()->StatusID == 6) ? "LuckyWheel Inactive" : "";
                $this->Return['Message'] = "Data found sucessfully.";
            }
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Record not found.";
        }
    }

    public function addWheelPoints_post() {
        $this->form_validation->set_rules('Value', 'Value', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        $this->db->trans_start();

        $GUID = get_guid();

        $InsertData = array_filter(array(
            "LuckyWheelGUID" => $GUID,
            "UserID" => $this->SessionUserID,
            "Value" => $this->Post['Value'],
            "EntryDate" => date('Y-m-d H:i:s'),
        ));

        $this->db->insert('tbl_lucky_wheel_transaction', $InsertData);

        if ($this->Post['Value'] != "Better Luck Next Time") {

            $WalletData = array(
                "Amount" => $this->Post['Value'],
                "CashBonus" => $this->Post['Value'],
                "TransactionType" => 'Cr',
                "Narration" => 'Lucky Wheel',
                "EntryDate" => date("Y-m-d H:i:s")
            );

            $this->Users_model->addToWallet($WalletData, $this->SessionUserID, 5);

            $this->Return['ResponseCode'] = 200;
            $this->Return['Message'] = "Bonus added successfully.";
            $this->Return['Data'] = [];
        } else {
            $this->Return['ResponseCode'] = 200;
            $this->Return['Message'] = "Better Luck Next Time";
            $this->Return['Data'] = [];
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
    }

    public function getHistory_post() {
        $Query = $this->db->query("SELECT * FROM tbl_lucky_wheel_transaction WHERE UserID = '" . $this->SessionUserID . "' ORDER BY EntryDate DESC");
        if ($Query->num_rows() > 0) {

            $this->Return['Data']['TotalRecords'] = $Query->num_rows();
            $this->Return['Data']['Records'] = $Query->result_array();
            $this->Return['Message'] = "Data found sucessfully.";
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Data not found sucessfully.";
        }
    }

    public function getReport_post() {
        if (!empty($this->input->post('Date'))) {
            $date = "AND Date(EntryDate) = '" . $this->input->post('Date') . "'";
        } else {
            $date = '';
        }
        $Query = $this->db->query("SELECT Count(distinct(UserID)) as totalUser, sum(Value) as totalPoints FROM `tbl_lucky_wheel_transaction` WHERE Value != 'Better Luck Next Time' " . $date);
        if ($Query->num_rows() > 0) {


            $this->Return['Data']['Records'] = $Query->row_array();
            $this->Return['Message'] = "Data found sucessfully.";
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Data not found sucessfully.";
        }
    }

    public function getAll_post() {
        if(!empty($this->input->post('Date'))){
            $date = "AND Date(EntryDate) = '".$this->input->post('Date')."'";
        }else{
            $date = "AND Date(EntryDate) = '".date('Y-m-d')."'";
        } 
        $Query =  $this->db->query("SELECT SUM(Value) as total,Count(tbl_lucky_wheel_transaction.UserID) as TotalUser ,EntryDate,Value,tbl_users.FirstName FROM tbl_lucky_wheel_transaction left join tbl_users on tbl_lucky_wheel_transaction.UserID = tbl_users.UserID WHERE Value != 'Better Luck Next Time' ".$date." ORDER BY tbl_lucky_wheel_transaction.EntryDate DESC")->row();
        // print_r($Query);exit;
        $TotalWheelAmount = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'TotalWheelAmount' AND StatusID = 2 ")->row()->ConfigTypeValue;        
        if(!empty($Query)){
            $this->Return['Data']['OnDate'] =(!empty($this->input->post('Date')) ? $this->input->post('Date') : date('Y-m-d'));
            $this->Return['Data']['TotalUser'] = $Query->TotalUser;

            $this->Return['Data']['DistributedAmount'] =$Query->total;
            $this->Return['Data']['Remaining'] = $TotalWheelAmount - $Query->total;
            $this->Return['Data']['TotalDistributedAmount'] = $this->db->query("SELECT SUM(Value) as total FROM tbl_lucky_wheel_transaction WHERE Value != 'Better Luck Next Time'")->row()->total;     
            $this->Return['Message'] = "Data found sucessfully.";
        }else{
            $this->Return['ResponseCode'] 	= 500;
            $this->Return['Message'] = "Data not found sucessfully.";
        }
        // $this->form_validation->set_rules('DataFilter', 'Date Filter', 'trim');
        // $this->form_validation->set_rules('FromDate', 'FromDate', 'trim' . (!empty($this->Post['DataFilter']) && $this->Post['DataFilter'] == 'DateRange' ? '|required' : ''));
        // $this->form_validation->set_rules('ToDate', 'ToDate', 'trim' . (!empty($this->Post['DataFilter']) && $this->Post['DataFilter'] == 'DateRange' ? '|required' : ''));
        // $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        // $WinningUsersData = $this->LuckyWheel_model->getLuckyWheelReport($this->Post);
        // if (!empty($WinningUsersData)) {
        //     $this->Return['Data'] = $WinningUsersData;
        // }
    }

    public function getPointID_post() {
        $this->form_validation->set_rules('PointsID', 'PointsID', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */

        $points = $this->db->query("SELECT *, IF( Image IS NULL ,CONCAT('" . BASE_URL . "','uploads/luckywheel/','default.jpg'), CONCAT('" . BASE_URL . "','uploads/luckywheel/', Image)) as Image FROM tbl_lucky_wheel WHERE PointsID = '" . $this->Post['PointsID'] . "' ");
        if ($points->num_rows() > 0) {
            $this->Return['Data']['Records'] = $points->row();
            $this->Return['Message'] = "Data found sucessfully.";
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Record not found.";
        }
    }

    public function editPoints_post() {
        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
        $this->form_validation->set_rules('PointsID', 'PointsID', 'trim|required');
        $this->form_validation->set_rules('Points', 'Points', 'trim|required');
        $this->form_validation->set_rules('Pick', 'Pick', 'trim|required');
        $this->form_validation->set_rules('ColourCode', 'ColourCode', 'trim|required');
        $this->form_validation->set_rules('Status', 'Status', 'trim|required');

        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */
        $img = '';
        if (!empty($this->Post['MediaURL'])) {
            $img_path = explode('/', $this->Post['MediaURL']);
            $img = end($img_path);
        }

        $UpdateData = array_filter(array(
            'Points' => $this->Post['Points'],
            'Pick' => $this->Post['Pick'],
            'ColourCode' => $this->Post['ColourCode'],
            'Status' => $this->Post['Status'],
            'Image' => $img
        ));
        $this->db->where('PointsID', $this->Post['PointsID']);
        $this->db->limit(1);
        $this->db->update('tbl_lucky_wheel', $UpdateData);

        // $this->Category_model->editCategory($this->CategoryID, array('CategoryName'=>$this->Post['CategoryName'], 'StatusID'=>$this->StatusID));

        /* check for media present - associate media with this Post */
        // if(!empty($this->Post['MediaGUIDs'])){
        // 	$MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
        // 	foreach($MediaGUIDsArray as $MediaGUID){
        // 		$EntityData=$this->Entity_model->getEntity('E.EntityID MediaID',array('EntityGUID'=>$MediaGUID));
        //         print_r($EntityData);exit;
        //         if ($EntityData){
        // 			$this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $this->CategoryID);
        // 		}
        // 	}
        // }

        $this->Return['Message'] = "Points updated successfully.";
    }

    public function broadcast_post() {
        pushNotificationAndroidBroadcast("Spin Wheel", "Hey!!Its being a time you haven't try your luck by spinning the wheel ..spin the wheel and win bonus..If already avail the bonus please ignore");
        pushNotificationAndroidBroadcast("Spin Wheel", "Hey!!Its being a time you haven't try your luck by spinning the wheel ..spin the wheel and win bonus..If already avail the bonus please ignore");
        
    }

}
