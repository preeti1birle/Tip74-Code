<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends API_Controller {

    function __construct() {
        parent::__construct();
    }

    /*
      Name: 			getWallet
      Description: 	To get wallet data
      URL: 			/users/getWallet/
     */

    public function getWallet_post() {
        $this->form_validation->set_rules('UserGUID', 'UserGUID', 'trim|required|callback_validateEntityGUID[User,UserID]');
        $this->form_validation->set_rules('TransactionMode', 'TransactionMode', 'trim|in_list[All,WalletAmount,WinningAmount,CashBonus]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('OrderBy', 'OrderBy', 'trim');
        $this->form_validation->set_rules('Sequence', 'Sequence', 'trim|in_list[ASC,DESC]');
        $this->form_validation->validation($this);  /* Run validation */
        if($this->Post['NarrationMultiple']=='Deposit Money'){
            $this->Post['NarrationMultiple'] = array('Deposit Money', 'Referral Deposit','Signup Direct Deposit','Refund Deposit');
        }
        if($this->Post['NarrationMultiple']=='Join Details'){
            $this->Post['NarrationMultiple'] = array('Join Contest', 'Cancel Contest');
        }
        if($this->Post['NarrationMultiple']=='Bonus'){
            $this->Post['NarrationMultiple'] = array('Signup Bonus', 'Admin Cash Bonus', 'First Deposit Bonus', 'Verification Bonus', 'Referral Bonus', 'Coupon Discount Bonus','Join Contest Winning Bonus');
        }
        // print_r($this->Post);exit;
        /* Get Wallet Data */
        $WalletDetails = $this->Users_model->getWallet(@$this->Post['Params'], array_merge($this->Post, array('UserID' => $this->UserID)), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($WalletDetails)) {
            $this->Return['Data'] = $WalletDetails['Data'];
        }
    }

    public function getTotalDeposits_post() {
        $DepositDetails = $this->Users_model->getDeposits($this->Post, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($DepositDetails)) {
            $this->Return['Data'] = $DepositDetails['Data'];
        }
    }

    /*
      Name: 			getWithdrawals
      Description: 	To get Withdrawal data
      URL: 			/wallet/getWithdrawals/
     */

    public function getWithdrawals_post() {
        $this->form_validation->set_rules('UserGUID', 'UserGUID', 'trim|required|callback_validateEntityGUID[User,UserID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('OrderBy', 'OrderBy', 'trim');
        $this->form_validation->set_rules('Sequence', 'Sequence', 'trim|in_list[ASC,DESC]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Get Withdrawal Data */
        $WithdrawalsData = $this->Users_model->getWithdrawals(@$this->Post['Params'], array_merge($this->Post, array('UserID' => $this->UserID)), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($WithdrawalsData)) {
            $this->Return['Data'] = $WithdrawalsData['Data'];
        }
    }

}
