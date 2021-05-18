<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MAIN_Controller {

    public function index() {
        echo "This is a sample page.";
    }

    public function logs() {
        $this->load->library('logviewer');
        $this->load->view('logs', $this->logviewer->get_logs());
    }

    public function upload() {
        $this->load->view('upload');
    }

    public function paytmResponse() {
        mongoDBConnection();
        $this->load->model('Users_model');

        /* Get User ID */
        $UserID = $this->db->query('SELECT `UserID` FROM `tbl_users_wallet` WHERE `WalletID` = ' . $_POST["ORDERID"] . ' LIMIT 1')->row()->UserID;
        $PaymentResponse = array();
        $PaymentResponse['WalletID'] = $_POST["ORDERID"];
        $PaymentResponse['PaymentGatewayResponse'] = json_encode($_POST);

        $Insert = $this->fantasydb->payment_logs_paytm->insertOne(array(
            'PaymentType' => "Paytm",
            'Response' => $_POST,
            'EntryDate' => date('Y-m-d H:i:s'),
        ));

        if ($_POST["STATUS"] == "TXN_FAILURE") {

            /* Update Transaction */
            $PaymentResponse['PaymentGatewayStatus'] = 'Failed';
            $this->Users_model->confirm($PaymentResponse, $UserID);
            redirect(SITE_HOST . ROOT_FOLDER . 'myAccount?status=failed');
        } else {

            /* Verify Transaction */
            $IsValidCheckSum = $this->Users_model->verifychecksum_e($_POST, PAYTM_MERCHANT_KEY, $_POST['CHECKSUMHASH']);
            if ($IsValidCheckSum == "TRUE" && $_POST["STATUS"] == "TXN_SUCCESS") {

                /* Update Transaction */
                $PaymentResponse['PaymentGatewayStatus'] = 'Success';
                $PaymentResponse['Amount'] = $_POST['TXNAMOUNT'];
                $this->Users_model->confirm($PaymentResponse, $UserID);
                redirect(SITE_HOST . ROOT_FOLDER . 'myAccount?status=success');
            } else {

                /* Update Transaction */
                $PaymentResponse['PaymentGatewayStatus'] = 'Failed';
                $this->Users_model->confirm($PaymentResponse, $UserID);
                redirect(SITE_HOST . ROOT_FOLDER . 'myAccount?status=failed');
            }
        }
    }

}
