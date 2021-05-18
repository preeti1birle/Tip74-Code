<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recovery extends API_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Recovery_model');
	}

	/*
	Name: 			Recovery Password
	Description: 	Use to set OTP and send to user for password recovery.
	URL: 			/api/recovery
	*/
	public function index_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('Keyword', 'Keyword', 'trim|required'. ($this->input->post('type' == 'Phone' ? '|numeric':'')));
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		
		// check captcha
		/*if(empty($this->Post['g-recaptcha-response'])){
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Please verify captcha";
            exit;
        }
        // check captcha
        if(isset($this->Post['g-recaptcha-response']) && !empty($this->Post['g-recaptcha-response']))
        {
            $secret = '6Le6FekUAAAAADYFqWZPlys5aPxatKZw5divqopP';
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$this->Post['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if($responseData->success)
            {
                $this->Return['ResponseCode'] = 200;
                $this->Return['Message'] = "Success";
            }
            else
            {   
                $this->Return['ResponseCode'] = 500;
                $this->Return['Message'] = "Invalid Captcha";
                exit;
            }            
        }*/


		$Recover = $this->Recovery_model->recovery($this->Post['Keyword']);

		if($Recover != false){

			$this->Return['ResponseCode'] 	=	200;
			$this->Return['Message']      	=	$Recover; 
		}else{
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"Please enter your registered email / Phone number.";
		}
	}

	/*
	Name: 			Set Password From OTP
	Description: 	Use to set user password from OTP.
	URL: 			/api/recovery/setPassword
	*/
	public function setPassword_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('OTP', 'OTP', 'trim|required|callback_validateToken[1]');
		$this->form_validation->set_rules('Password', 'Password', 'trim|required|min_length[6]|max_length[25]|callback_is_password_strong');
		//$this->form_validation->set_rules('Retype', 'Confirm Password', 'trim|matches[Password]');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$UserID=$this->Recovery_model->verifyToken($this->Post['OTP'],1);
		if($this->Users_model->updateUserLoginInfo($UserID, array("Password"=>$this->Post['Password']), DEFAULT_SOURCE_ID)){
			$this->Recovery_model->deleteSession($this->Post['OTP'],1);
			$this->Recovery_model->deleteToken($this->Post['OTP'],1); /*delete token*/
			$this->Return['Message']      	=	"New password has been set, please login to get access your account."; 	
		}
	}

	public function is_password_strong($password)
    {
       $password = trim($password);

        $regex_lowercase = '/[a-z]/';
        $regex_uppercase = '/[A-Z]/';
        $regex_number = '/[0-9]/';
        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

        if (empty($password))
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field is required.');

            return FALSE;
        }

        if (preg_match_all($regex_lowercase, $password) < 1)
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field must be at least one lowercase letter.');

            return FALSE;
        }

        if (preg_match_all($regex_uppercase, $password) < 1)
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field must be at least one uppercase letter.');

            return FALSE;
        }

        if (preg_match_all($regex_number, $password) < 1)
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field must have at least one number.');

            return FALSE;
        }

        if (preg_match_all($regex_special, $password) < 1)
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));

            return FALSE;
        }

        if (strlen($password) < 6)
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field must be at least 5 characters in length.');

            return FALSE;
        }

        if (strlen($password) > 32)
        {
            $this->form_validation->set_message('is_password_strong', 'The {field} field cannot exceed 32 characters in length.');

            return FALSE;
        }

        return TRUE;
    }


}
