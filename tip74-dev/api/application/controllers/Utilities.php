<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Utility_model');
        $this->load->model('Post_model');
        $this->load->model('Crons_model');
        $this->load->model('Football_model');
        $this->load->model('Page_model');
        $this->load->model('Users_model');
    }

    function syncmongousers_get() {
        mongoDBConnection();
        $Query = $this->db->query('SELECT *,IF(ProfilePic IS NULL,CONCAT("' . BASE_URL . '","uploads/profile/picture/","default.jpg"),CONCAT("' . BASE_URL . '","uploads/profile/picture/",ProfilePic)) AS ProfilePic FROM tbl_users');
        if($Query->num_rows() > 0){
            foreach($Query->result_array() as $User){
                $this->fantasydb->tbl_users->updateOne(
                    ['_id' => (int) $User['UserID']],
                    ['$set'   => array('UserGUID' => $User['UserGUID'],'FirstName' => $User['FirstName'],'LastName' => $User['LastName'],'Email' => $User['Email'],'Username' => $User['Username'],'ProfilePic' => $User['ProfilePic'])],
                    ['upsert' => true]
                );
            }
        }
    }

    /*
      Description: 	get site setting.
      URL: 			/api/utilities/setting/
     */

    function setting_get() {
        $ConfigData = $this->Utility_model->getConfigs(@$this->Post);
        if (!empty($ConfigData)) {
            $this->Return['Data'] = $ConfigData['Data'];
        }
    }

     /*
      Name : banner list
      Description : User to get banner list
      URL : /utilities/bannerList/
     */

    public function bannerList_post() {
      $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
      $this->form_validation->set_rules('BannerPage', 'Banner Page', 'trim');
      $this->form_validation->validation($this);  /* Run validation */

      $data = $this->Utility_model->bannerList('', array_merge($this->Post,array('StatusID' => $this->StatusID)), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($data) {
            $this->Return['Data'] = $data['Data'];
        } else {
            $this->Return['Data'] = new StdClass();
        }
    }

    /*
      Description: 	Use to send email to webadmin.
      URL: 			/api/utilities/contact/
     */

    public function contact_post() {
        /* Validation section */
        $this->form_validation->set_rules('Name', 'Name', 'trim');
        $this->form_validation->set_rules('Email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('PhoneNumber', 'PhoneNumber', 'trim');
        $this->form_validation->set_rules('Title', 'Title', 'trim');
        $this->form_validation->set_rules('Message', 'Message', 'trim|required');
        $this->form_validation->validation($this); /* Run validation */
        /* Validation - ends */
        send_mail(array(
            'emailTo' => SITE_CONTACT_EMAIL,
            'template_id' => 'd-30d87722d7fa42f8b0b671f6482a83f9',
            'Subject' => $this->Post['Name'] . ' filled out the contact form on ' . SITE_NAME,
            "Name" => $this->Post['Name'],
            'Email' => $this->Post['Email'],
            'PhoneNumber' => $this->Post['PhoneNumber'],
            'Title' => $this->Post['Title'],
            'Message' => $this->Post['Message']
        ));
    }

    /*
      Description:  Use execute cron jobs.
      URL:      /api/utilities/getCountries
     */

    public function getCountries_post() {
        $CountryData = $this->Utility_model->getCountries();
        if (!empty($CountryData)) {
            $this->Return['Data'] = $CountryData['Data'];
        }
    }

    public function getStates_post() {
        /* Validation section */
        $this->form_validation->set_rules('CountryCode', 'Country Code', 'trim|required');
        $this->form_validation->validation($this); /* Run validation */
        /* Validation - ends */

        $StateData = $this->Utility_model->getStates(array('CountryCode' => $this->Post['CountryCode'], 'Status' => 2));

        if (!empty($StateData)) {
            $this->Return['Data'] = $StateData['Data'];
        }
    }

    /*
    Name:           getPage
    URL:            /api/utilities/getPage
    Description:    Use to get page data.
    */
    public function getPage_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('PageGUID', 'PageGUID', 'trim');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');

        $this->form_validation->validation($this);  /* Run validation */        
        /* Validation - ends */

        $PageData = $this->Page_model->getPage('PageGUID,PageID,Title,Content',array("PageGUID" => @$this->Post['PageGUID'],'StatusID' => @$this->StatusID),TRUE,0);
        if($PageData){
            if (!empty($PageData['Content'])) {
                $PageData['Content'] = htmlentities($PageData['Content']);
            }
            $this->Return['Data'] = $PageData;
        }   
    }

    /*
      Description:    Use to get list of random posts.
      URL:            /api/utilities/getPosts
     */

    public function getPosts_post() {
        /* Validation section */
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */
        $Posts = $this->Post_model->getPosts('
            P.PostGUID,
            P.PostContent,
            P.PostCaption,
            P.Sort,
            ', array(), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($Posts) {
            $this->Return['Data'] = $Posts['Data'];
        }
    }

    public function sendAppLink_post() {
        $this->form_validation->set_rules('PhoneNumber', 'PhoneNumber', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */

        $this->Post['LogType'] = "Invite";
        $this->Users_model->addLoginLogs($this->Post);

        /** cpatcha validation check **/
        $this->Return['CaptchaEnable'] = "No";
        $this->Post['LogType'] = "Invite";
        $IsValidCaptcha=$this->Users_model->getLoginLogs($this->Post);
        if($IsValidCaptcha){
            $this->Return['CaptchaEnable'] = "Yes";
            $VerifyCaptcha= $this->Users_model->verifyCaptcha($this->Post,"Invalid request.");
            if($VerifyCaptcha['ResponseCode'] == 500){
                $this->Return['ResponseCode']   =   201;
                $this->Return['Message']        =   $VerifyCaptcha['Message'];
                exit;
            }
        }
        
        $this->Utility_model->sendSMS(array(
            'PhoneNumber' => $this->Post['PhoneNumber'],
            'Text' => "Here is the new " . SITE_NAME . " Android Application! Click on the link to download the App and Start Winning.".APP_LINK
        ));
        $this->Return['Message'] = "Link Sent successfully.";
    }

    /*
      Description:  Use to create pre draft contest
      URL:      /api/utilities/createPreContest
     */

    public function createPreContest_get() {
        // $this->Football_model->updateLeaderboards(8205);
    }

    /*
      Description: 	Cron jobs to get football seasons data.
      URL: 			/api/utilities/getSeasonsLiveFootball
     */

    public function getSeasonsLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getSeasonsLiveFootball');
        $this->Crons_model->getSeasonsLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to get football leagues data.
      URL:          /api/utilities/getLeaguesLiveFootball
     */

    public function getLeaguesLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getLeaguesLiveFootball');
        $this->Crons_model->getLeaguesLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to get football league rounds data.
      URL:          /api/utilities/getLeagueRoundsLiveFootball
     */

    public function getLeagueRoundsLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getLeagueRoundsLiveFootball');
        $this->Crons_model->getLeagueRoundsLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to manage football seasons custom weeks (Friday to Thursday).
      URL:          /api/utilities/getSeasonWeeksLiveFootball
     */

    public function getSeasonWeeksLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getSeasonWeeksLiveFootball');
        $this->Crons_model->getSeasonWeeksLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to get football venues (stadium) data.
      URL:          /api/utilities/getVenuesLiveFootball
     */

    public function getVenuesLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getVenuesLiveFootball');
        $this->Crons_model->getVenuesLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to get football teams data.
      URL:          /api/utilities/getTeamsLiveFootball
     */

    public function getTeamsLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getTeamsLiveFootball');
        $this->Crons_model->getTeamsLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to get football teams standings data.
      URL:          /api/utilities/getTeamsStandingsLiveFootball
     */

    public function getTeamsStandingsLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getTeamsStandingsLiveFootball');
        $this->Crons_model->getTeamsStandingsLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:  Cron jobs to get football fixtures/matches data.
      URL:          /api/utilities/getMatchesLiveFootball
     */
    public function getMatchesLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getMatchesLiveFootball');
        $this->Crons_model->getMatchesLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }


    /*
      Description:   Cron jobs to get football fixtures/matches live score data.
      URL:          /api/utilities/getMatchScoreLiveFootball
     */
    public function getMatchScoreLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getMatchScoreLiveFootball');
        $this->Crons_model->getMatchScoreLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:   Cron jobs to get football team lineup data.
      URL:          /api/utilities/getMatchTeamLineUpsLiveFootball
     */
    public function getMatchTeamLineUpsLiveFootball_get() {
        $CronID = $this->Utility_model->insertCronLogs('getMatchTeamLineUpsLiveFootball');
        $this->Crons_model->getMatchTeamLineUpsLive_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description:   Cron jobs to distribute winning data.
      URL:          /api/utilities/distributeWinning
     */
    public function distributeWinning_get() {
        $CronID = $this->Utility_model->insertCronLogs('distributeWinning');
        $this->Crons_model->distributeWinning_Football($CronID);
        $this->Utility_model->updateCronLogs($CronID);
        echo "Success";
    }

    /*
      Description: To get statics
    */
    public function dashboardStatics_post() {
        $SiteStatics = new stdClass();
        $SiteStatics = $this->db->query('SELECT
                                            TotalUnverifiedUsers,
                                            TotalWebUsers,
                                            TotalAndoridUsers,
                                            TotalIosUsers,
                                            TotalUsers,
                                            TotalDeposits,
                                            TotalWithdraw,
                                            TodayDeposit,
                                            NewUsers,
                                            TotalDeposits - TotalWithdraw AS TotalEarning,
                                            PendingWithdraw
                                        FROM
                                            (SELECT
                                              (
                                                    SELECT
                                                        COUNT(U.UserID) AS `TotalUsers`
                                                    FROM
                                                        `tbl_users` U,tbl_entity E
                                                    WHERE E.EntityID=U.UserID AND
                                                        U.`UserTypeID` = 2 AND E.StatusID = 1
                                                ) AS TotalUnverifiedUsers,
                                                (
                                                    SELECT
                                                        COUNT(U.UserID) AS `TotalUsers`
                                                    FROM
                                                        `tbl_users` U,tbl_entity E
                                                    WHERE E.EntityID=U.UserID AND
                                                        U.`UserTypeID` = 2 AND E.StatusID = 2
                                                ) AS TotalUsers,
                                                (
                                                    SELECT
                                                        COUNT(UserID) AS `NewUsers`
                                                    FROM
                                                        `tbl_users` U, `tbl_entity` E
                                                    WHERE
                                                        U.`UserTypeID` = 2 AND U.UserID = E.EntityID AND DATE(E.EntryDate) = "' . date('Y-m-d') . '"
                                                ) AS NewUsers,
                                                (
                                                    SELECT
                                                        IFNULL(SUM(`WalletAmount`),0) AS TotalDeposits
                                                    FROM
                                                        `tbl_users_wallet`
                                                    WHERE
                                                        `Narration`= "Deposit Money" AND
                                                        `StatusID` = 5
                                                ) AS TotalDeposits,
                                                (
                                                    SELECT
                                                        IFNULL(SUM(`WalletAmount`),0) AS TodayDeposit
                                                    FROM
                                                        `tbl_users_wallet`
                                                    WHERE
                                                        `Narration`= "Deposit Money" AND
                                                        `StatusID` = 5 AND DATE(EntryDate) = "' . date('Y-m-d') . '"
                                                ) AS TodayDeposit,
                                                (
                                                    SELECT
                                                        IFNULL(SUM(`Amount`),0) AS TotalWithdraw
                                                    FROM
                                                        `tbl_users_wallet`
                                                    WHERE
                                                        `StatusID` = 5 AND Narration = "Withdrawal Request"
                                                ) AS TotalWithdraw,
                                                (
                                                    SELECT
                                                        IFNULL(SUM(`Amount`),0) AS TotalWithdraw
                                                    FROM
                                                        `tbl_users_withdrawal`
                                                    WHERE
                                                        `StatusID` = 1
                                                ) AS PendingWithdraw,
                                                (
                                                    SELECT
                                                        IFNULL(Count(`UserID`),0) AS TotalWebUsers
                                                    FROM
                                                        `tbl_users`
                                                    WHERE
                                                        `LoginType` = "Web"
                                                ) AS TotalWebUsers,
                                                (
                                                    SELECT
                                                        IFNULL(Count(`UserID`),0) AS TotalAndoridUsers
                                                    FROM
                                                        `tbl_users`
                                                    WHERE
                                                        `LoginType` = "Andorid"
                                                ) AS TotalAndoridUsers,
                                                (
                                                    SELECT
                                                        IFNULL(Count(`UserID`),0) AS TotalIosUsers
                                                    FROM
                                                        `tbl_users`
                                                    WHERE
                                                        `LoginType` = "Ios"
                                                ) AS TotalIosUsers
                                            ) Total'
                )->row();
        $this->Return['Data'] = $SiteStatics;
    }

    /*
      Name:           getTotalDeposits
      Description:    To get Total Deposits data
      URL:            /Utilites/getTotalDeposits/
     */

    public function getTotalDeposits_post() {
        /* Get Total Deposit Data */
        $WalletDetails = $this->Utility_model->getTotalDeposit(@$this->Post['Params'], $this->Post, TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($WalletDetails)) {
            $this->Return['Data'] = $WalletDetails['Data'];
        }
    }

    /*
      Description:  Use to get app version details
      URL:      /api/utilities/getAppVersionDetails
     */

    public function getAppVersionDetails_post() {
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|callback_validateSession');
        $this->form_validation->set_rules('UserAppVersion', 'UserAppVersion', 'trim|required');
        $this->form_validation->set_rules('DeviceType', 'Device type', 'trim|required|callback_validateDeviceType');
        $this->form_validation->validation($this); /* Run validation */
        /* Validation - ends */

        $VersionData = $this->Utility_model->getAppVersionDetails();
        if (!empty($VersionData)) {
            $this->Return['Data'] = $VersionData;
        }
    }

    /*
      Description:  Use to get referel amount details.
      URL:      /api/utilities/getReferralDetails
     */

    public function getReferralDetails_post() {
        $ReferByQuery = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "ReferByDepositBonus" AND StatusID = 2 LIMIT 1');
        $ReferToQuery = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "ReferToDepositBonus" AND StatusID = 2 LIMIT 1');
        $this->Return['Data']['ReferByBonus'] = ($ReferByQuery->num_rows() > 0) ? $ReferByQuery->row()->ConfigTypeValue : 0;
        $this->Return['Data']['ReferToBonus'] = ($ReferToQuery->num_rows() > 0) ? $ReferToQuery->row()->ConfigTypeValue : 0;
    }

    /*
      Name : game entries packages list
      Description : User to get game entries packages list
      URL : /utilities/gameEntriesPackages/
    */
    public function gameEntriesPackages_post() {

        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
        $this->form_validation->set_rules('EntriesID', 'EntriesID', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $EntriesData = $this->Utility_model->gameEntriesPackages((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), $this->Post, (!empty($this->Post['EntriesID']) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($EntriesData) {
            $this->Return['Data'] = (!empty($this->Post['EntriesID'])) ? $EntriesData :  $EntriesData['Data'];
        }
    }

}
