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
	Description: 	Use to add coupon.
	URL: 			/api_admin/store/addCoupon/	
	*/
	public function addCoupon_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
		$this->form_validation->set_rules('CouponCode', 'CouponCode', 'trim|required');
		$this->form_validation->set_rules('CouponValue', 'CouponValue', 'trim|required');
		$this->form_validation->set_rules('CouponType', 'CouponType', 'trim|required');
		$this->form_validation->set_rules('OfferType', 'Offer Type', 'trim|required');
		$this->form_validation->set_rules('CouponTitle', 'CouponTitle', 'trim|required');
		$this->form_validation->set_rules('CouponDescription', 'CouponDescription', 'trim');
		$this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$CouponData = $this->Store_model->addCoupon($this->SessionUserID, array_merge($this->Post),$this->StatusID);
		if($CouponData){
			/* check for media present - associate media with this Post */
			if(!empty($this->Post['MediaGUIDs'])){
				$MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
				foreach($MediaGUIDsArray as $MediaGUID){
					$EntityData=$this->Entity_model->getEntity('E.EntityID MediaID',array('EntityGUID'=>$MediaGUID, 'EntityTypeID'=>2));
					if ($EntityData){
						$this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $CouponData['CouponID']);
					}
				}
			}
			/* check for media present - associate media with this Post - ends */

			$this->Return['Message']      	=	"Coupon added successfully."; 
		}
	}

	/*
	Description: 	Use to update coupon.
	URL: 			/api_admin/store/editCoupon/	
	*/
	public function editCoupon_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required|callback_validateSession');
		$this->form_validation->set_rules('CouponGUID', 'CouponGUID', 'trim|required|callback_validateEntityGUID[Coupon,CouponID]');
		$this->form_validation->set_rules('CouponValidTillDate', 'CouponValidTillDate', 'trim|callback_validateDate');
		$this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		/* check for media present - associate media with this Post */
		if (!empty($this->Post['MediaGUID'])) {
            $EntityData = $this->Entity_model->getEntity('E.EntityID MediaID', array('EntityGUID' => $this->Post['MediaGUID'], 'EntityTypeID' => 2));
            if ($EntityData) {
                $this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $this->CouponID);
            }
        }
		/* check for media present - associate media with this Post - ends */

		$updateCoupon = $this->Store_model->updateCoupon($this->CouponID, array_merge($this->Post,array("StatusID"=>$this->StatusID)));
		if ($updateCoupon) {
			$CouponData = $this->Store_model->getCoupons('
				E.EntityGUID AS CouponGUID,
				E.EntryDate,
				E.StatusID,
				C.CouponTitle,
				C.OfferType,
				C.CouponDescription,		
				C.CouponCode,
				C.CouponType,
				C.CouponValue,
				C.CouponValueLimit,
				C.CouponValidTillDate,
				',
				array("CouponID"=>@$this->CouponID));

			$this->Return['Data'] 		= $CouponData;
			$this->Return['Message']  	=	"Status has been changed.";
		}else{
			$this->Return['ResponseCode']  	=	500;
			$this->Return['Message']  	=	"Something went wrong";
		}
	}
}