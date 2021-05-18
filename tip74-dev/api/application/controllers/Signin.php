<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Recovery_model');
    }

    /*
      Name: 			Login
      Description: 	Verify login and activate session
      URL: 			/api/signin/
     */

    public function index_post() {
        /* Validation section */
        $this->form_validation->set_rules('Keyword', 'Keyword', 'trim' . (empty($this->Post['Source']) || $this->Post['Source'] == 'Direct' ? '|required' : ''));
        $this->form_validation->set_rules('Password', 'Password', 'trim' . (empty($this->Post['Source']) || $this->Post['Source'] == 'Direct' ? '|required' : ''));

        $this->form_validation->set_rules('PhoneNumber', 'PhoneNumber', 'trim' . (empty($this->Post['Source']) || $this->Post['Source'] == 'Otp' ? '|required' : ''));

        $this->form_validation->set_rules('OTP', 'OTP', 'trim' . (empty($this->Post['Source']) || $this->Post['Source'] == 'Otp' ? '|required' : ''));

        $this->form_validation->set_rules('Source', 'Source', 'trim|required|callback_validateSource');
        $this->form_validation->set_rules('DeviceType', 'Device type', 'trim|required|callback_validateDeviceType');
        $this->form_validation->set_rules('DeviceGUID', 'DeviceGUID', 'trim');
        $this->form_validation->set_rules('DeviceToken', 'DeviceToken', 'trim');
        $this->form_validation->set_rules('IPAddress', 'IPAddress', 'trim|callback_validateIP');
        $this->form_validation->set_rules('Latitude', 'Latitude', 'trim');
        $this->form_validation->set_rules('Longitude', 'Longitude', 'trim');

        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!empty($this->Post['PhoneNumber']) && !empty($this->Post['OTP'])) {
            $UserID = $this->Recovery_model->verifyToken($this->Post['OTP'], 3);
            if ($UserID) {
                $UserData = $this->Users_model->getUsers('Username,PhoneStatus,UserTypeID,UserID,FirstName,MiddleName,LastName,Email,StatusID,ProfilePic,PhoneNumber,WalletAmount,ReferralCode,TotalCash', array('UserID' => $UserID));
            }else{
                $this->Return['ResponseCode'] = 500;
                $this->Return['Message'] = "Invalid OTP";
                return false;
            }
        } else {
            $UserData = $this->Users_model->getUsers('Username,PhoneStatus,UserTypeID,UserID,FirstName,MiddleName,LastName,Email,StatusID,ProfilePic,PhoneNumber,WalletAmount,ReferralCode,TotalCash', array('LoginKeyword' => @$this->Post['Keyword'], 'Password' => $this->Post['Password'], 'SourceID' => $this->SourceID));
        }

        if(!$UserData){
            $Query = $this->db->query("SELECT * FROM `tbl_users` WHERE `EmailForChange` = '".@$this->Post['Keyword']."' AND `PhoneNumber` IS NOT NULL LIMIT 1");
            if ($Query->num_rows() > 0) {
                $IsEmailForChange = TRUE;
            }
        }

        if(!$UserData && !isset($IsEmailForChange)){
             $this->Post['LogType'] = "Login";
             $this->Users_model->addLoginLogs($this->Post);
        }
        /** cpatcha validation check **/
        /*$this->Return['CaptchaEnable'] = "No";
        $this->Post['LogType'] = "Login";
        $IsValidCaptcha=$this->Users_model->getLoginLogs($this->Post);
        if($IsValidCaptcha){
            $this->Return['CaptchaEnable'] = "Yes";
            $VerifyCaptcha= $this->Users_model->verifyCaptcha($this->Post,"Invalid login credentials.");
            if($VerifyCaptcha['ResponseCode'] == 500){
                $this->Return['ResponseCode']   =   201;
                $this->Return['Message']        =   $VerifyCaptcha['Message'];
                exit;
            }
        }*/
        if(!$UserData && !isset($IsEmailForChange)){
            $this->Return['ResponseCode']   =   500;
            $this->Return['Message']        =   "Invalid login credentials.";
        }elseif(!$UserData && isset($IsEmailForChange)){
            $this->Return['ResponseCode']   =   500;
            $this->Return['Message']        =   "Please login using your phone number, your email is not verified.";
        } elseif ($UserData && $UserData['StatusID'] == 1) {
            $this->Return['ResponseCode'] = 501;
            $this->Return['Message'] = "You have not activated your account yet, please verify your email address first.";
        } elseif ($UserData && $UserData['StatusID'] == 3) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Your account has been deleted. Please contact the Admin for more info.";
        } elseif ($UserData && $UserData['StatusID'] == 4) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Your account has been blocked. Please contact the Admin for more info.";
        } elseif ($UserData && $UserData['StatusID'] == 6) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "You have deactivated your account, please contact the Admin to reactivate.";
        }
        /*elseif($UserData && isset($OtherLoginSourcesExists)){
            $this->Return['ResponseCode']   =   500;
            $this->Return['Message']        =   "You have registered using ".$SourcesArray[0]['SourceName']." option. Please Signin with ".$SourcesArray[0]['SourceName'];
        }*/
        else {
            /* Create Session */
            $UserData['SessionKey'] = $this->Users_model->createSession($UserData['UserID'], array(
                "IPAddress" => @$this->Post['IPAddress'],
                "SourceID" => $this->SourceID,
                "PhoneStatus" => $UserData['PhoneStatus'],
                "DeviceTypeID" => $this->DeviceTypeID,
                "DeviceGUID" => @$this->Post['DeviceGUID'],
                "DeviceToken" => @$this->Post['DeviceToken'],
                "Latitude" => @$this->Post['Latitude'],
                "Longitude" => @$this->Post['Longitude']
            ));
            $this->Return['Data'] = $UserData;

            $this->Post['LogType'] = "Login";
            $this->Users_model->deleteLogs($this->Post);
        }
        
        /* unset output parameters */
        unset($this->Return['Data']->UserID);
        unset($this->Return['Data']->StatusID);
        /* unset output parameters - ends */
    }

    /*
      Name: 			OTP for sigin
      Description: 	Signin using OTP
      URL: 			/api/signin/OtpSignIn/
     */

    public function OtpSignIn_post() {
        /* Validation section */
        $this->form_validation->set_rules('PhoneNumber', 'PhoneNumber', 'trim' . (empty($this->Post['Source']) || $this->Post['Source'] == 'Otp' ? '|required' : ''));
        $this->form_validation->set_rules('Source', 'Source', 'trim|required|callback_validateSource');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $UserData = $this->Users_model->getUsers('UserID,FirstName,LastName,Email,StatusID,PhoneNumber', array('PhoneNumber' => @$this->Post['PhoneNumber']));
        if ($UserData) {
            $Token = $this->Recovery_model->generateToken($UserData['UserID'], 3);
            $this->Utility_model->sendMobileSMS(array(
                'PhoneNumber' => $UserData['PhoneNumber'],
                'Text' => $Token
            ));
            $this->Return['Data'] = $UserData;
        } else if (!$UserData) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Phone number not found";
        }
    }

    /*
      Name: 		Logout
      Description: 	Delete session
      URL: 			/api/signin/signout/
     */

    public function signout_post() {
        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Users_model->deleteSession($this->Post['SessionKey']); /* Delete session */
    }

}
