<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->model('Utility_model');
        mongoDBConnection();
    }

    /*
      Description: 	Use to update user profile info.
     */

    function updateUserInfo($UserID, $Input = array()) {

        $UpdateArray = array_filter(array(
            "FirstName" => @ucfirst(strtolower($Input['FirstName'])),
            "MiddleName" => @ucfirst(strtolower($Input['MiddleName'])),
            "LastName" => @ucfirst(strtolower($Input['LastName'])),
            "About" => @$Input['About'],
            "About1" => @$Input['About1'],
            "About2" => @$Input['About2'],
            "ProfilePic" => @$Input['ProfilePic'],
            "ProfileCoverPic" => @$Input['ProfileCoverPic'],
            "Email" => @strtolower($Input['Email']),
            "Username" => @strtoupper($Input['Username']),
            "Gender" => @$Input['Gender'],
            "BirthDate" => @$Input['BirthDate'],
            "Age" => @$Input['Age'],
            "Height" => @$Input['Height'],
            "Weight" => @$Input['Weight'],
            "Address" => @$Input['Address'],
            "Address1" => @$Input['Address1'],
            "IBAN" => @$Input['IBAN'],
            "RoutingCode" => @$Input['RoutingCode'],
            "SwiftCode" => @$Input['SwiftCode'],
            "Postal" => @$Input['Postal'],
            "CountryCode" => @$Input['CountryCode'],
            "TimeZoneID" => @$Input['TimeZoneID'],
            "CityName" => @$Input['CityName'],
            "StateName" => @$Input['StateName'],
            "Latitude" => @$Input['Latitude'],
            "Longitude" => @$Input['Longitude'],
            "LanguageKnown" => @$Input['LanguageKnown'],
            "PhoneNumber" => @$Input['PhoneNumber'],
            "IsPrivacyNameDisplay" => @$Input['IsPrivacyNameDisplay'],
            "isWithdrawal" => @$Input['isWithdrawal'],
            "Website" => @strtolower($Input['Website']),
            "FacebookURL" => @strtolower($Input['FacebookURL']),
            "TwitterURL" => @strtolower($Input['TwitterURL']),
            "GoogleURL" => @strtolower($Input['GoogleURL']),
            "InstagramURL" => @strtolower($Input['InstagramURL']),
            "LinkedInURL" => @strtolower($Input['LinkedInURL']),
            "WhatsApp" => @strtolower($Input['WhatsApp']),
        ));

        if (isset($Input['LastName']) && $Input['LastName'] == '') {
            $UpdateArray['LastName'] = null;
        }
        if (isset($Input['Username']) && $Input['Username'] == '') {
            $UpdateArray['Username'] = null;
        }
        if (isset($Input['Gender']) && $Input['Gender'] == '') {
            $UpdateArray['Gender'] = null;
        }
        if (isset($Input['BirthDate']) && $Input['BirthDate'] == '') {
            $UpdateArray['BirthDate'] = null;
        }
        if (isset($Input['Address']) && $Input['Address'] == '') {
            $UpdateArray['Address'] = null;
        }
        if (isset($Input['Address1']) && $Input['Address1'] == '') {
            $UpdateArray['Address1'] = null;
        }
        if (isset($Input['IBAN']) && $Input['IBAN'] == '') {
            $UpdateArray['IBAN'] = null;
        }
        if (isset($Input['RoutingCode']) && $Input['RoutingCode'] == '') {
            $UpdateArray['RoutingCode'] = null;
        }
        if (isset($Input['SwiftCode']) && $Input['SwiftCode'] == '') {
            $UpdateArray['SwiftCode'] = null;
        }
        if (isset($Input['PhoneNumber']) && $Input['PhoneNumber'] == '') {
            $UpdateArray['PhoneNumber'] = null;
        }
        if (isset($Input['Website']) && $Input['Website'] == '') {
            $UpdateArray['Website'] = null;
        }
        if (isset($Input['FacebookURL']) && $Input['FacebookURL'] == '') {
            $UpdateArray['FacebookURL'] = null;
        }
        if (isset($Input['TwitterURL']) && $Input['TwitterURL'] == '') {
            $UpdateArray['TwitterURL'] = null;
        }
        if (isset($Input['PhoneNumber']) && $Input['PhoneNumber'] == '') {
            $UpdateArray['PhoneNumber'] = null;
        }


        /* for change email address */
        if (!empty($UpdateArray['Email']) || !empty($UpdateArray['PhoneNumber'])) {
            $UserData = $this->Users_model->getUsers('Email,FirstName,PhoneNumber', array('UserID' => $UserID));
        }

        /* for update email address */
        if (!empty($UpdateArray['Email'])) {
            if ($UserData['Email'] != $UpdateArray['Email']) {
                $UpdateArray['EmailForChange'] = $UpdateArray['Email'];
                /* Genrate a Token for Email verification and save to tokens table. */
                $this->load->model('Recovery_model');
                $Token = $this->Recovery_model->generateToken($UserID, 2);
                send_mail(array(
                    'emailTo' => $UpdateArray['EmailForChange'],
                    'template_id' => 'd-c9a4320dc3f740799d1d5861e032df59',
                    'Subject' => SITE_NAME . ", OTP for change of email address",
                    "Name" => $UserData['FirstName'],
                    'Token' => $Token
                ));
                unset($UpdateArray['Email']);
            }
        }


        /* for update phone number */
        if (!empty($UpdateArray['PhoneNumber']) && PHONE_NO_VERIFICATION && !isset($Input['SkipPhoneNoVerification'])) {
            if ($UserData['PhoneNumber'] != $UpdateArray['PhoneNumber']) {

                $UpdateArray['PhoneNumberForChange'] = $UpdateArray['PhoneNumber'];
                /* Genrate a Token for PhoneNumber verification and save to tokens table. */
                $this->load->model('Recovery_model');
                $Token = $this->Recovery_model->generateToken($UserID, 3);

                /* Send change phonenumber SMS to User with Token. */

                $this->Utility_model->sendMobileSMS(array(
                    'PhoneNumber' => $UpdateArray['PhoneNumberForChange'],
                    'Text' => $Token
                        // 'Text' => SITE_NAME . ", OTP to verify Mobile no. is: $Token",
                ));
                unset($UpdateArray['PhoneNumber']);
            }
        }

        /* Update User details to users table. */
        if (!empty($UpdateArray)) {
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users', $UpdateArray);
        }

        if (!empty($Input['InterestGUIDs'])) {
            /* Revoke categories - starts */
            $this->db->where(array("EntityID" => $UserID));
            $this->db->delete('tbl_entity_categories');
            /* Revoke categories - ends */

            /* Assign categories - starts */
            $this->load->model('Category_model');
            foreach ($Input['InterestGUIDs'] as $CategoryGUID) {
                $CategoryData = $this->Category_model->getCategories('CategoryID', array('CategoryGUID' => $CategoryGUID));
                if ($CategoryData) {
                    $InsertCategory[] = array('EntityID' => $UserID, 'CategoryID' => $CategoryData['CategoryID']);
                }
            }
            if (!empty($InsertCategory)) {
                $this->db->insert_batch('tbl_entity_categories', $InsertCategory);
            }
            /* Assign categories - ends */
        }


        if (!empty($Input['SpecialtyGUIDs'])) {
            /* Revoke categories - starts */
            $this->db->where(array("EntityID" => $UserID));
            $this->db->delete('tbl_entity_categories');
            /* Revoke categories - ends */

            /* Assign categories - starts */
            $this->load->model('Category_model');
            foreach ($Input['SpecialtyGUIDs'] as $CategoryGUID) {
                $CategoryData = $this->Category_model->getCategories('CategoryID', array('CategoryGUID' => $CategoryGUID));
                if ($CategoryData) {
                    $InsertCategory[] = array('EntityID' => $UserID, 'CategoryID' => $CategoryData['CategoryID']);
                }
            }
            if (!empty($InsertCategory)) {
                $this->db->insert_batch('tbl_entity_categories', $InsertCategory);
            }
            /* Assign categories - ends */
        }

        /* Edit Into MongoDB */
        $this->fantasydb->tbl_users->updateOne(
            ['_id'    => (int) $UserID],
            ['$set'   => array_filter(array('ModifiedDate' => date('Y-m-d H:i:s'),'UserName' => @$UpdateArray['UserName'],'FirstName' => @$UpdateArray['FirstName'], 'LastName' => @$UpdateArray['LastName'],'ProfilePic' => (!empty($UpdateArray['ProfilePic'])) ? (BASE_URL."uploads/profile/picture/".$UpdateArray['ProfilePic']) : ''))],
            ['upsert' => true]
        );

        $this->Entity_model->updateEntityInfo($UserID, array('StatusID' => @$Input['StatusID']));
        return TRUE;
    }

    /*
      Description: 	Use to update user profile info.
     */

    function updateUserInfoAdminAccess($UserID, $Input = array()) {

        $UpdateArray = array_filter(array(
            "UserTypeID" => @$Input['UserTypeID'],
            "FirstName" => @ucfirst(strtolower($Input['FirstName'])),
            "MiddleName" => @ucfirst(strtolower($Input['MiddleName'])),
            "LastName" => @ucfirst(strtolower($Input['LastName'])),
            "About" => @$Input['About'],
            "About1" => @$Input['About1'],
            "About2" => @$Input['About2'],
            "ProfilePic" => @$Input['ProfilePic'],
            "ProfileCoverPic" => @$Input['ProfileCoverPic'],
            "Email" => @strtolower($Input['Email']),
            "Username" => @strtoupper($Input['Username']),
            "Gender" => @$Input['Gender'],
            "BirthDate" => @$Input['BirthDate'],
            "Age" => @$Input['Age'],
            "Height" => @$Input['Height'],
            "Weight" => @$Input['Weight'],
            "Address" => @$Input['Address'],
            "Address1" => @$Input['Address1'],
            "Postal" => @$Input['Postal'],
            "CountryCode" => @$Input['CountryCode'],
            "TimeZoneID" => @$Input['TimeZoneID'],
            "CityName" => @$Input['CityName'],
            "StateName" => @$Input['StateName'],
            "Latitude" => @$Input['Latitude'],
            "Longitude" => @$Input['Longitude'],
            "LanguageKnown" => @$Input['LanguageKnown'],
            "PhoneNumber" => @$Input['PhoneNumber'],
            "IsPrivacyNameDisplay" => @$Input['IsPrivacyNameDisplay'],
            "isWithdrawal" => @$Input['isWithdrawal'],
            "Website" => @strtolower($Input['Website']),
            "FacebookURL" => @strtolower($Input['FacebookURL']),
            "TwitterURL" => @strtolower($Input['TwitterURL']),
            "GoogleURL" => @strtolower($Input['GoogleURL']),
            "InstagramURL" => @strtolower($Input['InstagramURL']),
            "LinkedInURL" => @strtolower($Input['LinkedInURL']),
            "WhatsApp" => @strtolower($Input['WhatsApp']),
        ));

        if (isset($Input['LastName']) && $Input['LastName'] == '') {
            $UpdateArray['LastName'] = null;
        }
        if (isset($Input['Username']) && $Input['Username'] == '') {
            $UpdateArray['Username'] = null;
        }
        if (isset($Input['Gender']) && $Input['Gender'] == '') {
            $UpdateArray['Gender'] = null;
        }
        if (isset($Input['BirthDate']) && $Input['BirthDate'] == '') {
            $UpdateArray['BirthDate'] = null;
        }
        if (isset($Input['Address']) && $Input['Address'] == '') {
            $UpdateArray['Address'] = null;
        }
        if (isset($Input['PhoneNumber']) && $Input['PhoneNumber'] == '') {
            $UpdateArray['PhoneNumber'] = null;
        }
        if (isset($Input['Website']) && $Input['Website'] == '') {
            $UpdateArray['Website'] = null;
        }
        if (isset($Input['FacebookURL']) && $Input['FacebookURL'] == '') {
            $UpdateArray['FacebookURL'] = null;
        }
        if (isset($Input['TwitterURL']) && $Input['TwitterURL'] == '') {
            $UpdateArray['TwitterURL'] = null;
        }
        if (isset($Input['PhoneNumber']) && $Input['PhoneNumber'] == '') {
            $UpdateArray['PhoneNumber'] = null;
        }


        /* for change email address */
        if (!empty($UpdateArray['Email']) || !empty($UpdateArray['PhoneNumber'])) {
            $UserData = $this->Users_model->getUsers('Email,FirstName,PhoneNumber', array('UserID' => $UserID));
        }

        /* for update email address */
        if (!empty($UpdateArray['Email'])) {
            if ($UserData['Email'] != $UpdateArray['Email']) {
                $UpdateArray['EmailForChange'] = $UpdateArray['Email'];
                /* Genrate a Token for Email verification and save to tokens table. */
                $this->load->model('Recovery_model');
                $Token = $this->Recovery_model->generateToken($UserID, 2);
                /* Send welcome Email to User with Token. */
                sendMail(array(
                    'emailTo' => $UpdateArray['EmailForChange'],
                    'emailSubject' => SITE_NAME . ", OTP for change of email address.",
                    'emailMessage' => emailTemplate($this->load->view('emailer/change_email', array("Name" => $UserData['FirstName'], 'Token' => $Token), TRUE))
                ));
                // send_mail(array(
                //     'emailTo' => $UpdateArray['EmailForChange'],
                //     'template_id' => 'd-c9a4320dc3f740799d1d5861e032df59',
                //     'Subject' => SITE_NAME . ", OTP for change of email address",
                //     "Name" => $UserData['FirstName'],
                //     'Token' => $Token
                // ));
                unset($UpdateArray['Email']);
            }
        }


        /* for update phone number */
        if (!empty($UpdateArray['PhoneNumber']) && PHONE_NO_VERIFICATION && !isset($Input['SkipPhoneNoVerification'])) {
            if ($UserData['PhoneNumber'] != $UpdateArray['PhoneNumber']) {

                $UpdateArray['PhoneNumberForChange'] = $UpdateArray['PhoneNumber'];
                /* Genrate a Token for PhoneNumber verification and save to tokens table. */
                $this->load->model('Recovery_model');
                $Token = $this->Recovery_model->generateToken($UserID, 3);

                /* Send change phonenumber SMS to User with Token. */

                $this->Utility_model->sendMobileSMS(array(
                    'PhoneNumber' => $UpdateArray['PhoneNumberForChange'],
                    'Text' => $Token
                ));
                unset($UpdateArray['PhoneNumber']);
            }
        }
        /* Update User details to users table. */
        if (!empty($UpdateArray)) {
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users', $UpdateArray);
        }

        if (!empty($Input['InterestGUIDs'])) {
            /* Revoke categories - starts */
            $this->db->where(array("EntityID" => $UserID));
            $this->db->delete('tbl_entity_categories');
            /* Revoke categories - ends */

            /* Assign categories - starts */
            $this->load->model('Category_model');
            foreach ($Input['InterestGUIDs'] as $CategoryGUID) {
                $CategoryData = $this->Category_model->getCategories('CategoryID', array('CategoryGUID' => $CategoryGUID));
                if ($CategoryData) {
                    $InsertCategory[] = array('EntityID' => $UserID, 'CategoryID' => $CategoryData['CategoryID']);
                }
            }
            if (!empty($InsertCategory)) {
                $this->db->insert_batch('tbl_entity_categories', $InsertCategory);
            }
            /* Assign categories - ends */
        }


        if (!empty($Input['SpecialtyGUIDs'])) {
            /* Revoke categories - starts */
            $this->db->where(array("EntityID" => $UserID));
            $this->db->delete('tbl_entity_categories');
            /* Revoke categories - ends */

            /* Assign categories - starts */
            $this->load->model('Category_model');
            foreach ($Input['SpecialtyGUIDs'] as $CategoryGUID) {
                $CategoryData = $this->Category_model->getCategories('CategoryID', array('CategoryGUID' => $CategoryGUID));
                if ($CategoryData) {
                    $InsertCategory[] = array('EntityID' => $UserID, 'CategoryID' => $CategoryData['CategoryID']);
                }
            }
            if (!empty($InsertCategory)) {
                $this->db->insert_batch('tbl_entity_categories', $InsertCategory);
            }
            /* Assign categories - ends */
        }



        $this->Entity_model->updateEntityInfo($UserID, array('StatusID' => @$Input['StatusID']));
        return TRUE;
    }

    /*
      Description: 	Use to set user new password.
     */

    function updateUserLoginInfo($UserID, $Input = array(), $SourceID) {
        $UpdateArray = array_filter(array(
            "Password" => (!empty($Input['Password']) ? md5($Input['Password']) : ''),
            "ModifiedDate	" => (!empty($Input['Password']) ? date("Y-m-d H:i:s") : ''),
            "LastLoginDate" => @$Input['LastLoginDate']
        ));

        /* Update User Login details */
        $this->db->where('UserID', $UserID);
        $this->db->where('SourceID', $SourceID);
        $this->db->limit(1);
        $this->db->update('tbl_users_login', $UpdateArray);

        if (!empty($Input['Password'])) {
            /* Send Password Assistance Email to User with Token (If user is not Pending or Email-Confirmed then email send without Token). */
            $UserData = $this->Users_model->getUsers('FirstName,Email', array('UserID' => $UserID));
            // $SendMail = sendMail(array(
            //     'emailTo' => $UserData['Email'],
            //     'emailSubject' => SITE_NAME . " Password Assistance",
            //     'emailMessage' => emailTemplate($this->load->view('emailer/change_password', array("Name" => $UserData['FirstName']), TRUE))
            // ));

            $SendMail = send_mail(array(
                'emailTo' => $UserData['Email'],
                'template_id' => CHANGE_PASSWORD,
                'Subject' => SITE_NAME . " Password Assistance",
                "Name" => $UserData['FirstName']
            ));
        }
        return TRUE;
    }

        /*
      Description:  Use to update user profile info.
     */

    function updateUserInfoPhone($UserID, $Input = array()) {

        $UpdateArray = array_filter(array(
            "PhoneNumber" => @$Input['PhoneNumber']
        ));

        /* for change email address */
        if (!empty($UpdateArray['PhoneNumber'])) {
            $UserData = $this->Users_model->getUsers('Email,FirstName,PhoneNumber', array('UserID' => $UserID));
        }

        /* for update phone number */
        if (!empty($UpdateArray['PhoneNumber']) && PHONE_NO_VERIFICATION && !isset($Input['SkipPhoneNoVerification'])) {
            if ($UserData['PhoneNumber'] != $UpdateArray['PhoneNumber']) {

                $UpdateArray['PhoneNumberForChange'] = $UpdateArray['PhoneNumber'];
                $this->load->model('Recovery_model');
                $Token = $this->Recovery_model->generateToken($UserID, 3);
                /* Send change phonenumber SMS to User with Token. */
                $this->Utility_model->sendMobileSMS(array(
                    'PhoneNumber' => $UpdateArray['PhoneNumberForChange'],
                    'Text' => $Token
                        // 'Text' => SITE_NAME . ", OTP to verify Mobile no. is: $Token",
                ));
                unset($UpdateArray['PhoneNumber']);
            }
        }
        /* Update User details to users table. */
        if (!empty($UpdateArray)) {
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users', $UpdateArray);
        }
        return TRUE;
    }


    /*
      Description: 	ADD user to system.
      Procedures:
      1. Add user to user table and get UserID.
      2. Save login info to users_login table.
      3. Save User details to users_profile table.
      4. Genrate a Token for Email verification and save to tokens table.
      5. Send welcome Email to User with Token.
     */

    function addUser($Input = array(), $UserTypeID, $SourceID, $StatusID = 1) {
        $this->db->trans_start();
        $EntityGUID = get_guid();

        /* Add user to entity table and get EntityID. */
        $EntityID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID" => 1, "StatusID" => $StatusID));
        /* Add user to user table . */
        if (!empty($Input['PhoneNumber']) && PHONE_NO_VERIFICATION) {
            $Input['PhoneNumberForChange'] = $Input['PhoneNumber'];
            unset($Input['PhoneNumber']);
        }
        $InsertData = array_filter(array(
            "UserID" => $EntityID,
            "UserGUID" => $EntityGUID,
            "UserTypeID" => $UserTypeID,
            "StoreID" => @$Input['StoreID'],
            "FirstName" => @ucfirst(strtolower($Input['FirstName'])),
            "MiddleName" => @ucfirst(strtolower($Input['MiddleName'])),
            "LastName" => @ucfirst(strtolower($Input['LastName'])),
            "About" => @$Input['About'],
            "ProfilePic" => @$Input['ProfilePic'],
            "ProfileCoverPic" => @$Input['ProfileCoverPic'],
            "Email" => ($SourceID != 1) ? @strtolower($Input['Email']) : '',
            "EmailForChange" => ($SourceID == 1) ? @strtolower($Input['Email']) : '',
            "Username" => @strtolower($Input['Username']),
            "Gender" => @$Input['Gender'],
            "BirthDate" => @$Input['BirthDate'],
            "Address" => @$Input['Address'],
            "Address1" => @$Input['Address1'],
            "Postal" => @$Input['Postal'],
            "CountryCode" => @$Input['CountryCode'],
            "TimeZoneID" => @$Input['TimeZoneID'],
            "Latitude" => @$Input['Latitude'],
            "Longitude" => @$Input['Longitude'],
            "PhoneCode" => @$Input['PhoneCode'],
            "PhoneNumber" => @$Input['PhoneNumber'],
            "LoginType" => @$Input['LoginType'],
            "PhoneNumberForChange" => @$Input['PhoneNumberForChange'],
            "Website" => @strtolower($Input['Website']),
            "FacebookURL" => @strtolower($Input['FacebookURL']),
            "TwitterURL" => @strtolower($Input['TwitterURL']),
            "ReferredByUserID" => @$Input['Referral']->UserID,
        ));
        $this->db->insert('tbl_users', $InsertData);

        /* Manage Singup Bonus */
        // $BonusData = $this->db->query('SELECT ConfigTypeValue,StatusID FROM set_site_config WHERE ConfigTypeGUID = "SignupBonus" LIMIT 1');
        // if ($BonusData->row()->StatusID == 2) {
        //     $WalletData = array(
        //         "Amount" => $BonusData->row()->ConfigTypeValue,
        //         "CashBonus" => $BonusData->row()->ConfigTypeValue,
        //         "TransactionType" => 'Cr',
        //         "Narration" => 'Signup Bonus',
        //         "EntryDate" => date("Y-m-d H:i:s")
        //     );
        //     $this->addToWallet($WalletData, $EntityID, 5);
        //     $this->Notification_model->addNotification('bonus', 'Signup Bonus', $EntityID, $EntityID, '', '' . DEFAULT_CURRENCY . $BonusData->row()->ConfigTypeValue . ' has been credited in your bonus Wallet');
        // }

        /* Save login info to users_login table. */
        $LoginData = array_filter(array(
            "UserID" => $EntityID,
            "Password" => md5(($SourceID == '1' ? $Input['Password'] : $Input['SourceGUID'])),
            "SourceID" => $SourceID,
            "EntryDate" => date("Y-m-d H:i:s")));
        $this->db->insert('tbl_users_login', $LoginData);

        /* save user settings */
        $this->db->insert('tbl_users_settings', array("UserID" => $EntityID));

        /* Insert Default Entries */
       // $this->db->insert('tbl_users_game_entries', array("UserID" => $EntityID));

        /* Insert Into MongoDB */
        $this->fantasydb->tbl_users->insertOne(array(
                '_id'        => (int) $EntityID,
                'UserGUID'   => $EntityGUID,
                'Email'      => @$InsertData['Email'],
                'Username'   => @$InsertData['Username'],
                'FirstName'  => @$InsertData['FirstName'],
                'LastName'   => @$InsertData['LastName'],
                'ProfilePic' => (!empty($InsertData['ProfilePic'])) ? (BASE_URL. "uploads/profile/picture/" .$InsertData['ProfilePic']) : (BASE_URL. "uploads/profile/picture/default.jpg")
            ));

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $EntityID;
    }

    /*
      Description: 	Use to get single user info or list of users.
      Note:			$Field should be comma seprated and as per selected tables alias.
     */

    function getUsers($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        /* Additional fields to select */
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'RegisteredOn' => 'DATE_FORMAT(E.EntryDate, "' . DATE_FORMAT . '") RegisteredOn',
                'LastLoginDate' => 'DATE_FORMAT(UL.LastLoginDate, "' . DATE_FORMAT . '") LastLoginDate',
                'Rating' => 'E.Rating',
                'UserTypeName' => 'UT.UserTypeName',
                'IsAdmin' => 'UT.IsAdmin',
                'UserID' => 'U.UserID',
                'UserTypeID' => 'U.UserTypeID',
                'FirstName' => 'U.FirstName',
                'MiddleName' => 'U.MiddleName',
                'LastName' => 'U.LastName',
                'ProfilePic' => 'IF(U.ProfilePic IS NULL,CONCAT("' . BASE_URL . '","uploads/profile/picture/","default.jpg"),CONCAT("' . BASE_URL . '","uploads/profile/picture/",U.ProfilePic)) AS ProfilePic',
                'ProfileCoverPic' => 'IF(U.ProfilePic IS NULL,CONCAT("' . BASE_URL . '","uploads/profile/cover/","default.jpg"),CONCAT("' . BASE_URL . '","uploads/profile/picture/",U.ProfileCoverPic)) AS ProfileCoverPic',
                'About' => 'U.About',
                'About1' => 'U.About1',
                'About2' => 'U.About2',
                'Email' => 'U.Email',
                'EmailForChange' => 'U.EmailForChange',
                'Username' => 'U.Username',
                'Gender' => 'U.Gender',
                'BirthDate' => 'U.BirthDate',
                'Address' => 'U.Address',
                'Address1' => 'U.Address1',
                'RoutingCode' => 'U.RoutingCode',
                'IBAN'        => 'U.IBAN',
                'SwiftCode'   => 'U.SwiftCode',
                'Postal' => 'U.Postal',
                'CountryCode' => 'U.CountryCode',
                'CountryName' => 'CO.CountryName',
                'CityName' => 'U.CityName',
                'StateName' => 'U.StateName',
                'PhoneCode'   => 'U.PhoneCode',
                'PhoneNumber' => 'U.PhoneNumber',
                'Email' => 'U.Email',
                'PhoneNumberForChange' => 'U.PhoneNumberForChange',
                'Website' => 'U.Website',
                'FacebookURL' => 'U.FacebookURL',
                'TwitterURL' => 'U.TwitterURL',
                'GoogleURL' => 'U.GoogleURL',
                'InstagramURL' => 'U.InstagramURL',
                'LinkedInURL' => 'U.LinkedInURL',
                'WhatsApp' => 'U.WhatsApp',
                'WalletAmount' => 'U.WalletAmount',
                'WinningAmount' => 'U.WinningAmount',
                'isWithdrawal' => 'U.isWithdrawal',
                'CashBonus' => 'U.CashBonus',
                'TotalCash' => '(U.WalletAmount + U.WinningAmount + U.CashBonus) AS TotalCash',
                'ReferralCode' => '(SELECT ReferralCode FROM tbl_referral_codes WHERE tbl_referral_codes.UserID=U.UserID LIMIT 1) AS ReferralCode',
                'ReferredByUserID' => 'U.ReferredByUserID',
                'ModifiedDate' => 'E.ModifiedDate',
                'Status' => 'CASE E.StatusID
												when "1" then "Pending"
												when "2" then "Verified"
												when "3" then "Deleted"
												when "4" then "Blocked"
												when "8" then "Hidden"		
											END as Status',
                'ReferredCount' => '(SELECT COUNT(*) FROM `tbl_users` WHERE `ReferredByUserID` = U.UserID) AS ReferredCount',
                'StatusID' => 'E.StatusID',
                'IsPrivacyNameDisplay' => 'U.IsPrivacyNameDisplay',
                'PushNotification' => 'US.PushNotification',
                'PhoneStatus' => 'if(U.PhoneNumber is null, "Pending", "Verified") as PhoneStatus',
                'EmailStatus' => 'if(U.Email is null, "Pending", "Verified") as EmailStatus',
                'WeekID' => '(SELECT WeekID FROM football_sports_season_weeks WK,tbl_entity E WHERE E.EntityID=WK.WeekID AND E.StatusID = 2 LIMIT 1) AS WeekID',
            );
            foreach ($Params as $Param) {
                $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
            }
        }
        $this->db->select('U.UserGUID, U.UserID,  CONCAT_WS(" ",U.FirstName,U.LastName) FullName');
        if (!empty($Field)) $this->db->select($Field, FALSE);


        /* distance calculation - starts */
        /* this is called Haversine formula and the constant 6371 is used to get distance in KM, while 3959 is used to get distance in miles. */
        if (!empty($Where['Latitude']) && !empty($Where['Longitude'])) {
            $this->db->select("(3959*acos(cos(radians(" . $Where['Latitude'] . "))*cos(radians(E.Latitude))*cos(radians(E.Longitude)-radians(" . $Where['Longitude'] . "))+sin(radians(" . $Where['Latitude'] . "))*sin(radians(E.Latitude)))) AS Distance", false);
            $this->db->order_by('Distance', 'ASC');

            if (!empty($Where['Radius'])) {
                $this->db->having("Distance <= " . $Where['Radius'], null, false);
            }
        }
        /* distance calculation - ends */

        $this->db->from('tbl_entity E');
        $this->db->from('tbl_users U');
        $this->db->where("U.UserID", "E.EntityID", FALSE);

        if (array_keys_exist($Params, array('UserTypeName', 'IsAdmin')) || !empty($Where['IsAdmin'])) {
            $this->db->from('tbl_users_type UT');
            $this->db->where("UT.UserTypeID", "U.UserTypeID", FALSE);
        }
        $this->db->join('tbl_users_login UL', 'U.UserID = UL.UserID', 'left');
        $this->db->join('tbl_users_settings US', 'U.UserID = US.UserID', 'left');

        if (array_keys_exist($Params, array('CountryName'))) {
            $this->db->join('set_location_country CO', 'U.CountryCode = CO.CountryCode', 'left');
        }

        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("U.FirstName", $Where['Keyword']);
            $this->db->or_like("U.LastName", $Where['Keyword']);
            $this->db->or_like("U.Username", $Where['Keyword']);
            $this->db->or_like("U.Email", $Where['Keyword']);
            $this->db->or_like("U.EmailForChange", $Where['Keyword']);
            $this->db->or_like("U.PhoneNumber", $Where['Keyword']);
            $this->db->or_like("U.PhoneNumberForChange", $Where['Keyword']);
            $this->db->or_like("CONCAT_WS('',U.FirstName,U.Middlename,U.LastName)", preg_replace('/\s+/', '', $Where['Keyword']), FALSE);
            $this->db->group_end();
        }

        if (!empty($Where['SourceID'])) {
            $this->db->where("UL.SourceID", $Where['SourceID']);
        }

        if (!empty($Where['UserTypeID'])) {
            $this->db->where_in("U.UserTypeID", $Where['UserTypeID']);
        }

        if (!empty($Where['UserTypeIDNot']) && $Where['UserTypeIDNot'] == 'Yes') {
            $this->db->where("U.UserTypeID!=", $Where['UserTypeIDNot']);
        }

        if (!empty($Where['UserID'])) {
            $this->db->where("U.UserID", $Where['UserID']);
        }
        if (!empty($Where['StateName'])) {
            $this->db->where("U.StateName", $Where['StateName']);
        }
        if (!empty($Where['UserArray'])) {
            $this->db->where_in("U.UserGUID", $Where['UserArray']);
        }
        if (!empty($Where['UserIDNot'])) {
            $this->db->where("U.UserID!=", $Where['UserIDNot']);
        }
        if (!empty($Where['UserGUID'])) {
            $this->db->where("U.UserGUID", $Where['UserGUID']);
        }
        if (!empty($Where['ReferredByUserID'])) {
            $this->db->where("U.ReferredByUserID", $Where['ReferredByUserID']);
        }

        if (!empty($Where['Username'])) {
            $this->db->where("U.Username", $Where['Username']);
        }
        if (!empty($Where['Email'])) {
            $this->db->where("U.Email", $Where['Email']);
        }
        if (!empty($Where['PhoneNumber'])) {
            $this->db->where("U.PhoneNumber", $Where['PhoneNumber']);
        }

        if (!empty($Where['LoginKeyword'])) {
            $this->db->group_start();
            $this->db->where("U.Email", $Where['LoginKeyword']);
            $this->db->or_where("U.EmailForChange", $Where['LoginKeyword']);
            $this->db->or_where("U.Username", $Where['LoginKeyword']);
            $this->db->or_where("U.PhoneNumber", $Where['LoginKeyword']);
            $this->db->group_end();
        }
        if (!empty($Where['Password'])) {
            $this->db->where("UL.Password", md5($Where['Password']));
        }

        if (!empty($Where['IsAdmin'])) {
            $this->db->where("UT.IsAdmin", $Where['IsAdmin']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['groupFilter']) && $Where['groupFilter'] == "Group") {
            $this->db->group_by("EmailForChange");
            $this->db->group_by("PhoneNumberForChange");
        }
        if (!empty($Where['EntryFrom'])) {
            $this->db->where("DATE(E.EntryDate) >=", $Where['EntryFrom']);
        }
        if (!empty($Where['EntryTo'])) {
            $this->db->where("DATE(E.EntryDate) <=", $Where['EntryTo']);
        }
        if (!empty($Where['ListType'])) {
            $this->db->where("DATE(E.EntryDate) =", date("Y-m-d"));
        }
        if (!empty($Where['isWithdrawal'])) {
            $this->db->where("U.isWithdrawal", $Where['isWithdrawal']);
        }
        if (!empty($Where['IsPrivacyNameDisplay'])) {
            $this->db->where("U.IsPrivacyNameDisplay", $Where['IsPrivacyNameDisplay']);
        }
        if (!empty($Where['WalletType']) && !empty($Where['Operator']) && !empty($Where['Amount'])) {
            $this->db->where($Where['WalletType'] . $Where['Operator'], $Where['Amount']);
        }

        if (!empty($Where['OrderBy']) && !empty($Where['Sequence']) && in_array($Where['Sequence'], array('ASC', 'DESC'))) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('U.UserID', 'DESC');
        }


        /* Total records count only if want to get multiple records */
        if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
        } else {
            $this->db->limit(1);
        }

        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            foreach ($Query->result_array() as $Record) {
                /* Get Wallet Data */
                if (in_array('Wallet', $Params)) {
                    $WalletData = $this->getWallet('Amount,Currency,PaymentGateway,TransactionType,TransactionID,EntryDate,Narration,Status,OpeningBalance,ClosingBalance', array('UserID' => $Where['UserID'], 'TransactionMode' => 'WalletAmount'), TRUE);
                    $Record['Wallet'] = ($WalletData) ? $WalletData['Data']['Records'] : array();
                }

                /* Get User Entries Data */
                if (in_array('UserEntriesBalance', $Params) && !empty($Where['WeekID'])) {
                    $Query = $this->db->query('SELECT `PurchasedEntries`,ConsumedEntries,AllowedPredictions,ConsumedPredictions,AllowedPurchaseDoubleUps,TotalPurchasedDoubleUps,(AllowedPurchaseDoubleUps-TotalPurchasedDoubleUps) RemainingPurchaseDoubleUps,ConsumeDoubleUps FROM `tbl_users_game_entries` WHERE `UserID` =' . $Where['UserID'] . ' AND WeekID = '.$Where['WeekID'].' LIMIT 1');
                    if($Query->num_rows() > 0){
                        $Record['UserEntriesBalance'] = $Query->row();
                    }else{
                        $Record['UserEntriesBalance'] = array('PurchasedEntries' => 0,'ConsumedEntries' => 0,'AllowedPredictions' => 0,'ConsumedPredictions' => 0,'AllowedPurchaseDoubleUps' => 0,'TotalPurchasedDoubleUps' => 0,'RemainingPurchaseDoubleUps' => 0,'ConsumeDoubleUps' => 0);
                    }
                }

                  /* Get Week Data */
                  if (in_array('WeekData', $Params)) {

                    $WeekData = $this->db->query('SELECT FW.WeekGUID,FW.WeekStartDate,FW.WeekEndDate FROM football_sports_season_weeks FW,tbl_entity E WHERE E.EntityID = FW.WeekID AND E.StatusID = 2 LIMIT 1')->row();
                    $Record['WeekData'] = ($WeekData) ? $WeekData : new stdClass();
                }

                if (!$multiRecords) {
                    return $Record;
                }
                $Records[] = $Record;
            }

            $Return['Data']['Records'] = $Records;
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: 	Use to create session.
     */

    function createSession($UserID, $Input = array()) {
        /* Multisession handling */
        if (!MULTISESSION) {
            $this->db->delete('tbl_users_session', array('UserID' => $UserID));
        } else {
            /* 			if(empty(@$Input['DeviceGUID'])){
              $this->db->delete('tbl_users_session', array('DeviceGUID' => $Input['DeviceGUID']));
              } */
        }

        /* Multisession handling - ends */
        $InsertData = array_filter(array(
            'UserID' => $UserID,
            'SessionKey' => get_guid(),
            'IPAddress' => @$Input['IPAddress'],
            'SourceID' => (!empty($Input['SourceID']) ? $Input['SourceID'] : DEFAULT_SOURCE_ID),
            'DeviceTypeID' => (!empty($Input['DeviceTypeID']) ? $Input['DeviceTypeID'] : DEFAULT_DEVICE_TYPE_ID),
            'DeviceGUID' => @$Input['DeviceGUID'],
            'DeviceToken' => @$Input['DeviceToken'],
            'EntryDate' => date("Y-m-d H:i:s"),
        ));

        $this->db->insert('tbl_users_session', $InsertData);
        /* update current date of login */
        $this->updateUserLoginInfo($UserID, array("LastLoginDate" => date("Y-m-d H:i:s")), $InsertData['SourceID']);
        /* Update Latitude, Longitude */
        if (!empty($Input['Latitude']) && !empty($Input['Longitude'])) {
            $this->updateUserInfo($UserID, array("Latitude" => $Input['Latitude'], "Longitude" => $Input['Longitude']));
        }
        return $InsertData['SessionKey'];
    }

    /*
      Description:    Use to get User login Sources.
     */

    function checkSources($UserID) {
        $this->db->select('S.SourceName');
        $this->db->from('set_source S');
        $this->db->where('EXISTS(SELECT 1 FROM `tbl_users_login` WHERE SourceID=S.SourceID AND UserID=' . $UserID . ')');
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            return $Query->result_array();
        }
        return FALSE;
    }

        /*
      Description:    Use to get User login Sources.
     */

    function getUserRoleType($UserID) {
        $this->db->select('UserTypeID');
        $this->db->from('tbl_users');
        $this->db->where("UserID", $UserID);
        $Query = $this->db->get();
        return $Query->row_array();

    }

    /*
      Description: 	Use to get UserID by SessionKey and validate SessionKey.
     */

    function checkSession($SessionKey) {
        $this->db->select('UserID');
        $this->db->from('tbl_users_session');
        $this->db->where("SessionKey", $SessionKey);
        $this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            return $Query->row()->UserID;
        }
        return FALSE;
    }

    /*
      Description: 	Use to delete Session.
     */

    function deleteSession($SessionKey) {
        $this->db->limit(1);
        $this->db->delete('tbl_users_session', array('SessionKey' => $SessionKey));
        return TRUE;
    }

    /*
      Description: 	Use to set new email address of user.
     */

    function updateEmail($UserID, $Email) {
        /* check new email address is not in use */
        $UserData = $this->Users_model->getUsers('', array('Email' => $Email,));
        if (!$UserData) {

            $this->db->trans_start();
            /* update profile table */
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users', array("Email" => $Email, "EmailForChange" => null));

            /* Delete session */
            $this->db->limit(1);
            $this->db->delete('tbl_users_session', array('UserID' => $UserID));
            /* Delete session - ends */
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }

            $this->Entity_model->updateEntityInfo($UserID, array("StatusID" => 2));
        }
        return TRUE;
    }

    /*
      Description: 	Use to set new email address of user.
     */

    function updatePhoneNumber($UserID, $PhoneNumber) {
        /* check new PhoneNumber is not in use */
        $UserData = $this->Users_model->getUsers('StatusID,PhoneNumber', array('PhoneNumber' => $PhoneNumber));
        if (!$UserData) {
            $this->db->trans_start();
            /* update profile table */
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users', array("PhoneNumber" => $PhoneNumber, "PhoneNumberForChange" => null));
            /* change entity status to activate */
            if ($UserData['StatusID'] == 1) {
                $this->Entity_model->updateEntityInfo($UserID, array("StatusID" => 2));
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /*
      Description: To get user wallet data
     */

    function add($Input = array(), $UserID, $CouponID = NULL) {
        /* Get Coupon Details */
        if (!empty($CouponID)) {
            $this->load->model('Store_model');
            $CouponDetailsArr = $this->Store_model->getCoupons('CouponTitle,CouponDescription,OfferType,CouponCode,CouponType,CouponValue', array('CouponID' => $CouponID));
            $CouponDetailsArr['DiscountedAmount'] = ($CouponDetailsArr['CouponType'] == 'Flat' ? $CouponDetailsArr['CouponValue'] : ($Input['Amount'] / 100) * $CouponDetailsArr['CouponValue']);
        }
        /* Add Wallet Pre Request */
        $TransactionID = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
        $InsertData = array(
            "Amount" => @$Input['Amount'],
            "WalletAmount" => @$Input['Amount'],
            "PaymentGateway" => $Input['PaymentGateway'],
            "CouponDetails" => (!empty($CouponID)) ? json_encode($CouponDetailsArr) : NULL,
            "CouponCode" => (!empty($CouponID)) ? $CouponDetailsArr['CouponCode'] : NULL,
            "TransactionType" => 'Cr',
            "TransactionID" => $TransactionID,
            "Narration" => 'Deposit Money',
            "EntryDate" => date("Y-m-d H:i:s")
        );
        $WalletID = $this->addToWallet($InsertData, $UserID);

        if ($WalletID) {
            $PaymentResponse = array();
            if ($Input['PaymentGateway'] == 'Stripe') {
                $PaymentResponse['MerchantKey']    = STRIPE_API_KEY;
                $PaymentResponse['PublishableKey'] = STRIPE_PUBLISHABLE_KEY;
                $PaymentResponse['MerchantName']   = SITE_NAME;
                $PaymentResponse['Amount']         = $Input['Amount'];
                $PaymentResponse['CentAmount']     = $Input['Amount'] * 100;
                $PaymentResponse['Currency']       = DEFAULT_CURRENCY;
                $PaymentResponse['OrderID']        = $WalletID;
                $PaymentResponse['LogoImage']      = SITE_HOST.ROOT_FOLDER.'assets/img/logo.png';
            }
            return $PaymentResponse;
        }
        return FALSE;
    }

    /*
      Description: To confirm payment gateway response
     */

    function confirm($Input = array(), $UserID) {
        $this->db->trans_start();

        /* Manage Stripe Payment */
        $TransactionStatus = 1;
        $ApiResponse       = array();
        if($Input['PaymentGateway'] == 'Stripe'){

            /* Include Stripe PHP library */
            require_once getcwd() . '/vendor/autoload.php';

            \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
            try{
                $Charge = \Stripe\Charge::create(array(
                    'amount'      => $Input['Amount'] * 100,
                    'currency'    => $Input['Currency'],
                    'source'      => $Input['StripeToken'],
                    'description' => 'Add funds to wallet'
                ));
                $Response = $Charge->jsonSerialize();
                $ApiResponse['TransactionResponse'] = $Response;
                if ($Response['amount_refunded'] == 0 && empty($Response['failure_code']) && $Response['paid'] == 1 && $Response['captured'] == 1) {
                    if ($Response['status'] == 'succeeded') {
                        $TransactionStatus = 5;
                        $ApiResponse['ResponseCode'] = 200;
                        $ApiResponse['Message'] = 'Your payment has been successfully done.';
                    } else {
                        $TransactionStatus = 3;
                        $ApiResponse['ResponseCode'] = 500;
                        $ApiResponse['Message'] = 'Transaction has been failed.';
                    }
                } else {
                    $TransactionStatus = 3;
                    $ApiResponse['ResponseCode'] = 500;
                    $ApiResponse['Message'] = 'Transaction has been failed.';
                }
            } catch (Exception $e) {
                $TransactionStatus = 3;
                $ApiResponse['ResponseCode'] = 500;
                $ApiResponse['Message'] = $e->getMessage();
                $ApiResponse['TransactionResponse'] = array('message' => $ApiResponse['Message']);
            }
        }

        /* Update Wallet Data */
        $UpdataData = array_filter(
                array(
                    'PaymentGatewayResponse' => (!empty($ApiResponse['TransactionResponse'])) ? json_encode($ApiResponse['TransactionResponse']) : NULL,
                    'ModifiedDate'           => date("Y-m-d H:i:s"),
                    'StatusID'               => $TransactionStatus
        ));
        if ($UpdataData['StatusID'] == 5) {
            $UpdataData['ClosingWalletAmount'] = $Input['OpeningWalletAmount'] + $Input['Amount'];
        }

        if(!empty($ApiResponse['TransactionResponse'])){
            unset($ApiResponse['TransactionResponse']);
        }
        $this->db->where(array('WalletID' => $Input['WalletID'], 'UserID' => $UserID, 'StatusID' => 1));
        $this->db->limit(1);
        $this->db->update('tbl_users_wallet', $UpdataData);
        if ($this->db->affected_rows() <= 0) {
            return FALSE;
        }

        /* Update user main wallet amount */
        if ($UpdataData['StatusID'] == 5) {
            
            /* Update User Wallet */
            $this->db->set('WalletAmount', 'WalletAmount+' . $Input['Amount'], FALSE);
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users');

            /* Add Notification */
            $this->Notification_model->addNotification('AddCash', 'Cash Added', $UserID, $UserID, '', 'Deposit of ' . DEFAULT_CURRENCY . ' ' . $Input['Amount'] . ' is Successful.');

            /* Check Coupon Details */
            if (!empty($Input['CouponDetails'])) {
                $WalletData = array(
                    "Amount"          => $CouponDetails['DiscountedAmount'],
                    "WalletAmount"    => $CouponDetails['DiscountedAmount'],
                    "TransactionType" => 'Cr',
                    "Narration"       => 'Coupon Discount',
                    "EntryDate"       => date("Y-m-d H:i:s")
                );
                $this->addToWallet($WalletData, $UserID, 5);

                /* Add Notification */
                $this->Notification_model->addNotification('CouponDiscount', 'Coupon Discount Added', $UserID, $UserID, '', $CouponDetails['CouponCode'].' coupon was successfully applied. Coupon discount of ' . DEFAULT_CURRENCY . ' ' . $CouponDetails['DiscountedAmount'] . ' successfully added in your wallet.');
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        $ApiResponse['Data'] = $this->getWalletDetails($UserID);
        if($Input['PaymentGateway'] == 'Stripe'){
            $ApiResponse['Data']->StripeAPIStatus = (!empty($Response['status']) && $Response['status'] == 'succeeded') ? 'Success' : 'Failed';
        }
        return $ApiResponse;
    }

    function confirmApp($Input = array(), $UserID) {
        /* Verify payment from payment gateway */
        if ($Input['PaymentGatewayStatus'] == 'Success') {

            $PaymentGatewayResponse = (!empty($Input['PaymentGatewayResponse'])) ? @json_decode($Input['PaymentGatewayResponse'], TRUE) : NULL;

            if ($Input['PaymentGateway'] == 'Stripe') {
                $this->db->select('WalletID');
                $this->db->where("WalletID", $Input['WalletID']);
                $this->db->where("UserID", $UserID);
                $this->db->where("PaymentGateway", "Stripe");
                $this->db->where('StatusID', 5);
                $this->db->from('tbl_users_wallet');
                $this->db->limit(1);
                $WalletDetails = $this->db->get()->row();
                if (!empty($WalletDetails)) {
                    return $this->getWalletDetails($UserID);
                }                

                if ($PaymentGatewayResponse['ResponseCode']==200) {
                    $Input['PaymentGatewayStatus'] = 'Success';
                    $Input['PaymentGatewayResponse'] = json_encode($PaymentGatewayResponse);
                } else {
                    $PaymentGatewayResponse['razorpay_payment_id'] = @$Input['Razor_payment_id'];
                    $Input['PaymentGatewayStatus'] = 'Failed';
                    $Input['PaymentGatewayResponse'] = json_encode($PaymentGatewayResponse);
                }
            }
        }

        /* update profile table */
        $UpdataData = array_filter(
                array(
                    'PaymentGatewayResponse' => @$Input['PaymentGatewayResponse'],
                    'ModifiedDate' => date("Y-m-d H:i:s"),
                    'StatusID' => ($Input['PaymentGatewayStatus'] == 'Failed' || $Input['PaymentGatewayStatus'] == 'Cancelled') ? 3 : 5
        ));
        $this->db->where('WalletID', $Input['WalletID']);
        $this->db->where('UserID', $UserID);
        $this->db->where('StatusID', 1);
        $this->db->limit(1);
        $this->db->update('tbl_users_wallet', $UpdataData);
        if ($this->db->affected_rows() <= 0) return $this->getWalletDetails($UserID);

        $this->db->select("Amount");
        $this->db->from('tbl_users_wallet');
        $this->db->where('WalletID', $Input['WalletID']);
        $this->db->where('UserID', $UserID);
        $this->db->where('StatusID', 5);
        $this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Amount = $Query->row()->Amount;
            /* Update user main wallet amount */
            if ($Input['PaymentGatewayStatus'] == 'Success') {

                $this->db->set('ClosingWalletAmount', 'ClosingWalletAmount+' . @$Input['Amount'], FALSE);
                $this->db->where('WalletID', $Input['WalletID']);
                $this->db->where('UserID', $UserID);
                $this->db->where('StatusID', 5);
                $this->db->limit(1);
                $this->db->update('tbl_users_wallet');


                $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                $this->db->where('UserID', $UserID);
                $this->db->limit(1);
                $this->db->update('tbl_users');

                $this->Notification_model->addNotification('AddCash', 'Cash Added', $UserID, $UserID, '', 'Deposit of ' . DEFAULT_CURRENCY . @$Input['Amount'] . ' is Successful.');

                $CouponDetails = $this->getWallet('CouponDetails', array("WalletID" => $Input['WalletID']));

                /* Check Coupon Details */
                if (!empty($CouponDetails['CouponDetails'])) {
                    $WalletData = array(
                        "Amount" => $CouponDetails['CouponDetails']['DiscountedAmount'],
                        "CashBonus" => ($CouponDetails['CouponDetails']['OfferType'] == "CashBonus" ? $CouponDetails['CouponDetails']['DiscountedAmount'] : 0),
                        "WalletAmount" => ($CouponDetails['CouponDetails']['OfferType'] == "RealMoney" ? $CouponDetails['CouponDetails']['DiscountedAmount'] : 0),
                        "TransactionType" => 'Cr',
                        "Narration" => ($CouponDetails['CouponDetails']['OfferType'] == "RealMoney" ? 'Coupon Discount' : 'Coupon Discount Bonus'),
                        "EntryDate" => date("Y-m-d H:i:s")
                    );
                    $this->addToWallet($WalletData, $UserID, 5);
                }
            }
        }
        return $this->getWalletDetails($UserID);
    }

    /*
      Description: To to get stripe ephemeral keys data
     */

    function stripeEphemeralKeys($SessionUserID) {

        /* To Get User Details */
        $User = $this->db->query('SELECT FirstName,Email,StripeCustomerID FROM tbl_users WHERE UserID = ' . $SessionUserID . ' LIMIT 1')->row_array();
        $StripeCustomerID = $User['StripeCustomerID'];

        /* Include Stripe PHP library */
        require_once getcwd() . '/vendor/autoload.php';

        // Set API key 
        \Stripe\Stripe::setApiKey(STRIPE_API_KEY);

        // Create Customer on stripe
        $ApiResponse = array();
        if (empty($StripeCustomerID)) {
            try {
                $Customer = \Stripe\Customer::create([
                    'name'  => $User['FirstName'],
                    'email' => $User['Email'],
                    'description' => SITE_NAME,
                ]);

                // Retrieve customer details 
                $Response = $Customer->jsonSerialize();
                $StripeCustomerID = $Response['id'];

                // Update Stripe Customer ID
                $this->db->query('UPDATE tbl_users SET StripeCustomerID = "' . $StripeCustomerID . '" WHERE UserID = ' . $SessionUserID . ' LIMIT 1');
            } catch (Exception $e) {
                $ApiResponse['ResponseCode'] = 500;
                $ApiResponse['Message'] = $e->getMessage();
                return $ApiResponse;
            }
        }

        // Create Ephemeral Keys
        try {
            $Key = \Stripe\EphemeralKey::create(
                ['customer' => $StripeCustomerID],
                ['stripe_version' => '2019-05-16']
            );
            $ApiResponse['ResponseCode'] = 200;
            $ApiResponse['Message'] = 'Success';
            $ApiResponse['Data'] = $Key->jsonSerialize();
            return $ApiResponse;
         } catch (Exception $e) {
            $ApiResponse['ResponseCode'] = 500;
            $ApiResponse['Message'] = $e->getMessage();
            return $ApiResponse;
        }
    }

    /*
      Description: To to get payment intent keys data
     */

    function returnClientSecret($Input = array(), $SessionUserID) {

        /* To Get User Details */
        $User = $this->db->query('SELECT FirstName,Email,StripeCustomerID FROM tbl_users WHERE UserID = ' . $SessionUserID . ' LIMIT 1')->row_array();
        $StripeCustomerID = $User['StripeCustomerID'];

        /* Include Stripe PHP library */
        require_once getcwd() . '/vendor/autoload.php';

        // Set API key 
        \Stripe\Stripe::setApiKey(STRIPE_API_KEY);

        // Create Customer on stripe
        $ApiResponse = array();
        if (empty($StripeCustomerID)) {
            try {
                $Customer = \Stripe\Customer::create([
                    'name'  => $User['FirstName'],
                    'email' => $User['Email'],
                    'description' => SITE_NAME,
                ]);

                // Retrieve customer details 
                $Response = $Customer->jsonSerialize();
                $StripeCustomerID = $Response['id'];

                // Update Stripe Customer ID
                $this->db->query('UPDATE tbl_users SET StripeCustomerID = "' . $StripeCustomerID . '" WHERE UserID = ' . $SessionUserID . ' LIMIT 1');
            } catch (Exception $e) {
                $ApiResponse['ResponseCode'] = 500;
                $ApiResponse['Message'] = $e->getMessage();
                return $ApiResponse;
            }
        }

        // Create Secret Keys
        try {
            $intent = \Stripe\PaymentIntent::create([
                'customer' => $Input['customerId'],
                'amount'   => $Input['amount'],
                'currency' => DEFAULT_CURRENCY,
            ]);
            $ApiResponse['ResponseCode'] = 200;
            $ApiResponse['Message'] = 'Success';
            $ApiResponse['Data'] = $intent->client_secret;
            return $ApiResponse;
         } catch (Exception $e) {
            $ApiResponse['ResponseCode'] = 500;
            $ApiResponse['Message'] = $e->getMessage();
            return $ApiResponse;
        }
    }

    /*
      Description: To add data into user wallet
     */

    function addToWallet($Input = array(), $UserID, $StatusID = 1) {
        $this->db->trans_start();

        $OpeningWalletAmount = $this->getUserWalletOpeningBalance($UserID, 'ClosingWalletAmount');
        $OpeningWinningAmount = $this->getUserWalletOpeningBalance($UserID, 'ClosingWinningAmount');
        $InsertData = array_filter(array(
            "UserID" => $UserID,
            "Amount" => @$Input['Amount'],
            "OpeningWalletAmount" => $OpeningWalletAmount,
            "OpeningWinningAmount" => $OpeningWinningAmount,
            "WalletAmount" => @$Input['WalletAmount'],
            "WinningAmount" => @$Input['WinningAmount'],
            "ClosingWalletAmount" => ($StatusID == 5) ? (($OpeningWalletAmount != 0) ? ((@$Input['TransactionType'] == 'Cr') ? $OpeningWalletAmount + @$Input['WalletAmount'] : $OpeningWalletAmount - @$Input['WalletAmount'] ) : @$Input['WalletAmount']) : $OpeningWalletAmount,
            "ClosingWinningAmount" => ($StatusID == 5) ? (($OpeningWinningAmount != 0) ? ((@$Input['TransactionType'] == 'Cr') ? $OpeningWinningAmount + @$Input['WinningAmount'] : $OpeningWinningAmount - @$Input['WinningAmount'] ) : @$Input['WinningAmount']) : $OpeningWinningAmount,
            "Currency" => @$Input['Currency'],
            "CouponCode" => @$Input['CouponCode'],
            "PaymentGateway" => @$Input['PaymentGateway'],
            "TransactionType" => @$Input['TransactionType'],
            "TransactionID" => (!empty($Input['TransactionID'])) ? $Input['TransactionID'] : substr(hash('sha256', mt_rand() . microtime()), 0, 20),
            "Narration" => @$Input['Narration'],
            "EntityID" => @$Input['EntityID'],
            "CouponDetails" => @$Input['CouponDetails'],
            "AmountType" => @$Input['AmountType'],
            "PaymentGatewayResponse" => @$Input['PaymentGatewayResponse'],
            "EntryDate" => date("Y-m-d H:i:s"),
            "StatusID" => $StatusID
        ));
        $this->db->insert('tbl_users_wallet', $InsertData);
        $WalletID = $this->db->insert_id();

        /* Update User Balance */
        if ($StatusID == 5 || $Input['Narration'] == 'Withdrawal Request') {
            switch (@$Input['Narration']) {
                case 'Deposit Money':
                case 'Signup Deposit Bonus':
                case 'Signup Bonus':
                case 'Admin Deposit Money':
                    $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                    break;
                case 'Referral Deposit':
                case 'Football Winning':
                    if ($Input['WinningAmount'] > 0) {
                        $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    }
                    break;
                case 'Coupon Discount':
                case 'Signup Direct Deposit':
                    if ($Input['WalletAmount'] > 0) {
                        $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                    }
                    break;
                case 'Withdrawal Request':
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    if (@$Input['WithdrawalStatus'] == 1) {
                        $this->db->set('WithdrawalHoldAmount', 'WithdrawalHoldAmount+' . @$Input['WinningAmount'], FALSE);
                    }
                    break;
                case 'Withdrawal Reject':
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    $this->db->set('WithdrawalHoldAmount', 'WithdrawalHoldAmount-' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Purchase Entries':
                case 'Purchase Double UP':
                    $this->db->set('WalletAmount', 'WalletAmount-' . @$Input['Amount'], FALSE);
                    break;
                default:
                    break;
            }
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $WalletID;
    }



        /*
      Description: To add data into user wallet
     */

    function addToWalletBonus($Input = array(), $UserID, $StatusID = 1) {
        $this->db->trans_start();

        $OpeningWalletAmount = $this->getUserWalletOpeningBalance($UserID, 'ClosingWalletAmount');
        $OpeningWinningAmount = $this->getUserWalletOpeningBalance($UserID, 'ClosingWinningAmount');
        $OpeningCashBonus = $this->getUserWalletOpeningBalance($UserID, 'ClosingCashBonus');
        $InsertData = array_filter(array(
            "UserID" => $UserID,
            "Amount" => @$Input['Amount'],
            "OpeningWalletAmount" => $OpeningWalletAmount,
            "OpeningWinningAmount" => $OpeningWinningAmount,
            "OpeningCashBonus" => $OpeningCashBonus,
            "WalletAmount" => @$Input['WalletAmount'],
            "WinningAmount" => @$Input['WinningAmount'],
            "CashBonus" => @$Input['CashBonus'],
            "SportsType" => @$Input['SportsType'],
            "ClosingWalletAmount" => ($StatusID == 5) ? (($OpeningWalletAmount != 0) ? ((@$Input['TransactionType'] == 'Cr') ? $OpeningWalletAmount + @$Input['WalletAmount'] : $OpeningWalletAmount - @$Input['WalletAmount'] ) : @$Input['WalletAmount']) : $OpeningWalletAmount,
            "ClosingWinningAmount" => ($StatusID == 5) ? (($OpeningWinningAmount != 0) ? ((@$Input['TransactionType'] == 'Cr') ? $OpeningWinningAmount + @$Input['WinningAmount'] : $OpeningWinningAmount - @$Input['WinningAmount'] ) : @$Input['WinningAmount']) : $OpeningWinningAmount,
            "ClosingCashBonus" => ($StatusID == 5) ? (($OpeningCashBonus != 0) ? ((@$Input['TransactionType'] == 'Cr') ? $OpeningCashBonus + @$Input['CashBonus'] : $OpeningCashBonus - @$Input['CashBonus'] ) : @$Input['CashBonus']) : $OpeningCashBonus,
            "Currency" => @$Input['Currency'],
            "CouponCode" => @$Input['CouponCode'],
            "PaymentGateway" => @$Input['PaymentGateway'],
            "TransactionType" => @$Input['TransactionType'],
            "TransactionID" => (!empty($Input['TransactionID'])) ? $Input['TransactionID'] : substr(hash('sha256', mt_rand() . microtime()), 0, 20),
            "Narration" => @$Input['Narration'],
            "EntityID" => @$Input['EntityID'],
            "UserTeamID" => @$Input['UserTeamID'],
            "CouponDetails" => @$Input['CouponDetails'],
            "ReferralGetAmountUserID" => @$Input['ReferralGetAmountUserID'],
            "AmountType" => @$Input['AmountType'],
            "PaymentGatewayResponse" => @$Input['PaymentGatewayResponse'],
            "EntryDate" => date("Y-m-d H:i:s"),
            "StatusID" => $StatusID
        ));
        
        $this->db->insert('tbl_users_wallet', $InsertData);
        $WalletID = $this->db->insert_id();

        /* Update User Balance */
        if ($StatusID == 5 || $Input['Narration'] == 'Withdrawal Request') {
            switch (@$Input['Narration']) {
                case 'Deposit Money':
                case 'Admin Deposit Money':
                    $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                    break;
                case 'Join Contest Winning':
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Join Contest Winning Bonus':
                    $this->db->set('CashBonus', 'CashBonus+' . @$Input['CashBonus'], FALSE);
                    break;
                case 'Join Contest':
                    $this->db->set('WalletAmount', 'WalletAmount-' . @$Input['WalletAmount'], FALSE);
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    $this->db->set('CashBonus', 'CashBonus-' . @$Input['CashBonus'], FALSE);
                    break;
                case 'Cancel Contest':
                    $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['WalletAmount'], FALSE);
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    $this->db->set('CashBonus', 'CashBonus+' . @$Input['CashBonus'], FALSE);
                    break;
                case 'Wrong Winning Distribution':
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Referral Deposit':
                    if ($Input['WinningAmount'] > 0) {
                        $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    }
                    break;
                case 'Lucky Wheel':
                case 'Signup Bonus':
                case 'Verification Bonus':
                case 'First Deposit Bonus':
                case 'Referral Bonus':
                case 'Admin Cash Bonus':
                case 'Coupon Discount Bonus':
                    if ($Input['CashBonus'] > 0) {
                        $this->db->set('CashBonus', 'CashBonus+' . @$Input['Amount'], FALSE);
                    }
                    break;
                case 'Coupon Discount':
                case 'Signup Direct Deposit':
                    if ($Input['WalletAmount'] > 0) {
                        $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                    }
                    break;
                case 'Withdrawal Request':
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    if (@$Input['WithdrawalStatus'] == 1) {
                        $this->db->set('WithdrawalHoldAmount', 'WithdrawalHoldAmount+' . @$Input['WinningAmount'], FALSE);
                    }
                    break;
                case 'Withdrawal Reject':
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    $this->db->set('WithdrawalHoldAmount', 'WithdrawalHoldAmount-' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Cash Bonus Expire':
                    $this->db->set('CashBonus', 'CashBonus-' . @$Input['Amount'], FALSE);
                    break;
                default:
                    break;
            }
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $WalletID;
    }

    /*
      Description: To get user wallet opening balance
     */

    function getUserWalletOpeningBalance($UserID, $Field) {
        return $this->db->query('SELECT ' . str_replace("Closing", "", $Field) . ' Amount FROM `tbl_users` WHERE `UserID` = ' . $UserID . ' LIMIT 1')->row()->Amount;

        $Query = $this->db->query('SELECT IF(' . $Field . ' IS NULL,0,' . $Field . ') Amount FROM `tbl_users_wallet` WHERE StatusID = 5 AND `UserID` = ' . $UserID . ' ORDER BY `WalletID` DESC LIMIT 1');
        if ($Query->num_rows() > 0) {
            return $Query->row()->Amount;
        } else {
     
            return $this->db->query('SELECT ' . str_replace("Closing", "", $Field) . ' Amount FROM `tbl_users` WHERE `UserID` = ' . $UserID . ' LIMIT 1')->row()->Amount;
        }
    }

    /*
      Description: To get user wallet details
     */

    function getWalletDetails($UserID) {
        return $this->db->query('SELECT `WalletAmount`,WinningAmount FROM `tbl_users` WHERE `UserID` =' . $UserID . ' LIMIT 1')->row();
    }

    function getDeposits($Where = array(), $PageNo = 1, $PageSize = 15) {

        $this->db->select('W.UserID,W.Amount,W.PaymentGateway,W.TransactionID,W.EntryDate,U.Email,U.PhoneNumber,U.FirstName,U.LastName');
        $this->db->from('tbl_users_wallet W');
        $this->db->from('tbl_users U');
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("U.FirstName", $Where['Keyword']);
            $this->db->or_like("U.LastName", $Where['Keyword']);
            $this->db->or_like("U.Email", $Where['Keyword']);
            $this->db->or_like("CONCAT_WS('',U.FirstName,U.Middlename,U.LastName)", preg_replace('/\s+/', '', $Where['Keyword']), FALSE);
            $this->db->group_end();
        }
        $this->db->where('W.UserID', "U.UserID", false);
        $this->db->where('W.Narration', "Deposit Money");
        $this->db->where('W.StatusID', 5);

        if (!empty($Where['Type']) && $Where['Type'] == 'Today') {
            $this->db->where("W.EntryDate >=", date('Y:m:d'));
        }
        if (!empty($Where['FromDate'])) {
            $this->db->where("W.EntryDate >=", $Where['FromDate']);
        }
        if (!empty($Where['ToDate'])) {
            $this->db->where("W.EntryDate <=", $Where['ToDate']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }
        if ($PageNo != 0) {
            $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
        }

        $DepositsData = $this->db->get();
        // echo $this->db->last_query(); die();

        $Return['Data']['Records'] = $DepositsData->result_array();

        return $Return;
    }

    /*
      Description: To get user wallet data
     */

    function getWallet($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        $Params = array();
        $Return = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'UserID' => 'W.UserID',
                'Amount' => 'W.Amount',
                'OpeningWalletAmount' => 'W.OpeningWalletAmount',
                'OpeningWinningAmount' => 'W.OpeningWinningAmount',
                'OpeningCashBonus' => 'W.OpeningCashBonus',
                'WalletAmount' => 'W.WalletAmount',
                'WinningAmount' => 'W.WinningAmount',
                'CashBonus' => 'W.CashBonus',
                'EntityID' => 'W.EntityID',
                'UserTeamID' => 'W.UserTeamID',
                'ClosingWalletAmount' => 'W.ClosingWalletAmount',
                'ClosingWinningAmount' => 'W.ClosingWinningAmount',
                'ClosingCashBonus' => 'W.ClosingCashBonus',
                'WithdrawalHoldAmount' => 'W.WithdrawalHoldAmount',
                'Currency' => 'W.Currency',
                'PaymentGateway' => 'W.PaymentGateway',
                'CouponDetails' => 'W.CouponDetails',
                'TransactionType' => 'W.TransactionType',
                'TransactionID' => 'W.TransactionID',
                'OpeningBalance' => '(W.OpeningWalletAmount + W.OpeningWinningAmount + W.OpeningCashBonus) OpeningBalance',
                'ClosingBalance' => '(W.ClosingWalletAmount + W.ClosingWinningAmount + W.ClosingCashBonus) ClosingBalance',
                'Narration' => 'W.Narration',
                'EntryDate' => 'DATE_FORMAT(CONVERT_TZ(W.EntryDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") EntryDate',
                'Status' => 'CASE W.StatusID
                                        when "1" then "Pending"
                                        when "2" then "Processing"
                                        when "3" then "Failed"
                                        when "5" then "Completed"
                                    END as Status',
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }

        if (in_array('MediaBANK', $Params)) {
            $Media['BANK'] = array(
                'EntryDate' => '',
                'MediaGUID' => '',
                'MediaURL' => '',
                'MediaThumbURL' => '',
                'MediaCaption' => (object) []
            );
            $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID,DATE_FORMAT(CONVERT_TZ(E.EntryDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") EntryDate, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,"110_",M.MediaName) AS MediaThumbURL, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL,	M.MediaCaption', array("SectionID" => 'BankDetail', "EntityID" => $Where['UserID']), FALSE);
            if ($MediaData) $MediaData['MediaCaption'] = json_decode($MediaData['MediaCaption']);
            $Return['Data']['MediaBANK'] = ($MediaData ? $MediaData : $Media['BANK']);

            $MaximumWithdrawalLimitBank = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'MaximumWithdrawalLimitBank'")->row()->ConfigTypeValue;
            $MinimumWithdrawalLimitBank = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'MinimumWithdrawalLimitBank'")->row()->ConfigTypeValue;

            $MaximumWithdrawalLimitPaytm = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'MaximumWithdrawalLimitPaytm'")->row()->ConfigTypeValue;
            $MinimumWithdrawalLimitPaytm = $this->db->query("SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = 'MinimumWithdrawalLimitPaytm'")->row()->ConfigTypeValue;

            $Return['Data']['MediaBANK']['Message'] = "1.In Bank User can withdraw (minimum " . $MinimumWithdrawalLimitBank . " and maximum of INR " . $MaximumWithdrawalLimitBank . " per day)\n\n2.In Paytm Wallet User Can Withdraw (minimum " . $MinimumWithdrawalLimitPaytm . " and maximum of INR " . $MaximumWithdrawalLimitPaytm . " per day)\n\n3.Normal Bank Withdrawal a minimum of INR " . $MinimumWithdrawalLimitBank . " and maximum  amount INR " . $MaximumWithdrawalLimitBank . " in a day. Gets Processed In your bank account within 2-3 Working Days.\n\n4.Withdrawal timings are 11.00AM to 8.00PM (In case of instant withdrawal)";
        }

        if (in_array('WalletDetails', $Params)) {
            $WalletData = $this->db->query('select WalletAmount,WinningAmount,CashBonus,(WalletAmount + WinningAmount + CashBonus) AS TotalCash from tbl_users where UserID = ' . $Where['UserID'])->row();
            $Return['Data']['WalletAmount'] = $WalletData->WalletAmount;
            $Return['Data']['CashBonus'] = $WalletData->CashBonus;
            $Return['Data']['WinningAmount'] = $WalletData->WinningAmount;
            $Return['Data']['TotalCash'] = $WalletData->TotalCash;
        }

        if (in_array('VerificationDetails', $Params)) {
            $UserVerificationData = $this->Users_model->getUsers('Status,PanStatus,BankStatus,PhoneStatus,EmailStatus,AadharStatus', array('UserID' => $Where['UserID']));
            $Return['Data']['Status'] = $UserVerificationData['Status'];
            $Return['Data']['PanStatus'] = $UserVerificationData['PanStatus'];
            $Return['Data']['BankStatus'] = $UserVerificationData['BankStatus'];
            $Return['Data']['PhoneStatus'] = $UserVerificationData['PhoneStatus'];
            $Return['Data']['AadharStatus'] = $UserVerificationData['AadharStatus'];
            $Return['Data']['EmailStatus'] = $UserVerificationData['EmailStatus'];
        }
        if (in_array('CashbonusExpiry', $Params)) {
            $Return['Data']['CashbonusMessage'] = $this->getUserCashbonusExpiry($Where['UserID']);
        }

        $this->db->select('W.WalletID');
        if (!empty($Field)) $this->db->select($Field, FALSE);
        $this->db->from('tbl_users_wallet W');
        if (!empty($Where['WalletID'])) {
            $this->db->where("W.WalletID", $Where['WalletID']);
        }
        if (!empty($Where['CouponCode'])) {
            $this->db->where("W.CouponCode", $Where['CouponCode']);
        }
        if (!empty($Where['UserID'])) {
            $this->db->where("W.UserID", $Where['UserID']);
        }
        if (!empty($Where['PaymentGateway'])) {
            $this->db->where("W.PaymentGateway", $Where['PaymentGateway']);
        }
        if (!empty($Where['TransactionType'])) {
            $this->db->where("W.TransactionType", $Where['TransactionType']);
        }
        if (!empty($Where['Narration'])) {
            $this->db->where("W.Narration", $Where['Narration']);   
        }
        if (!empty($Where['NarrationMultiple'])) {
            $this->db->where_in("W.Narration", $Where['NarrationMultiple']);   
        }
        if (!empty($Where['EntityID'])) {
            $this->db->where("W.EntityID", $Where['EntityID']);
        }
        if (!empty($Where['UserTeamID'])) {
            $this->db->where("W.UserTeamID", $Where['UserTeamID']);
        }
        if (!empty($Where['TransactionMode']) && $Where['TransactionMode'] != 'All') {
            $this->db->where("W." . $Where['TransactionMode'] . ' >', 0);
        }
        if (!empty($Where['Filter']) && $Where['Filter'] == 'FailedCompleted') {
            $this->db->where_in("W.StatusID", array(3, 5));
        }
        if (!empty($Where['FromDate'])) {
            $this->db->where("W.EntryDate >=", $Where['FromDate']);
        }
        if (!empty($Where['ToDate'])) {
            $this->db->where("W.EntryDate <=", $Where['ToDate']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("W.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }

        $this->db->order_by('W.WalletID', 'DESC');

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
                $Records = array();
                foreach ($Query->result_array() as $key => $Record) {
                    $Records[] = $Record;
                    if (in_array('CouponDetails', $Params)) {
                        $Records[$key]['CouponDetails'] = (!empty($Record['CouponDetails'])) ? json_decode($Record['CouponDetails'], TRUE) : array();
                    }
                }
                $Return['Data']['Records'] = $Records;
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('CouponDetails', $Params)) {
                    $Record['CouponDetails'] = (!empty($Record['CouponDetails'])) ? json_decode($Record['CouponDetails'], TRUE) : array();
                }
                return $Record;
            }
        } else {
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: To send OTP for withdrawal
     */

    public function withdrawalOTP($Input = array(), $UserID) {
        /* Insert Withdrawal Add Request */
        $OTP = random_string('numeric', 6);
        $InsertData = array_filter(array(
            "UserID" => $UserID,
            "Amount" => @$Input['Amount'],
            "PaytmPhoneNumber" => @$Input['PaytmPhoneNumber'],
            "OTP" => $OTP,
            "IsOTPVerified" => "No",
            "PaymentGateway" => $Input['PaymentGateway'],
            "EntryDate" => date("Y-m-d H:i:s"),
            "StatusID" => 1,
        ));
        $this->db->insert('tbl_users_withdrawal', $InsertData);
        $WithdrawalID = $this->db->insert_id();
        if (empty($WithdrawalID)) {
            return false;
        }

        /* Verify OTP Send SMS */
        $this->Utility_model->sendSMS(array(
            'PhoneNumber' => $Input['PaytmPhoneNumber'],
            'Text' => SITE_NAME . ", OTP for withdraw request is: " . $OTP,
        ));
        return array('WithdrawalID' => $WithdrawalID, 'WalletDetails' => $this->getWalletDetails($UserID));
    }

    /*
      Description: To withdrawal amount
     */

    function withdrawal($Input = array(), $UserID) {
        if (AUTO_WITHDRAWAL && $Input['PaymentGateway'] == 'Paytm') {
            /* Withdraw to Paytm Account */
            $StatusID = 3;
            $OrderID = "Order" . substr(hash('sha256', mt_rand() . microtime()), 0, 10);
            $data = array(
                "request" => array("requestType" => $OrderID,
                    "merchantGuid" => PAYTM_MERCHANT_GUID,
                    "merchantOrderId" => $OrderID,
                    "salesWalletName" => null,
                    "salesWalletGuid" => PAYTM_SALES_WALLET_GUID,
                    "payeeEmailId" => null,
                    "payeePhoneNumber" => @$Input['PaytmPhoneNumber'],
                    "payeeSsoId" => "",
                    "appliedToNewUsers" => "N",
                    "amount" => @$Input['Amount'],
                    "currencyCode" => "INR"
                ),
                "metadata" => "Withdraw Money",
                // "ipAddress"     => $this->input->ip_address(),
                "ipAddress" => "127.0.0.1",
                "platformName" => "PayTM",
                "operationType" => "SALES_TO_USER_CREDIT"
            );
            $requestData = json_encode($data);
            $Checksumhash = $this->getChecksumFromString($requestData, PAYTM_MERCHANT_KEY_WITHDRAWAL);
            $headerValue = array('Content-Type:application/json', 'mid:' . PAYTM_MERCHANT_GUID, 'checksumhash:' . $Checksumhash);

            $ch = curl_init(PAYTM_GRATIFICATION_URL);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerValue);
            $info = curl_getinfo($ch);
            $PaymentGatewayResponse = curl_exec($ch);
            $PaymentGatewayResponse = json_decode($PaymentGatewayResponse);
            $StatusArr = array("FAILURE" => 3, "SUCCESS" => 5, "PENDING" => 3); // NEED TO MANAGE WEBHOOKS IN PENDING CASE
            $StatusID = (!empty($PaymentGatewayResponse)) ? $StatusArr[$PaymentGatewayResponse->status] : 3;
        } else {
            $StatusID = 1;
        }

        $this->db->trans_start();

        if (!empty($this->Post['PaymentGateway']) && $this->Post['PaymentGateway'] == 'Paytm') {
            /* Update Withdrawal Request */
            $InsertData = array_filter(array(
                "UserID" => $UserID,
                "Amount" => @$Input['Amount'],
                "PaytmPhoneNumber" => @$Input['PaytmPhoneNumber'],
                "PaymentGatewayResponse" => (!empty($PaymentGatewayResponse)) ? json_encode($PaymentGatewayResponse) : null,
                "IsOTPVerified" => "Yes",
                "PaymentGateway" => $Input['PaymentGateway'],
                "EntryDate" => date("Y-m-d H:i:s"),
                "ModifiedDate" => date("Y-m-d H:i:s"),
                "StatusID" => $StatusID,
            ));
            $this->db->insert('tbl_users_withdrawal', $InsertData);
            /* $UpdateData = array(
              "IsOTPVerified" => "Yes",
              "PaymentGatewayResponse" => (!empty($PaymentGatewayResponse)) ? json_encode($PaymentGatewayResponse) : null,
              "ModifiedDate" => date("Y-m-d H:i:s"),
              "StatusID" => $StatusID,
              );
              $this->db->where('WithdrawalID', $Input['WithdrawalID']);
              $this->db->limit(1);
              $this->db->update('tbl_users_withdrawal', $UpdateData); */
        }

        /* Update user winning amount */
        if ($StatusID == 1 || $StatusID == 5) {
            $WalletData = array(
                "Amount" => @$Input['Amount'],
                "WinningAmount" => @$Input['Amount'],
                "TransactionType" => 'Dr',
                "Narration" => 'Withdrawal Request',
                "EntryDate" => date("Y-m-d H:i:s"),
                "WithdrawalStatus" => $StatusID
            );
            $WalletID = $this->addToWallet($WalletData, $UserID, $StatusID);
        }
        if (!empty($this->Post['PaymentGateway']) && $this->Post['PaymentGateway'] == 'Bank') {
            /* Insert Withdrawal Logs */
            $InsertData = array(
                "UserID" => $UserID,
                "WalletID" => $WalletID,
                "Amount" => @$Input['Amount'],
                "PaytmPhoneNumber" => @$Input['PaytmPhoneNumber'],
                "PaymentGatewayResponse" => (!empty($PaymentGatewayResponse)) ? json_encode($PaymentGatewayResponse) : NULL,
                "PaymentGateway" => $Input['PaymentGateway'],
                "EntryDate" => date("Y-m-d H:i:s"),
                "StatusID" => $StatusID
            );
            $this->db->insert('tbl_users_withdrawal', $InsertData);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        $Response = $this->getWalletDetails($UserID);
        $Response->paytmResponse = (!empty($PaymentGatewayResponse) ? $PaymentGatewayResponse : "");

        return $Response;
    }

    /*
      Description: To get user withdrawals data
     */

    function getWithdrawals($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'UserID' => 'W.UserID',
                'Amount' => 'W.Amount',
                'Email' => 'U.Email',
                'PhoneNumber' => 'U.PhoneNumber',
                'FirstName' => 'U.FirstName',
                'Middlename' => 'U.Middlename',
                'LastName' => 'U.LastName',
                'Comments' => 'W.Comments',
                'ProfilePic' => 'IF(U.ProfilePic IS NULL,CONCAT("' . BASE_URL . '","uploads/profile/picture/","default.jpg"),CONCAT("' . BASE_URL . '","uploads/profile/picture/",U.ProfilePic)) AS ProfilePic',
                'PaymentGateway' => 'W.PaymentGateway',
                'PaytmPhoneNumber' => 'W.PaytmPhoneNumber',
                'EntryDate' => 'DATE_FORMAT(CONVERT_TZ(W.EntryDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") EntryDate',
                'Status' => 'CASE W.StatusID
                                                            when "1" then "Pending"
                                                            when "2" then "Processing"
                                                            when "3" then "Rejected"
                                                            when "5" then "Completed"
                                                        END as Status',
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('W.WithdrawalID,W.UserID');
        if (!empty($Field)) $this->db->select($Field, FALSE);
        $this->db->from('tbl_users_withdrawal W,tbl_users U');
        $this->db->where("W.UserID", "U.UserID", FALSE);

        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            // if (validateEmail($Where['Keyword'])) {
            //     $Where['Email'] = $Where['Keyword'];
            // } elseif (is_numeric($Where['Keyword'])) {
            //     $Where['PhoneNumber'] = $Where['Keyword'];
            // } else {
            $this->db->group_start();
            $this->db->like("U.FirstName", $Where['Keyword']);
            $this->db->or_like("U.LastName", $Where['Keyword']);
            $this->db->or_like("U.Email", $Where['Keyword']);
            $this->db->or_like("CONCAT_WS('',U.FirstName,U.Middlename,U.LastName)", preg_replace('/\s+/', '', $Where['Keyword']), FALSE);
            $this->db->group_end();
            // }
        }
        if (!empty($Where['WithdrawalID'])) {
            $this->db->where("W.WithdrawalID", $Where['WithdrawalID']);
        }
        if (!empty($Where['UserID'])) {
            $this->db->where("W.UserID", $Where['UserID']);
        }
        if (!empty($Where['PaymentGateway'])) {
            $this->db->where("W.PaymentGateway", $Where['PaymentGateway']);
        }
        if (!empty($Where['FromDate'])) {
            $this->db->where("W.EntryDate >=", $Where['FromDate']);
        }
        if (!empty($Where['ToDate'])) {
            $this->db->where("W.EntryDate <=", $Where['ToDate']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("W.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['ListType']) && $Where['ListType'] == 'Pending') {
            $this->db->where("W.StatusID =", 1);
        }
//        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
//            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
//        }
//        $this->db->order_by("W.StatusID", 1, true);
//        $this->db->order_by("W.StatusID=2", 'DESC');
//        $this->db->order_by("W.StatusID=5", 'DESC');
        $this->db->order_by('W.StatusID=1 DESC', null, FALSE);
        $this->db->order_by('W.StatusID=2 DESC', null, FALSE);
        $this->db->order_by('W.StatusID=5 DESC', null, FALSE);
        $this->db->order_by('W.WithdrawalID', 'DESC');

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
                foreach ($Query->result_array() as $Record) {
                    /* get attached media */
                    if (in_array('MediaBANK', $Params)) {
                        $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,"110_",M.MediaName) AS MediaThumbURL, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL,    M.MediaCaption', array("SectionID" => 'BankDetail', "EntityID" => $Record['UserID']), FALSE);
                        $MediaData['MediaCaption'] = json_decode($MediaData['MediaCaption']);
                        $Record['MediaBANK'] = ($MediaData ? $MediaData : new stdClass());
                    }

                    $Records[] = $Record;
                }
                $Return['Data']['Records'] = $Records;

                return $Return;
            } else {
                $Record = $Query->row_array();

                /* get attached media */
                if (in_array('MediaBANK', $Params)) {
                    $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,"110_",M.MediaName) AS MediaThumbURL, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL,M.MediaCaption', array("SectionID" => 5, "EntityID" => $Record['UserID']), FALSE);
                    if ($MediaData) $MediaData['MediaCaption'] = json_decode($MediaData['MediaCaption']);
                    $Record['MediaBANK'] = ($MediaData ? $MediaData : new stdClass());
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description: To user refer & earn
     */

    function referEarn($Input = array(), $SessionUserID) {

        $UserData = $this->Users_model->getUsers('FirstName,ReferralCode', array('UserID' => $SessionUserID));
        $InviteURL = SITE_HOST . ROOT_FOLDER . 'authenticate?referral=' . $UserData['ReferralCode'];

        if ($Input['ReferType'] == 'Email' && !empty($Input['Email'])) {

            /* Send referral Email to User with referral url */
            // sendMail(array(
            //     'emailTo' => $Input['Email'],
            //     'emailSubject' => "Refer & Earn - " . SITE_NAME,
            //     'emailMessage' => emailTemplate($this->load->view('emailer/refer_earn', array("Name" => $UserData['FirstName'], "ReferralCode" => $UserData['ReferralCode'], 'ReferralURL' => $InviteURL), TRUE))
            // ));

            send_mail(array(
                'emailTo' => $Input['Email'],
                'template_id' => 'd-e20ad55aa8fd4c59b0c314a0a0086311',
                'Subject' => 'Refer & Earn - ' . SITE_NAME,
                "Name" => $UserData['FirstName'],
                "ReferralCode" => $UserData['ReferralCode'],
                'ReferralURL' => $InviteURL
            ));
        } else if ($Input['ReferType'] == 'Phone' && !empty($Input['PhoneNumber'])) {

            /* Send referral SMS to User with referral url */
            $this->Utility_model->sendSMS(array(
                'PhoneNumber' => $Input['PhoneNumber'],
                'Text' => "Your Friend " . $UserData['FirstName'] . " just got registered with us and has referred you. Use his/her referral code: " . $UserData['ReferralCode'] . " Use the link provided to get " . DEFAULT_CURRENCY . REFERRAL_SIGNUP_BONUS . " signup bonus. " . $InviteURL
            ));
        }
    }

    /*
      Description: 	Use to update withdrawl status.
     */

    function updateWithdrawal($WithdrawalID, $Input = array()) {
        if ($Input['StatusID'] == 3) {
            $Comments = $Input['Comments'];
        } else {
            $Comments = '';
        }
        $UpdateArray = array_filter(array(
            "StatusID" => @$Input['StatusID'],
            "ModifiedDate" => date("Y-m-d H:i:s"),
            "Comments" => $Comments
        ));

        if (!empty($UpdateArray)) {
            /* Update entity Data. */
            $this->db->select('U.UserID,U.FirstName,U.Email,U.WinningAmount,U.WithdrawalHoldAmount');
            $this->db->select('W.Amount,W.WalletID');
            $this->db->where('W.WithdrawalID', $WithdrawalID);
            $this->db->from('tbl_users_withdrawal W');
            $this->db->join('tbl_users U', 'W.UserID = U.UserID');

            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                $UserData = $Query->row();
            }
            if (!empty($UserData->WalletID)) {
                $this->db->where('WalletID', $UserData->WalletID);
                $this->db->limit(1);
                $this->db->update('tbl_users_wallet', array('StatusID' => $Input['StatusID']));
            }
            if (@$Input['StatusID'] == 2) {

                $this->Notification_model->addNotification('Withdrawal', 'Withdrawal Request Approved', $UserData->UserID, $UserData->UserID, '', 'Your withdrawal request for ' . DEFAULT_CURRENCY . $UserData->Amount . ' has been approved by admin and will be transferred to your given account details within 3-4 working days.');
                /* Send welcome Email to User with login details */
                send_mail(array(
                    'emailTo' => $UserData->Email,
                    'template_id' => 'd-cca9f427f5c44f8d831ab1710c0b4d47',
                    'Subject' => 'Withdrawal Request Confirmed - ' . SITE_NAME,
                    "Name" => $UserData->FirstName,
                    "Amount" => $UserData->Amount
                ));
            } else if (@$Input['StatusID'] == 5) {

                /* Updating Hold Amount */
                $VerifiedData = array(
                    'WithdrawalHoldAmount' => 0
                );
                $this->db->where('UserID', $UserData->UserID);
                $this->db->limit(1);
                $this->db->update('tbl_users', $VerifiedData);

                $this->Notification_model->addNotification('Withdrawal', 'Withdrawal Request Processed', $UserData->UserID, $UserData->UserID, '', 'Your withdrawal request for ' . DEFAULT_CURRENCY . $UserData->Amount . ' has been processed successfully.');
            } else if (@$Input['StatusID'] == 3) {
                /* Updating Hold Amount */
                $VerifiedData = array(
                    'WithdrawalHoldAmount' => 0
                );
                $this->db->where('UserID', $UserData->UserID);
                $this->db->limit(1);
                $this->db->update('tbl_users', $VerifiedData);

                $this->Notification_model->addNotification('Withdrawal', 'Withdrawal Request Declined', $UserData->UserID, $UserData->UserID, '', 'Your withdrawal request for ' . DEFAULT_CURRENCY . $UserData->Amount . ' has been declined by admin for ' . $Comments);
                $WalletData = array(
                    "Amount" => $UserData->Amount,
                    "WinningAmount" => $UserData->Amount,
                    "TransactionType" => 'Cr',
                    "Narration" => 'Withdrawal Reject',
                    "EntryDate" => date("Y-m-d H:i:s"),
                    "WithdrawalStatus" => 3
                );
                $this->addToWallet($WalletData, $UserData->UserID, 5);
            }
            $this->db->where('WithdrawalID', $WithdrawalID);
            $this->db->limit(1);
            $this->db->update('tbl_users_withdrawal', $UpdateArray);
        }

        /* add event attributes */
        //$this->addEntityAttributes($EntityID,@$Input['Attributes']);
        return TRUE;
    }



    /*
      Description: To add data into user wallet
     */

    function addToWalletToWithdrawals($Input = array(), $UserID, $StatusID = 1) {
        $this->db->trans_start();

        $UserWalletAmount = $this->getUserWalletOpeningBalanceWithdrawals($UserID, 'ClosingWalletAmount');
        $OpeningWalletAmount = $UserWalletAmount['WalletAmount'];
        $OpeningWinningAmount = $UserWalletAmount['WinningAmount'];
        $OpeningCashBonus = $UserWalletAmount['CashBonus'];
        $InsertData = array_filter(array(
            "UserID" => $UserID,
            "Amount" => @$Input['Amount'],
            "OpeningWalletAmount" => $OpeningWalletAmount,
            "OpeningWinningAmount" => $OpeningWinningAmount,
            "OpeningCashBonus" => $OpeningCashBonus,
            "WalletAmount" => @$Input['WalletAmount'],
            "WinningAmount" => @$Input['WinningAmount'],
            "CashBonus" => @$Input['CashBonus'],
            "SportsType" => @$Input['SportsType'],
            "ClosingWalletAmount" => $OpeningWalletAmount,
            "ClosingWinningAmount" => ($StatusID == 5) ? (@$Input['TransactionType'] == 'Cr') ? $OpeningWinningAmount + @$Input['WinningAmount'] : $OpeningWinningAmount - @$Input['WinningAmount'] : $OpeningWinningAmount,
            "ClosingCashBonus" => ($StatusID == 5) ? (($OpeningCashBonus != 0) ? ((@$Input['TransactionType'] == 'Cr') ? $OpeningCashBonus + @$Input['CashBonus'] : $OpeningCashBonus - @$Input['CashBonus'] ) : @$Input['CashBonus']) : $OpeningCashBonus,
            "Currency" => @$Input['Currency'],
            "CouponCode" => @$Input['CouponCode'],
            "PaymentGateway" => @$Input['PaymentGateway'],
            "TransactionType" => @$Input['TransactionType'],
            "TransactionID" => (!empty($Input['TransactionID'])) ? $Input['TransactionID'] : substr(hash('sha256', mt_rand() . microtime()), 0, 20),
            "Narration" => @$Input['Narration'],
            "EntityID" => @$Input['EntityID'],
            "UserTeamID" => @$Input['UserTeamID'],
            "CouponDetails" => @$Input['CouponDetails'],
            "ReferralGetAmountUserID" => @$Input['ReferralGetAmountUserID'],
            "AmountType" => @$Input['AmountType'],
            "PaymentGatewayResponse" => @$Input['PaymentGatewayResponse'],
            "EntryDate" => date("Y-m-d H:i:s"),
            "StatusID" => $StatusID
        ));
        $this->db->insert('tbl_users_wallet', $InsertData);
        $WalletID = $this->db->insert_id();

        /* Update User Balance */
        if ($StatusID == 5 || $Input['Narration'] == 'Withdrawal Request') {
            switch (@$Input['Narration']) {
                case 'Deposit Money':
                case 'Admin Deposit Money':
                    $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                    break;
                case 'Join Contest Winning':
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Join Contest Winning Bonus':
                    $this->db->set('CashBonus', 'CashBonus+' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Join Contest':
                    $this->db->set('WalletAmount', 'WalletAmount-' . @$Input['WalletAmount'], FALSE);
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    $this->db->set('CashBonus', 'CashBonus-' . @$Input['CashBonus'], FALSE);
                    break;
                case 'Cancel Contest':
                    $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['WalletAmount'], FALSE);
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    $this->db->set('CashBonus', 'CashBonus+' . @$Input['CashBonus'], FALSE);
                    break;
                case 'Wrong Winning Distribution':
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Referral Deposit':
                    if ($Input['WinningAmount'] > 0) {
                        $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    }
                    break;
                case 'Lucky Wheel':
                case 'Signup Bonus':
                case 'Verification Bonus':
                case 'First Deposit Bonus':
                case 'Referral Bonus':
                case 'Admin Cash Bonus':
                case 'Coupon Discount Bonus':
                    if ($Input['CashBonus'] > 0) {
                        $this->db->set('CashBonus', 'CashBonus+' . @$Input['Amount'], FALSE);
                    }
                    break;
                case 'Coupon Discount':
                case 'Signup Direct Deposit':
                    if ($Input['WalletAmount'] > 0) {
                        $this->db->set('WalletAmount', 'WalletAmount+' . @$Input['Amount'], FALSE);
                    }
                    break;
                case 'Withdrawal Request':
                    $this->db->set('WinningAmount', 'WinningAmount-' . @$Input['WinningAmount'], FALSE);
                    if (@$Input['WithdrawalStatus'] == 1) {
                        $this->db->set('WithdrawalHoldAmount', 'WithdrawalHoldAmount+' . @$Input['WinningAmount'], FALSE);
                    }
                    break;
                case 'Withdrawal Reject':
                    $this->db->set('WinningAmount', 'WinningAmount+' . @$Input['WinningAmount'], FALSE);
                    //$this->db->set('WithdrawalHoldAmount', 'WithdrawalHoldAmount-' . @$Input['WinningAmount'], FALSE);
                    break;
                case 'Cash Bonus Expire':
                    $this->db->set('CashBonus', 'CashBonus-' . @$Input['Amount'], FALSE);
                    break;
                default:
                    break;
            }
            $this->db->where('UserID', $UserID);
            $this->db->limit(1);
            $this->db->update('tbl_users');
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $WalletID;
    }

    /*
      Description: To get user wallet opening balance
     */

    function getUserWalletOpeningBalanceWithdrawals($UserID) {
        return $this->db->query('SELECT WalletAmount,WinningAmount,CashBonus FROM `tbl_users` WHERE `UserID` = ' . $UserID . ' LIMIT 1')->row_array();
    }

    /*
      Description:  Use get referrals users
     */

    function getUserReferrals($UserID) {
        $Return = array();
        $this->db->select('UserID');
        $this->db->select('ReferredByUserID');
        $this->db->from('tbl_users');
        $this->db->where('ReferredByUserID', $UserID);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return['TotalRecords'] = $Query->num_rows();
            $Return['Records'] = $Query->result_array();
        }
        return $Return;
    }

    /*
      Description:  Use get referrals users next level
     */

    function getUserReferralBy($UserID) {
        $Return = array();
        $this->db->select('UserID');
        $this->db->select('ReferredByUserID');
        $this->db->from('tbl_users');
        $this->db->where('UserID', $UserID);
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return['Records'] = $Query->row_array();
        }
        return $Return;
    }

    /*
      Description:  Use get referrals users total winning.
     */

    function getUserReferralByTotalWinning($UserIDs, $UserID = "", $N = 1) {
        $Return = array();
        $this->db->select('SUM(Amount) as WinningAmount');
        $this->db->from('tbl_users_wallet');
        $this->db->where('ReferralGetAmountUserID', $UserIDs);
        $this->db->where('AmountType', "Referral");
        if (!empty($UserID)) {
            $this->db->where('UserID', $UserID);
        }
        $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            $Return['Records'] = $Query->row_array();
        }
        return $Return;
    }

    function checkReferral($Referral, $UserID) {
        $Check = $this->db->query("SELECT * FROM tbl_referral_codes Where UserID != $UserID AND ReferralCode = '" . $Referral . "'")->row();
        if (empty($Check)) {
            $this->db->query("UPDATE tbl_referral_codes SET ReferralCode='" . $Referral . "' WHERE UserID = $UserID");
            return true;
        } else {
            return false;
        }
    }

        /*
      Description:  ADD user to system.
      Procedures:
      1. Add user to user table and get UserID.
     */

    function addLoginLogs($Input = array()) {
        /* check validation mobile. */
        if(!empty(@$Input['PhoneNumber']) && @$Input['LogType'] != "Signup"){
            $this->db->select('LogID,Counter');
            $this->db->from('tbl_users_logs');
            $this->db->where('PhoneNumber', $Input['PhoneNumber']);
            $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
            $this->db->where('LogType', @$Input['LogType']);
            $this->db->limit(1);
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                $Log=$Query->row_array();

                $this->db->set('Counter', 'Counter+1', FALSE);
                $this->db->where('PhoneNumber', $Input['PhoneNumber']);
                $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
                $this->db->where('LogType', @$Input['LogType']);
                $this->db->limit(1);
                $this->db->update('tbl_users_logs');
            }else{
                /* Save login info to users_login table. */
                $InsertData = array_filter(array(
                    "UserID" => @$Input['UserID'],
                    "PhoneNumber" => @$Input['PhoneNumber'],
                    "Email" => @$Input['Keyword'],
                    "Counter" => 1,
                    "IP" => $_SERVER["REMOTE_ADDR"],
                    "LogType" => @$Input['LogType'],
                    "EntryDate" => date("Y-m-d H:i:s")));
                $this->db->insert('tbl_users_logs', $InsertData);
            }  
        }
        /* check validation email. */
        if(!empty(@$Input['Keyword']) && @$Input['LogType'] != "Signup"){
            $this->db->select('LogID,Counter');
            $this->db->from('tbl_users_logs');
            $this->db->where('Email', $Input['Keyword']);
            $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
            $this->db->where('LogType', @$Input['LogType']);
            $this->db->limit(1);
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                $Log=$Query->row_array();

                $this->db->set('Counter', 'Counter+1', FALSE);
                $this->db->where('Email', $Input['Keyword']);
                $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
                $this->db->where('LogType', @$Input['LogType']);
                $this->db->limit(1);
                $this->db->update('tbl_users_logs');
            }else{
                /* Save login info to users_login table. */
                $InsertData = array_filter(array(
                    "UserID" => @$Input['UserID'],
                    "PhoneNumber" => @$Input['PhoneNumber'],
                    "Email" => @$Input['Keyword'],
                    "Counter" => 1,
                    "IP" => $_SERVER["REMOTE_ADDR"],
                    "LogType" => @$Input['LogType'],
                    "EntryDate" => date("Y-m-d H:i:s")));
                $this->db->insert('tbl_users_logs', $InsertData);
            }  
        }

        /* check validation mobile. */
        if($Input['LogType'] == "Signup"){
            $this->db->select('LogID,Counter');
            $this->db->from('tbl_users_logs');
            $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
            $this->db->where('LogType', @$Input['LogType']);
            $this->db->limit(1);
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                $Log=$Query->row_array();

                $this->db->set('Counter', 'Counter+1', FALSE);
                $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
                $this->db->where('LogType', @$Input['LogType']);
                $this->db->limit(1);
                $this->db->update('tbl_users_logs');
            }else{
                /* Save login info to users_login table. */
                $InsertData = array_filter(array(
                    "Counter" => 1,
                    "IP" => $_SERVER["REMOTE_ADDR"],
                    "LogType" => @$Input['LogType'],
                    "EntryDate" => date("Y-m-d H:i:s")));
                $this->db->insert('tbl_users_logs', $InsertData);
            }  
        }
        return true;
    }

         /*
      Description:  ADD user to system.
      Procedures:
      1. Add user to user table and get UserID.
     */
    function getLoginLogs($Input = array()) {
        $Validation=3;
        /* check validation mobile. */
        
            $this->db->select('LogID,Counter');
            $this->db->from('tbl_users_logs');

            if(!empty(@$Input['PhoneNumber'])){
             $this->db->where('PhoneNumber', $Input['PhoneNumber']);
            }

            if(!empty(@$Input['Keyword'])){
             $this->db->where('Email', $Input['Keyword']);
            }

            $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
            $this->db->where('LogType', $Input['LogType']);
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                $Log=$Query->row_array();
                if($Log['Counter'] >= $Validation){
                    return true; 
                }
            }

        return false;
    }

             /*
      Description:  ADD user to system.
      Procedures:
     */
    function verifyCaptcha($Input = array(),$Message="") {
            $Return = array();
            $Return['ResponseCode'] = 200;
            // check captcha
            if(empty($Input['g-recaptcha-response'])){
                $Return['ResponseCode'] = 500;
                $Return['Message'] = $Message;
            }
            if(isset($Input['g-recaptcha-response']) && !empty($Input['g-recaptcha-response']))
            {
                $secret = '6Le6FekUAAAAADYFqWZPlys5aPxatKZw5divqopP';
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$Input['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if(!$responseData->success)
                {
                    $Return['ResponseCode'] = 500;
                    $Return['Message'] = "Invalid Captcha";
                }          
            }
            return $Return;
    }


    /*
      Description:  ADD user to system.
      Procedures:
      1. Add user to user table and get UserID.
     */
    function deleteLogs($Input = array()) {
            /* check validation mobile. */

            if(!empty(@$Input['PhoneNumber'])){
             $this->db->where('PhoneNumber', $Input['PhoneNumber']);
            }

            if(!empty(@$Input['Keyword'])){
             $this->db->where('Email', $Input['Keyword']);
            }

            $this->db->where('IP', $_SERVER["REMOTE_ADDR"]);
            $this->db->where('LogType', $Input['LogType']);
            $this->db->delete('tbl_users_logs');

            return true;
    }

        /*
      Description:  ADD user to system.
      Procedures:
      1. Add user to user table and get UserID.
     */
    function getLoginLogsAll($Input = array()) {
            /* check validation mobile. */
        
            $this->db->select('LogID,Counter');
            $this->db->from('tbl_users_logs');

            $now = date("Y-m-d H:i:s");
            $date = date("Y-m-d H:i:s", strtotime('-2 hours', strtotime($now)));

            $this->db->where('EntryDate <', $date);   
            
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                return $Query->result_array();
            }

            return false;
    }

}
