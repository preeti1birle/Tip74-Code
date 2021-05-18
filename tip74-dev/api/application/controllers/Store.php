<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends API_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Store_model');
	}

	/*
	Name: 			getCoupons
	Description: 	Use to get Get list of Coupons.
	URL: 			/api/store/getCoupons	
	*/
	public function getCoupons_post()
	{
		/* Validation section */
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
		$this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
		$this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$CouponData = $this->Store_model->getCoupons('
			E.EntityGUID AS CouponGUID,
			E.EntryDate,
			E.StatusID,
			C.CouponTitle,
			C.DepositType,
			C.OfferType,
			C.CouponDescription,		
			C.CouponCode,
			C.CouponType,
			C.CouponValue,
			C.MiniumAmount,
			C.MaximumAmount,
			C.NumberOfUses,
			C.CouponValueLimit,
			C.CouponValidTillDate
			',
			array_merge(@$this->Post,array('StatusID' => $this->StatusID)),
			TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']
		);
		if($CouponData){
			$this->Return['Data'] = $CouponData['Data'];
		}	
	}



	/*
	Name: 			getCoupons
	Description: 	Use to get Get list of Coupons.
	URL: 			/api/store/getCoupons	
	*/
	public function getCoupon_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('CouponGUID', 'CouponGUID','trim|required|callback_validateEntityGUID[Coupon,CouponID]');

		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		
		$CouponData = $this->Store_model->getCoupons('
			E.EntityGUID AS CouponGUID,
			E.EntryDate,
			E.StatusID,
			C.CouponTitle,
			C.OfferType,
			C.CouponDescription,		
			C.CouponCode,
			C.CouponType,
			C.DepositType,
			C.CouponValue,
			C.CouponValueLimit,
			C.CouponValidTillDate,
			C.NumberOfUses
			',
			array("CouponID"=>$this->CouponID));
		if($CouponData){
			$this->Return['Data'] = $CouponData;
		}	
	}



	/*
	Name: 			validateCoupon
	Description: 	Use to get detail of Coupon.
	URL: 			/api/store/validateCoupon	
	*/
	public function validateCoupon_post()
	{
		/* Validation section */
                $this->form_validation->set_rules('SessionKey','SessionKey', 'trim|required|callback_validateSession');
                 $this->form_validation->set_rules('Amount','Amount', 'trim|required');
		$this->form_validation->set_rules('CouponCode', 'CouponCode', 'trim'.(empty($this->Post['CouponGUID']) ? '|required' : '').'|callback_validateCoupon['.$this->Post['Amount'].']');
		$this->form_validation->set_rules('CouponGUID', 'CouponGUID', 'trim|callback_validateEntityGUID[Coupon,CouponID]');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
                 
		$CouponsData = $this->Store_model->getCoupons('
			E.EntityGUID AS CouponGUID,
			C.CouponTitle,
			C.CouponDescription,		
			C.OfferType,		
			C.CouponCode,
			C.CouponType,
			C.DepositType,
			C.CouponValue,
			C.CouponValueLimit,
			C.CouponValidTillDate
			',
			array("CouponID" => $this->CouponID)
		);
		if($CouponsData){
			$this->Return['Data'] = $CouponsData;
		}	
	}




	function validateCoupon($CouponCode,$Amount)
	{	
		if(empty($CouponCode)){
			return TRUE;
		}
		$CouponData = $this->Store_model->getCoupons('CouponID, CouponValidTillDate, DepositType, MiniumAmount, MaximumAmount, NumberOfUses, CouponCode',array("CouponCode"=>$CouponCode,"StatusID"=>2));
		if($CouponData){
			if(!empty($CouponData['CouponID']) && strtotime($CouponData['CouponValidTillDate'])<time()){
				$this->form_validation->set_message('validateCoupon', 'Coupon expired.');  
				return FALSE;
			}
            if($CouponData['MiniumAmount'] > $Amount){
               $this->form_validation->set_message('validateCoupon', "Coupon is valid for the range of ".$CouponData['MiniumAmount'].' to '.$CouponData['MaximumAmount'].'.');  
	           return FALSE; 
            }else if($CouponData['MaximumAmount'] < $Amount){
               $this->form_validation->set_message('validateCoupon', "Coupon is valid for the range of ".$CouponData['MiniumAmount'].' to '.$CouponData['MaximumAmount'].'.');  
	           return FALSE; 
            }
            if(!empty($CouponData['NumberOfUses'])){
                $UserCouponApplied = $this->Users_model->getWallet('W.CouponCode',array('UserID' => $this->SessionUserID,'CouponCode' => $CouponData['CouponCode'],'StatusID' => 5),TRUE, 0);
                if(!empty($UserCouponApplied)){
                    if($UserCouponApplied['Data']['TotalRecords'] >= $CouponData['NumberOfUses']){
                       $this->form_validation->set_message('validateCoupon', "Coupon code limit exceed.");  
                       return FALSE;  
                    }
                }
            }
            if($CouponData['DepositType'] == 'FirstDeposit'){
            	$UserDepositHistory = $this->Users_model->getWallet('UserID',array('UserID' => $this->SessionUserID,'Narration' => 'Deposit Money','StatusID' => 5),TRUE, 0);
            	if ($UserDepositHistory['Data']['TotalRecords'] > 0) {
            		 $this->form_validation->set_message('validateCoupon', "Coupon code applicable only on first deposit.");  
                       return FALSE;
            	}
            }

			$this->CouponID = $CouponData['CouponID'];
			return TRUE;
		}
		$this->form_validation->set_message('validateCoupon', 'Invalid {field}.');  
		return FALSE;
	}




}
