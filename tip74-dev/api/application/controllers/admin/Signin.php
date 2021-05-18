<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Admin_model');
        $this->load->model('Recovery_model');
        $this->load->model('Utility_model');
    }

    /*
      Description: 	Verify login and activate session
      URL: 			/api_admin/signin/
     */

    public function index_post() {
        /* Validation section */
        $this->form_validation->set_rules('Username', 'Username', 'trim' . (empty($this->Post['Source']) || $this->Post['Source'] == 'Direct' ? '|required' : ''));
        $this->form_validation->set_rules('Password', 'Password', 'trim|required');
        $this->form_validation->set_rules('Source', 'Source', 'trim|required|callback_validateSource');
        $this->form_validation->set_rules('DeviceType', 'Device type', 'trim|required|callback_validateDeviceType');
        $this->form_validation->set_rules('IPAddress', 'IPAddress', 'trim|callback_validateIP');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */
        // print_r($this->Post);
        if(empty($this->Post['g-recaptcha-response']) && ENVIRONMENT == "production"){
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Please verify captcha";
            exit;
        }

        $UserData = $this->Users_model->getUsers('UserTypeID,UserID,IsAdmin,FirstName,PhoneNumber,LastName,Email,StatusID,ProfilePic', array('LoginKeyword' => @$this->Post['Username'], 'Password' => $this->Post['Password'], 'SourceID' => $this->SourceID));
        if (!$UserData) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Invalid login credentials.";
        } elseif ($UserData && $UserData['StatusID'] == 1) {
            $this->Return['ResponseCode'] = 501;
            $this->Return['Message'] = "You have not activated your account yet, please verify your email address first.";
        } elseif ($UserData && $UserData['StatusID'] == 3) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Your account has been deleted. Please contact the Admin for more info.";
        } elseif ($UserData && $UserData['StatusID'] == 4) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Your account has been blocked. Please contact the Admin for more info.";
        } elseif ($UserData && $UserData['StatusID'] == 5) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "You have deactivated your account, please contact the Admin to reactivate.";
        } elseif ($UserData && $UserData['IsAdmin'] == 'No') {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Access restricted.";
        } else {

            if (!isset($this->Post['MobileOTP']) && $UserData['UserTypeID'] == 1) {
                $Token = $this->Recovery_model->generateToken($UserData['UserID'], 3);
                if (ENVIRONMENT == 'production') {
                    $this->Utility_model->sendMobileSMS(array(
                        'PhoneNumber' => "",
                        'Text' => $Token
                    ));
                    $this->Return['ResponseCode'] = 200;
                    $this->Return['Message'] = "Otp sent to your mobile number";
                } else {
                    $this->Return['ResponseCode'] = 200;
                    $this->Return['Message'] = "Otp sent to your mobile number " . $Token;
                }
            }
            if (isset($this->Post['MobileOTP'])) {
                $UserID = $this->Recovery_model->verifyToken($this->Post['MobileOTP'], 3);
                if (!$UserID) {
                    $this->Return['ResponseCode'] = 500;
                    $this->Return['Message'] = "Invalid OTP";
                    return false;
                }
                // check captcha 
                if(ENVIRONMENT == 'production' && isset($this->Post['g-recaptcha-response']) && !empty($this->Post['g-recaptcha-response']))
                {
                    $secret = '';
                    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$this->Post['g-recaptcha-response']);
                    $responseData = json_decode($verifyResponse);
                    if($responseData->success)
                    {
                        $this->Return['ResponseCode'] = 200;
                        $this->Return['CaptchaStatus'] = true;
                        $this->Return['Message'] = "Success";
                    }
                    else
                    {   
                        $this->Return['ResponseCode'] = 500;
                        $this->Return['CaptchaStatus'] = false;
                        $this->Return['Message'] = "Invalid Captcha";
                        exit;
                    }            
                }
            }
            /* Create Session */
            $UserData['SessionKey'] = $this->Users_model->createSession($UserData['UserID'], array(
                //"IPAddress"	=>	@$this->Post['IPAddress'],
                "SourceID" => $this->SourceID,
                "DeviceTypeID" => $this->DeviceTypeID,
                    //"DeviceGUID"	=>	(empty($this->Post['DeviceGUID']) ? '' : $this->Post['DeviceGUID']),
                    //"DeviceToken"	=>	@$this->Post['DeviceToken'],
                    //"Latitude"	=>	@$this->Post['Latitude'],
                    //"Longitude"	=>	@$this->Post['Longitude']
            ));

            /* Get Permitted Modules */
            $UserData['PermittedModules'] = $this->Admin_model->getPermittedModules($UserData['UserTypeID']);
            $UserData['Menu'] = $this->Admin_model->getMenu($UserData['UserTypeID']);
            $this->Return['Data'] = $UserData;
        }
    }

    public function SentOtp_post() {
        /* Validation section */
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $UserData = $this->Users_model->getUsers('UserID,FirstName,LastName,Email,StatusID,PhoneNumber', array('UserID' => $this->SessionUserID));
        if ($UserData) {
            $Token = $this->Recovery_model->generateToken($UserData['UserID'], 3);
            $this->Utility_model->sendMobileSMS(array(
                'PhoneNumber' => $UserData['PhoneNumber'],
                'Text' => $Token
            ));
            $this->Return['Data'] = $UserData;
            $this->Return['UserGUID'] = $this->Post['UserGUID'];
            $this->Return['Message'] = $Token;
        } else if (!$UserData) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Phone number not found";
        }
    }

    public function VerifyOtp_post() {
        /* Validation section */
        $this->form_validation->set_rules('OTP', 'OTP', 'trim|required');
        $this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */
        $UserID = $this->Recovery_model->verifyToken($this->Post['OTP'], 3);
        if (!empty($UserID)) {
            if ($UserID == $this->SessionUserID) {
                $this->Return['Message'] = "Authentication Verified";
            } else {
                $this->Return['ResponseCode'] = 500;
                $this->Return['Message'] = "Authentication Failed! Invalid OTP";
            }
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Authentication Failed! Invalid OTP";
        }
    }

}
