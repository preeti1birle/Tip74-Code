<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends API_Controller_Secure {

    function __construct() {
        parent::__construct();
        $this->load->model('Users_model');
    }

    /*
      Name:             add
      Description:  Use to add wallet cash
      URL:          /wallet/add/
     */

    public function add_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('RequestSource', 'RequestSource', 'trim|required|in_list[Web,Mobile]');
        $this->form_validation->set_rules('CouponGUID', 'CouponGUID', 'trim|callback_validateEntityGUID[Coupon,CouponID]');
        $this->form_validation->set_rules('PaymentGateway', 'PaymentGateway', 'trim|required|in_list[Stripe]');
        $this->form_validation->set_rules('Amount', 'Amount', 'trim|required|numeric|callback_validateMinimumDepositAmount');
        $this->form_validation->set_rules('FirstName', 'FirstName', 'trim');
        $this->form_validation->set_rules('Email', 'Email', 'trim|valid_email');
        $this->form_validation->set_rules('PhoneNumber', 'PhoneNumber', 'trim|numeric');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $PaymentResponse = $this->Users_model->add($this->Post, $this->SessionUserID, @$this->CouponID);
        if (empty($PaymentResponse)) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "An error occurred, please try again later.";
        } else {
            $this->Return['Data']    = $PaymentResponse;
            $this->Return['Message'] = "Success.";
        }
    }

    /*
      Name: 		confirm
      Description: 	Use to update payment gateway response
      URL: 			/wallet/confirm/
     */

    public function confirm_post() {
        /* Validation section */
        $this->form_validation->set_rules('PaymentGateway', 'PaymentGateway', 'trim|required|in_list[Stripe]');
        $this->form_validation->set_rules('WalletID', 'WalletID', 'trim|required|numeric|callback_validateWalletID');
        $this->form_validation->set_rules('StripeToken', 'StripeToken', 'trim' . (!empty($this->Post['PaymentGateway']) && $this->Post['PaymentGateway'] == 'Stripe' ? '|required' : ''));
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Return = $this->Users_model->confirm($this->Post, $this->SessionUserID);
    }

    public function confirmApp_post(){
        
        /* Validation section */
        $this->form_validation->set_rules('PaymentGateway', 'PaymentGateway', 'trim|required|in_list[Stripe]');
        $this->form_validation->set_rules('PaymentGatewayStatus', 'PaymentGatewayStatus', 'trim|required|in_list[Success,Failed,Cancelled]');
        $this->form_validation->set_rules('WalletID', 'WalletID', 'trim|required|numeric|callback_validateWalletID');
        $this->form_validation->set_rules('PaymentGatewayResponse', 'PaymentGatewayResponse', 'trim');
        $this->form_validation->set_rules('Razor_payment_id', 'Razor_payment_id', 'trim');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $WalletData = $this->Users_model->confirmApp($this->Post, $this->SessionUserID);
        if (!$WalletData) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "An error occurred, please try again later.";
        } else {
            $this->Return['Data'] = $WalletData;
            $this->Return['Message'] = "Success.";
        }
    }

    /*
      Name:         stripeEphemeralKeys
      Description:  Use to get stripe ephemeral keys data
      URL:          /wallet/stripeEphemeralKeys/
     */

    public function stripeEphemeralKeys_post() {
        $this->Return = $this->Users_model->stripeEphemeralKeys($this->SessionUserID);
    }

    /*
      Name:         returnClientSecret
      Description:  Use to get payment intent keys data (Only For Android Use)
      URL:          /wallet/returnClientSecret/
     */

    public function returnClientSecret_post() {
        /* Validation section */
        $this->form_validation->set_rules('customerId', 'customerId', 'trim|required');
        $this->form_validation->set_rules('amount', 'amount', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Return = $this->Users_model->returnClientSecret($this->Post, $this->SessionUserID);
    }

    /*
      Name: 		getWallet
      Description: 	To get wallet data
      URL: 			/wallet/getWallet/
     */

    public function getWallet_post() {
        $this->form_validation->set_rules('TransactionMode', 'TransactionMode', 'trim|required|in_list[All,WalletAmount,WinningAmount,CashBonus]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('OrderBy', 'OrderBy', 'trim');
        $this->form_validation->set_rules('Sequence', 'Sequence', 'trim|in_list[ASC,DESC]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Get Wallet Data */
        $WalletDetails = $this->Users_model->getWallet(@$this->Post['Params'], array_merge($this->Post, array('UserID' => $this->SessionUserID)), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($WalletDetails)) {
            $this->Return['Data'] = $WalletDetails['Data'];
        }
    }

    /*
      Name:             withdrawal
      Description:  Use to withdrawal winning amount
      URL:          /wallet/withdrawal/
     */

    public function withdrawal_post() {
        /* Validation section */
        $this->form_validation->set_rules('UserGUID', 'UserGUID', 'trim|required' . ($this->Post['PaymentGateway'] == 'Bank' ? '|callback_validateAccountStatus' : ''));
        $this->form_validation->set_rules('PaymentGateway', 'PaymentGateway', 'trim|required|in_list[Paytm,Bank]');
        $this->form_validation->set_rules('PaytmPhoneNumber', 'PaytmPhoneNumber', 'trim' . (!empty($this->Post['PaymentGateway']) && $this->Post['PaymentGateway'] == 'Paytm' ? '|required|callback_validatePhoneStatus' : ''));
        $this->form_validation->set_rules('Amount', 'Amount', 'trim|required|numeric|callback_validateWithdrawalAmount');

        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!empty($this->Post['PaymentGateway']) && $this->Post['PaymentGateway'] == 'Paytm') {
            /** temp code by paytm * */
            $WalletData = $this->Users_model->withdrawal($this->Post, $this->SessionUserID);
        } else {
            $WalletData = $this->Users_model->withdrawal($this->Post, $this->SessionUserID);
        }
        
        if (empty($WalletData) || @$WalletData->paytmResponse->status == 'FAILURE') {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "An error occurred, please try again later.";
        } else {
            $this->Return['Data'] = $WalletData;
            $this->Return['Message'] = ($this->Post['PaymentGateway'] == 'Paytm' ? "Withdrawal success." : 'Withdrawal Request had been Sent.');
        }
    }

    /*
      Name: 			getWithdrawals
      Description: 	To get Withdrawal data
      URL: 			/wallet/getWithdrawals/
     */

    public function getWithdrawals_post() {
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('OrderBy', 'OrderBy', 'trim');
        $this->form_validation->set_rules('Sequence', 'Sequence', 'trim|in_list[ASC,DESC]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Get Withdrawal Data */
        $WithdrawalsData = $this->Users_model->getWithdrawals(@$this->Post['Params'], array_merge($this->Post, array('UserID' => $this->SessionUserID)), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($WithdrawalsData)) {
            $this->Return['Data'] = $WithdrawalsData['Data'];
        }
    }

    /**
     * Function Name: validateAccountStatus
     * Description:   To validate user account status
     */
    public function validateAccountStatus($UserGUID) {
        /* Validate account status */
        $userData = $this->Users_model->getUsers('PanStatus,BankStatus', array('UserID' => $this->SessionUserID));

        /* Validate Pending Withdrawal Request */
        if ($this->db->query('SELECT COUNT(*) AS TotalRecords FROM `tbl_users_withdrawal` WHERE `UserID` = ' . $this->SessionUserID . ' AND `StatusID` = 1')->row()->TotalRecords > 0) {
            $this->form_validation->set_message('validateAccountStatus', 'Your withdrawal request already in pending mode.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Function Name: validateWithdrawalAmount
     * Description:   To validate withdrawal amount
     */
    public function validateWithdrawalAmount($Amount) {


        if ($this->Post['PaymentGateway'] == 'Bank') {
            $MinimumWithdrawalLimit = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MinimumWithdrawalLimitBank" LIMIT 1')->row()->ConfigTypeValue;
        } else {
            $MinimumWithdrawalLimit = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MinimumWithdrawalLimitPaytm" LIMIT 1')->row()->ConfigTypeValue;
        }
        if ($Amount < $MinimumWithdrawalLimit) {
            $this->form_validation->set_message('validateWithdrawalAmount', 'Minimum withdrawal amount limit is ' . DEFAULT_CURRENCY . '' . $MinimumWithdrawalLimit);
            return FALSE;
        }

        if ($this->Post['PaymentGateway'] == 'Bank') {
            $MaximumWithdrawalLimit = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MaximumWithdrawalLimitBank" LIMIT 1')->row()->ConfigTypeValue;
        } else {
            $MaximumWithdrawalLimit = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MaximumWithdrawalLimitPaytm" LIMIT 1')->row()->ConfigTypeValue;
        }

        if ($Amount > $MaximumWithdrawalLimit) {
            $this->form_validation->set_message('validateWithdrawalAmount', 'Maximum withdrawal amount limit is ' . DEFAULT_CURRENCY . '' . $MaximumWithdrawalLimit);
            return FALSE;
        }
        if ($this->Post['PaymentGateway'] == 'Paytm') {
            $MaximumWithdrawalLimitPaytmPerDay = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MaximumWithdrawalLimitPaytm" LIMIT 1')->row()->ConfigTypeValue;
            if ($MaximumWithdrawalLimitPaytmPerDay > 0) {
                $TotalWithdrawals = $this->db->query("SELECT SUM(Amount) as TotalWithdrawals FROM tbl_users_withdrawal WHERE UserID = $this->SessionUserID  AND DATE(EntryDate) = '" . date('Y-m-d') . "' AND StatusID = 5 AND PaymentGateway='Paytm' LIMIT 1")->row()->TotalWithdrawals;
                $TotalWithdrawals = round($TotalWithdrawals) + $Amount;
                if ($MaximumWithdrawalLimitPaytmPerDay <= $TotalWithdrawals) {
                    $this->form_validation->set_message('validateWithdrawalAmount', 'Today Paytm withdrawal limit is exceeded.');
                    return FALSE;
                }
            }
        }
        if ($this->Post['PaymentGateway'] == 'Bank') {
            $MaximumWithdrawalLimitPaytmPerDay = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MaximumWithdrawalLimitBank" LIMIT 1')->row()->ConfigTypeValue;

            if ($MaximumWithdrawalLimitPaytmPerDay > 0) {
                $TotalWithdrawals = $this->db->query("SELECT SUM(Amount) as TotalWithdrawals FROM tbl_users_withdrawal WHERE UserID = $this->SessionUserID  AND DATE(EntryDate) = '" . date('Y-m-d') . "' AND StatusID = 5 AND PaymentGateway='Bank' LIMIT 1")->row()->TotalWithdrawals;
                $TotalWithdrawals = round($TotalWithdrawals) + $Amount;
                if ($MaximumWithdrawalLimitPaytmPerDay <= $TotalWithdrawals) {
                    $this->form_validation->set_message('validateWithdrawalAmount', 'Today Bank withdrawal limit is exceeded.');
                    return FALSE;
                }
            }
        }
        /* Validate Winning Amount */
        $UserData = $this->Users_model->getUsers('WinningAmount,isWithdrawal', array('UserID' => $this->SessionUserID));
        if ($Amount > $UserData['WinningAmount']) {
            $this->form_validation->set_message('validateWithdrawalAmount', 'Withdrawal amount can not greater than to winning amount.');
            return FALSE;
        }
        if ($UserData['isWithdrawal'] == 'No') {
            $this->form_validation->set_message('validateWithdrawalAmount', 'You can not withdraw with this account.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Function Name: validateMinimumDepositAmount
     * Description:   To validate minimum deposit amount
     */
    public function validateMinimumDepositAmount($Amount) {
        /* Get Minimum Deposit Limit */
        $MinimumDepositLimit = $this->db->query('SELECT ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID = "MinimumDepositLimit" LIMIT 1')->row()->ConfigTypeValue;
        if ($Amount < $MinimumDepositLimit) {
            $this->form_validation->set_message('validateMinimumDepositAmount', 'Minimum deposit amount limit is ' . DEFAULT_CURRENCY . $MinimumDepositLimit);
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Function Name: validateWithdrawalID
     * Description:   To validate withdrawal ID
     */
    public function validateWithdrawalID($WithdrawalID) {
        $Query = $this->db->query("SELECT OTP,IsOTPVerified,Amount,PaymentGateway,StatusID,PaytmPhoneNumber FROM `tbl_users_withdrawal` WHERE `WithdrawalID` = " . $WithdrawalID . " LIMIT 1");
        if ($Query->num_rows() == 0) {
            $this->form_validation->set_message('validateWithdrawalID', 'Invalid {field}.');
            return FALSE;
        } else {

            if ($Query->row()->StatusID != 1) {
                $this->form_validation->set_message('validateWithdrawalID', 'We did not find pending withdrawal request.');
                return FALSE;
            }
            if ($Query->row()->IsOTPVerified == "Yes") {
                $this->form_validation->set_message('validateWithdrawalID', 'OTP already verified.');
                return FALSE;
            }
            if ($Query->row()->OTP != $this->Post['OTP']) {
                $this->form_validation->set_message('validateWithdrawalID', 'Invalid OTP.');
                return FALSE;
            }
            $this->Post['PaytmPhoneNumber'] = $Query->row()->PaytmPhoneNumber;
            $this->Post['Amount'] = $Query->row()->Amount;
            $this->Post['PaymentGateway'] = $Query->row()->PaymentGateway;
            return TRUE;
        }
    }

    /**
     * Function Name: validateWalletID
     * Description:   To validate wallet ID
     */
    public function validateWalletID($WalletID) {
        $WalletData = $this->Users_model->getWallet('Amount,TransactionID,CouponDetails,Currency,OpeningWalletAmount', array('UserID' => $this->SessionUserID, 'WalletID' => $WalletID));
        if (!$WalletData) {
            $this->form_validation->set_message('validateWalletID', 'Invalid {field}.');
            return FALSE;
        } else {
            $this->Post['Amount']        = round($WalletData['Amount'], 1);
            $this->Post['OpeningWalletAmount'] = $WalletData['OpeningWalletAmount'];
            $this->Post['TransactionID'] = $WalletData['TransactionID'];
            $this->Post['CouponDetails'] = $WalletData['CouponDetails'];
            $this->Post['Currency']      = $WalletData['Currency'];
            return TRUE;
        }
    }
    

    /**
     * Function Name: validateAccountStatus
     * Description:   To validate user account status
     */
    public function validatePhoneStatus($PaytmPhoneNumber) {
        /* Validate account status */
        $userData = $this->Users_model->getUsers('PhoneNumber', array('UserID' => $this->SessionUserID));
        if ($userData['PhoneNumber'] != $PaytmPhoneNumber && $userData['PhoneNumber'] != "") {
            $this->form_validation->set_message('validatePhoneStatus', 'PhoneNumber not verified.');
            return FALSE;
        }
        return TRUE;
    }

}
