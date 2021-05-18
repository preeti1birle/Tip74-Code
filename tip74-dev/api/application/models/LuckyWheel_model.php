<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class LuckyWheel_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    function getLuckyWheelReport($Where = array()) {
        $Return = array();
        $Return['FromTo'] = "";
            $this->db->select("T.Value,T.EntryDate,U.FirstName,U.Username");
            $this->db->from('tbl_lucky_wheel_transaction T,tbl_users U');
            $this->db->where("U.UserID", "T.UserID", FALSE);
            if (!empty($Where['DataFilter'])) {
                if ($Where['DataFilter'] == "Today") {
                    $this->db->where("DATE(T.EntryDate)", date('Y-m-d'));
                    $Return['FromTo'] = date('d-m-Y') . " To " . date('d-m-Y');
                } else if ($Where['DataFilter'] == "Last7Days") {
                    $this->db->where("DATE(T.EntryDate) <=", date('Y-m-d'));
                    $this->db->where("DATE(T.EntryDate) >=", date('Y-m-d', strtotime('-7 days')));
                    $Return['FromTo'] = date('d-m-Y') . " To " . date('d-m-Y', strtotime('-7 days'));
                } else if ($Where['DataFilter'] == "Last15Days") {
                    $this->db->where("DATE(T.EntryDate) <=", date('Y-m-d'));
                    $this->db->where("DATE(T.EntryDate) >=", date('Y-m-d', strtotime('-15 days')));
                    $Return['FromTo'] = date('d-m-Y') . " To " . date('d-m-Y', strtotime('-15 days'));
                } else if ($Where['DataFilter'] == "Last30Days") {
                    $this->db->where("DATE(T.EntryDate) <=", date('Y-m-d'));
                    $this->db->where("DATE(T.EntryDate) >=", date('Y-m-d', strtotime('-1 month')));
                    $Return['FromTo'] = date('d-m-Y') . " To " . date('d-m-Y', strtotime('-1 month'));
                } else if ($Where['DataFilter'] == "Last3Months") {
                    $this->db->where("DATE(T.EntryDate) <=", date('Y-m-d'));
                    $this->db->where("DATE(T.EntryDate) >=", date('Y-m-d', strtotime('-3 month')));
                    $Return['FromTo'] = date('d-m-Y') . " To " . date('d-m-Y', strtotime('-3 month'));
                } else {
                    $this->db->where("DATE(T.EntryDate) >=", $Where['FromDate']);
                    $this->db->where("DATE(T.EntryDate) <=", $Where['ToDate']);
                    $Return['FromTo'] = date('d-m-Y', strtotime($Where['FromDate'])) . " To " . date('d-m-Y', strtotime($Where['ToDate']));
                }
            } else {
                if (!empty($Where['FromDate']) && !empty($Where['ToDate'])) {
                    $this->db->where("DATE(T.EntryDate) >=", $Where['FromDate']);
                    $this->db->where("DATE(T.EntryDate) <=", $Where['ToDate']);
                    $Return['FromTo'] = date('d-m-Y', strtotime($Where['FromDate'])) . " To " . date('d-m-Y', strtotime($Where['ToDate']));
                }
            }
            $this->db->order_by("EntryDate", "DESC");
            $this->db->limit(500);
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                foreach ($Query->result_array() as $value) {
                    $Return['totalPoints']+= $value['Value'];
                };
                $Return['totalUser'] = $Query->num_rows();
                $Return['Records'] = $Query->result_array();
            }
        return $Return;
    }
}