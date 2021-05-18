<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
      Description: 	Use to get settings key value.
     */

    function getSiteSettings($ConfigTypeGUID) {

        $this->db->select("ConfigTypeValue");
        $this->db->from('set_site_config');
        $this->db->where("ConfigTypeGUID", $ConfigTypeGUID);
        $this->db->where("StatusID", 2);
        $this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return = $Query->row_array();
            return $Return['ConfigTypeValue'];
        }
        return false;
    }

    /*
      Description: 	Use to get settings list.
     */

    function getSettings($UserID) {
        $this->db->select("*");
        $this->db->from('tbl_users_settings US');
        $this->db->where("US.UserID", $UserID);
        $this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return = $Query->row_array();
            unset($Return['UserID']);
            return $Return;
        }
        return false;
    }

    /*
      Description: 	Use to update user setting.
     */

    function updateSettings($UserID, $Input = array()) {
        /* add reward points history - starts */
        $UpdateData = array_filter(array(
            "PushNotification" => @$Input['PushNotification'],
            "PrivacyPhone" => @$Input['PrivacyPhone'],
            "PrivacyLocation" => @$Input['PrivacyLocation'],
            "PrivacyGroup" => @$Input['PrivacyGroup'],
            "PrivacyEvent" => @$Input['PrivacyEvent'],
            "PrivacySocial" => @$Input['PrivacySocial'],
            "GroupJoinRequest" => @$Input['GroupJoinRequest'],
            "CreateEvent" => @$Input['CreateEvent'],
            "UpcomingEvent" => @$Input['UpcomingEvent'],
            "YouAreBlocked" => @$Input['YouAreBlocked'],
            "PostMessageToGroup" => @$Input['PostMessageToGroup'],
            "PostMessageToEvent" => @$Input['PostMessageToEvent'],
            "BuzzYou" => @$Input['BuzzYou'],
            "GroupJoin" => @$Input['GroupJoin'],
            "EventJoin" => @$Input['EventJoin'],
        ));
        if (!empty($UpdateData)) {
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users_settings', $UpdateData);
        }
    }

}
