<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Utility_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
      Description: 	Use to get country list
     */

    function getCountries() {
        /* Define section  */
        $Return = array('Data' => array('Records' => array()));
        /* Define variables - ends */
        $Query = $this->db->query("SELECT CountryCode,CountryName,phonecode   FROM `set_location_country` ORDER BY CountryName ASC");
        if ($Query->num_rows() > 0) {
            $Return['Data']['Records'] = $Query->result_array();
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: Use to manage cron api logs
     */

    function insertCronAPILogs($CronID, $Response) {
        if (!CRON_SAVE_LOG) {
            return true;
        }
        $InsertData = array(
            'CronID' => $CronID,
            'Response' => @json_encode($Response, JSON_UNESCAPED_UNICODE)
        );
        $this->db->insert('log_cron_api', $InsertData);
    }

    /*
      Description: 	Use to get banner list
     */

    function bannerList($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        $this->db->select('BannerID,BannerPage');
        $this->db->from('banner');

        if(!empty($Where['BannerPage'])){
			$this->db->where("BannerPage",$Where['BannerPage']);
        }
        /* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
			$this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /*for pagination*/
		}else{
			$this->db->limit(1);
		}

        $Query = $this->db->get();	
       
        if($Query->num_rows()>0){
            foreach($Query->result_array() as $Record){
                $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaThumbURL, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL,M.MediaCaption,M.SortBy', array("SectionID" => 'Banner', 'OrderBy' => 'SortBy', 'Sequence' => "ASC",'EntityID' => $Record['BannerID']), FALSE);
                if ($MediaData) {
                    $Return = ($MediaData ? $MediaData : new StdClass());
                    if($Return)
                    {
                      $Record['MediaGUID'] = $Return['MediaGUID'];
                      $Record['MediaThumbURL'] = $Return['MediaThumbURL'];
                      $Record['MediaURL'] = $Return['MediaURL'];
                      $Record['MediaCaption'] = $Return['MediaCaption'];
                      $Record['SortBy'] = $Return['SortBy'];
                    }
                    $this->cache->memcached->save('Banners', $Return);
                   // return $Return;
                }
                if (!$multiRecords) {

                    return $Record;
                }
                $Records[] = $Record;
            }
            $Return['Data']['Records'] = $Records;
            return $Return;
            
            // if (empty($MediaData)) {
            //     $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaThumbURL, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL,M.MediaCaption,M.SortBy', array("SectionID" => 'Banner', 'OrderBy' => 'SortBy', 'Sequence' => "ASC",'EntityID' => $Query->row()->BannerID), TRUE);
            //     if ($MediaData) {
            //         $Return = ($MediaData ? $MediaData : new StdClass());
            //         $this->cache->memcached->save('Banners', $Return);
            //         return $Return;
            //     }
            //     return false;
            // }
        }

        return FALSE;
        //return $MediaData;
    }

    /*
      Description: 	Use to add ReferralCode
     */

    function generateReferralCode($UserID = '') {
        $ReferralCode = random_string('alnum', 6);
        $this->db->insert('tbl_referral_codes', array_filter(array('UserID' => $UserID, 'ReferralCode' => $ReferralCode)));
        return $ReferralCode;
    }

    /*
      Description: Use to manage cron logs
     */

    function insertCronLogs($CronType) {
        $InsertData = array(
            'CronType' => $CronType,
            'EntryDate' => date('Y-m-d H:i:s')
        );
        $this->db->insert('log_cron', $InsertData);
        return $this->db->insert_id();
    }

    /*
      Description: Use to manage cron logs
     */

    function updateCronLogs($CronID) {
        $UpdateData = array(
            'CompletionDate' => date('Y-m-d H:i:s'),
            'CronStatus' => 'Completed'
        );
        $this->db->where('CronID', $CronID);
        $this->db->limit(1);
        $this->db->update('log_cron', $UpdateData);
    }

    /*
      Description: Use to get site config.
     */

    function getConfigs($Where = array()) {
        $this->db->select('ConfigTypeGUID,ConfigTypeDescprition,ConfigTypeValue, (CASE WHEN StatusID = 2 THEN "Active" WHEN StatusID = 6 THEN "Inactive" ELSE "Unknown" END) AS Status');
        $this->db->from('set_site_config');
        if (!empty($Where['ConfigTypeGUID'])) {
            $this->db->where("ConfigTypeGUID", $Where['ConfigTypeGUID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("StatusID", $Where['StatusID']);
        }
        $this->db->order_by("Sort", 'ASC');
        $TempOBJ = clone $this->db;
        $TempQ = $TempOBJ->get();
        $Return['Data']['TotalRecords'] = $TempQ->num_rows();
        // $this->db->cache_on();
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return['Data']['Records'] = $Query->result_array();
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: Use to update config.
     */
    function updateConfig($ConfigTypeGUID, $Input = array()) {
        if (!empty($Input)) {

            /* Update Config */
            $UpdateData = array(
                'ConfigTypeValue' => $Input['ConfigTypeValue'],
                'StatusID' => $Input['StatusID']
            );
            $this->db->where('ConfigTypeGUID', $ConfigTypeGUID);
            $this->db->limit(1);
            $this->db->update('set_site_config', $UpdateData);
            // $this->db->cache_delete('admin', 'config'); //Delete Cache
        }
    }

    /*
      Description: use to delte Notification.
     */

    function deleteNotifications() {
        /* Update Config */
        $Date = date('Y-m-d');
        $NewDate = date('Y-m-d', strtotime($Date . ' - 30 day'));
        $Query = "DELETE FROM tbl_notifications WHERE DATE(EntryDate) <  '" . $NewDate . "' ";
        $this->db->query($Query);
    }

    /*
      Description : To add banner
     */

    function addBanner($UserID, $Input = array(), $StatusID) {
        $this->db->trans_start();
        $EntityGUID = get_guid();
        /* Add to entity table and get ID. */
        $BannerID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID" => 14, "UserID" => $UserID, "StatusID" => $StatusID));
        $this->db->insert('banner', array("BannerID"=>$BannerID,"BannerPage" => $Input['BannerPage']));

        $this->db->trans_complete($this->SessionUserID, array_merge($this->Post), $this->StatusID);
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        $this->cache->memcached->delete('Banners');
        return array('BannerID' => $BannerID, 'BannerGUID' => $EntityGUID);
    }

    /*
      Description: Use to send OTP on mobile
     */

    function sendMobileSMS($SMSArray) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://control.msg91.com/api/sendotp.php?authkey=" . MSG91_AUTH_KEY . "&sender=" . MSG91_SENDER_ID . "&mobile=" . $SMSArray['PhoneNumber'] . "&otp=" . $SMSArray['Text'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return false;
        } else {
            return true;
        }
    }

    /*
      Description: Use to send SMS on mobile
     */

    function sendSMS($SMSArray) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.msg91.com/api/sendhttp.php?route=4&sender=".MSG91_SENDER_ID."&mobiles=" . $SMSArray['PhoneNumber'] . "&authkey=" . MSG91_AUTH_KEY . "&message=" . $SMSArray['Text'] . "&country=91",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));

        $response = curl_exec($curl);
        // print_r($response);exit();

        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /*
      Description: Use to send Bulk SMS on mobile
     */

    function sendBulkSMS($SMSArray) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{  
                                    "sender"    : "'.MSG91_SENDER_ID.'",
                                    "route"     : "4",
                                    "country"   : "91",
                                    "sms"       : [ 
                                                        {
                                                            "message": "' . $SMSArray['Text'] . '",
                                                            "to": [' . $SMSArray['PhoneNumber'] . ']
                                                        }
                                                    ]
                                }',
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Host: api.msg91.com",
                "authkey: 273511AkeBsbH953x5e45055fP1",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /*
      Description: Use to send emails
     */

    function sendMails($MailArray) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://control.msg91.com/api/sendmail.php?body=" . $MailArray['emailMessage'] . "&subject=" . $MailArray['emailSubject'] . "&to=" . $MailArray['emailTo'] . "&from=" . MSG91_FROM_EMAIL . "&authkey=" . MSG91_AUTH_KEY,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /*
      Description: 	Use to get state list
     */

    function getStates($Where = array()) {

        /* Define section  */
        $Return = array('Data' => array('Records' => array()));
        /* Define variables - ends */

        $this->db->select('StateName,CountryCode');
        $this->db->from('set_location_state');
        if (!empty($Where['CountryCode'])) {
            $this->db->where("CountryCode", $Where['CountryCode']);
        }
        if (!empty($Where['Status'])) {
            $this->db->where("Status", $Where['Status']);
        }
        $this->db->order_by("StateName", 'ASC');

        $TempOBJ = clone $this->db;
        $TempQ = $TempOBJ->get();
        $Return['Data']['TotalRecords'] = $TempQ->num_rows();

        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return['Data']['Records'] = $Query->result_array();
            return $Return;
        }
        return FALSE;

        if ($Query->num_rows() > 0) {
            $Return['Data']['Records'] = $Query->result_array();
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: 	Use to get app version details
     */

    function getAppVersionDetails() {
        $Query = $this->db->query("SELECT ConfigTypeGUID,ConfigTypeDescprition,ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID IN ('AndroidAppFeatures','AndridAppUrl','AndroidAppVersion','IsAndroidAppUpdateMandatory')");
        if ($Query->num_rows() > 0) {
            $VersionData = array();
            foreach ($Query->result_array() as $Value) {
                $VersionData[$Value['ConfigTypeGUID']] = $Value['ConfigTypeValue'];
            }
            return $VersionData;
        }
        return FALSE;
    }


    function NotificationBroadcastScheduling(){
        $Data = $this->db->query("SELECT * FROM tbl_broadcast_scheduling Where IsSend = 'No' ");
        if($Data){
            foreach ($Data->result_array() as $key => $value) {
                $to_time = strtotime($value['Date']);

                $from_time = strtotime(date('Y-m-d H:i:s', strtotime('+5 hour +30 minutes', strtotime(date('Y-m-d H:i:s')))));
                $finalTime = round(($to_time - $from_time) / 60, 2);           
                if ($finalTime <= 30 && $finalTime >= 0) {

                    if($value['Push'] == 1){
                            pushNotificationAndroidBroadcast($value['Title'], $value['Text'],'', (!empty($value['Redirection'])) ? $value['Redirection'] : '');
                            pushNotificationIphoneBroadcast($value['Title'], $value['Text'],'', (!empty($value['Redirection'])) ? $value['Redirection'] : '');
                    }

                    if($value['Normal'] == 1){
                        $this->load->model('Users_model');
                        $UsersData = $this->Users_model->getUsers('
                        UserID, 
                        Username,   
                        Email,
                        PhoneNumber         
                        ', array('AdminUsers' => 'No', 'UserTypeID' => 2), TRUE, 1, 1000000);

                        if(!empty($UsersData)){
                            foreach ($UsersData['Data']['Records'] as $ValueData) {
                                $InsertData[] = array_filter(array(
                                    "NotificationPatternID" => 2,
                                    "UserID" => $this->SessionUserID,
                                    "ToUserID" => $ValueData['UserID'],
                                    "RefrenceID" => "",
                                    "NotificationText" => $value['Title'],
                                    "NotificationMessage" => $value['Message'],
                                    "MediaID" => "",
                                    "EntryDate" => date("Y-m-d H:i:s")
                                ));
                            }
                        }

                        if (!empty($InsertData)) {
                            $this->db->insert_batch('tbl_notifications', $InsertData);
                        }

                    }

                    $this->db->where('ID', $value['ID']);
                    $this->db->limit(1);
                    $this->db->update('tbl_broadcast_scheduling', array('IsSend' => "Yes"));
                }
            }   
        }
    }

}
