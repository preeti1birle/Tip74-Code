<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends Admin_Controller {
    /* ------------------------------ */
    /* ------------------------------ */

    public function index() {
        // print_r($this->Post);exit;
        if (!empty($this->Post)) {
            $JSON = json_encode(array(
                "Username" => @$this->Post['Username'],
                "Password" => @$this->Post['Password'],
                "Source" => 'Direct',
                "DeviceType" => 'Native',
                "g-recaptcha-response" => @$this->Post['g-recaptcha-response']
            ));

            $Response = APICall(API_URL . 'admin/signin', $JSON); /* call API and get response */
            if ($Response['ResponseCode'] == 200) { /* check for admin type user */
                if ($Response['Data']['UserTypeID'] != 1) {
                    $this->session->set_userdata('UserData', $Response['Data']); /* Set data in PHP session */
                }
            }
            response($Response);
            exit;
        }

        /* load view */
        $load['js'] = array(
            'asset/js/signin.js'
        );
        $this->load->view('includes/header', $load);
        $this->load->view('signin/signin');
        $this->load->view('includes/footer');
    }

    public function otp() {
        if (!empty($this->Post)) {
            $JSON = json_encode(array(
                "Username" => @$this->Post['Username'],
                "Password" => @$this->Post['Password'],
                "MobileOTP" => @$this->Post['MobileOTP'],
                "Source" => 'Direct',
                "DeviceType" => 'Native',
                "g-recaptcha-response" => @$this->Post['g-recaptcha-response']
            ));
            $Response = APICall(API_URL . 'admin/signin', $JSON); /* call API and get response */
            if ($Response['ResponseCode'] == 200) { /* check for admin type user */
                $this->session->set_userdata('UserData', $Response['Data']); /* Set data in PHP session */
            }
            response($Response);
            exit;
        }

        /* load view */
        $load['js'] = array(
            'asset/js/signin.js'
        );
        $this->load->view('includes/header', $load);
        $this->load->view('signin/signin');
        $this->load->view('includes/footer');
    }

}
