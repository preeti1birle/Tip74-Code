<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Store_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}	

	/*
	Description: 	Use to get single coupon or list of coupons.
	*/
	function getCoupons($Field, $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=15){
		/* Define section  */
		$Return = array('Data' => array('Records' => array()));
		/* Define variables - ends */
		$this->db->select('C.CouponID CouponIDForUse');
		$this->db->select($Field);
		$this->db->select('
			CASE E.StatusID
			when "2" then "Active"
			when "6" then "Inactive"
			END as Status', false);
		$this->db->from('tbl_entity E');
		$this->db->from('ecom_coupon C');
		$this->db->where("C.CouponID","E.EntityID", FALSE);

		if(!empty($Where['CouponID'])){
			$this->db->where("C.CouponID",$Where['CouponID']);
		}
		if(!empty($Where['CouponCode'])){
			$this->db->where("C.CouponCode",$Where['CouponCode']);			
		}
		if(!empty($Where['StatusID'])){
			$this->db->where("E.StatusID",$Where['StatusID']);			
		}

		$this->db->order_by('C.CouponID','DESC');
		/* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
			$this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /*for pagination*/
		}else{
			$this->db->limit(1);
		}

		$Query = $this->db->get();	
		//echo $this->db->last_query();
		if($Query->num_rows()>0){
			foreach($Query->result_array() as $Record){
				$MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL',array("SectionID" => 'Coupon',"EntityID" => $Record['CouponIDForUse']),TRUE);
					$Record['Media'] = ($MediaData ? $MediaData['Data'] : array());

				unset($Record['CouponIDForUse']);
				if(!$multiRecords){
					return $Record;
				}
				if (!empty($Record['Media']['Records'])) {
					$Record['MediaURL'] = $Record['Media']['Records'][0]['MediaURL'];
				}
				$Records[] = $Record;
			}
			$Return['Data']['Records'] = $Records;
			return $Return;
		}
		return FALSE;		
	}

	/*
	Description: 	ADD new coupon.
	*/
	function addCoupon($UserID, $Input=array(), $StatusID){
		$this->db->trans_start();
		$EntityGUID = get_guid();
		/* Add to entity table and get ID. */
		$CouponID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID"=>13, "UserID"=>$UserID, "StatusID"=>$StatusID));
		/* Add product to product table*/
		$this->db->insert('ecom_coupon', array("CouponID"=>$CouponID));

		$this->updateCoupon($CouponID, $Input, $UserID);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}
		return array('CouponID' => $CouponID, 'CouponGUID' => $EntityGUID);
	}

	/*
	Description: 	Use to update category.
	*/
	function updateCoupon($CouponID, $Input=array()){
		$UpdateArray = array_filter(array(
			"CouponTitle" 			=>	@$Input['CouponTitle'],
			"CouponDescription" 	=>	@$Input['CouponDescription'],
			"ProductRegPrice" 		=>	@$Input['ProductRegPrice'],
			"CouponCode" 			=>	@$Input['CouponCode'],
			"CouponType" 			=>	@$Input['CouponType'],
			"OfferType" 			=>	@$Input['OfferType'],
			"DepositType" 			=>	@$Input['DepositType'],
			"CouponValue" 			=>	@$Input['CouponValue'],
			"CouponValidTillDate" 	=>	@$Input['CouponValidTillDate'],
			"Broadcast" 			=>	@$Input['Broadcast'],				
			"MiniumAmount" 			=>	@$Input['MiniumAmount'],				
			"MaximumAmount" 		=>	@$Input['MaximumAmount'],				
			"NumberOfUses" 			=>	@$Input['NumberOfUses'],				
		));

		if(isset($Input['CouponDescription']) && $Input['CouponDescription']==''){ $UpdateArray['CouponDescription'] = null; }

		if(!empty($UpdateArray)){ /*Update product details*/
			$this->db->where('CouponID', $CouponID);
			$this->db->limit(1);
			$this->db->update('ecom_coupon', $UpdateArray);
		}
		/*Update Status*/
		if(!empty($Input['StatusID'])){
			$this->Entity_model->updateEntityInfo($CouponID, array('StatusID'=>@$Input['StatusID']));			
		}
		return TRUE;
	}

	/*
	Description: 	Use to add new order
	*/
	function addOrder($Input=array(), $UserID, $CouponID='', $StatusID=1){

		$OrderGUID = get_guid();
		$this->db->trans_start();

		/* get coupon data for apply */
		if(!empty($CouponID)){
			$CouponData = $this->getCoupons('C.CouponType, C.CouponValue',array("CouponID"=>$CouponID, "StatusID"=>2));
		}

		/*booking duration calculation*/
		if(!empty($Input['FromDateTime']) && !empty($Input['ToDateTime'])){
			$BookedDuration = diffInHours($Input['FromDateTime'], $Input['ToDateTime']);
		}


		/* Add order to orders table */
		$OrderData = array_filter(array(
			"UserID"		=>	$UserID,
			"OrderGUID"		=>	$OrderGUID,

			"FromDateTime"	=>	@$Input['FromDateTime'],
			"ToDateTime"	=>	@$Input['ToDateTime'],
			"Note"			=>	@$Input['Note'],
			"BookedDuration"=>	@$BookedDuration,

			"EntryDate"		=>	date("Y-m-d h:i;s"),
			"FirstName"		=>	$Input['Recipient']['FirstName'],
			"LastName"		=>	@$Input['Recipient']['LastName'],
			"Address"		=>	@$Input['DeliveryPlace']['Address'],
			"Address1"		=>	@$Input['DeliveryPlace']['Address1'],
			"City"			=>	@$Input['DeliveryPlace']['City'],
			"PhoneNumber"	=>	@$Input['DeliveryPlace']['PhoneNumber'],
			"DeliveryType"	=>	$Input['DeliveryType'],
			"PaymentMode"	=>	$Input['PaymentMode'],
			"PaymentGateway"=>	@$Input['PaymentGateway'],
			"CouponID"		=>	@$CouponID,
			"RewardPoints"	=>	@$Input['RewardPoints'],
			"StatusID"		=>	$StatusID
		));
		$this->db->insert('ecom_orders', $OrderData);
		$OrderID = $this->db->insert_id();

		/* Add products order info to order_details table */
		$OrderPrice = 0;
		if(!empty($Input['Products']) && is_array($Input['Products'])){
			foreach ($Input['Products'] as $key => $value) {
				$ProductData = $this->getProducts('P.ProductID, P.ProductRegPrice, P.ProductBuyPrice',array("ProductGUID"=>$value['ProductGUID']));
				$OrderDetailData[] = array(
					'OrderID'=>$OrderID,
					'ProductID'=>$ProductData['ProductID'],
					'ProductQuantity'=>(!empty($value['Quantity']) ? $value['Quantity']:1),
					"ProductRegPrice"=>$ProductData['ProductRegPrice'],			
					"ProductBuyPrice"=>$ProductData['ProductBuyPrice'] 
				);
				$OrderPrice += $ProductData['ProductBuyPrice']*$value['Quantity'];

				//if($Input['PaymentMode']=='COD'){
				/*minus stock quantity*/
				$this->editProduct($ProductData['ProductID'], array("ProductInStock"=>"ProductInStock-".$value['Quantity']), $UserID);
				//}
			}
			$this->db->insert_batch('ecom_order_details', $OrderDetailData); 


			/* apply discount (discount calculation) - start */
			$DiscountedPrice = 0;
			if(!empty($CouponData)){
				$DiscountedPrice = ($CouponData['CouponType']=='Flat' ? $CouponData['CouponValue'] : ($OrderPrice / 100) * $CouponData['CouponValue']);
				$OrderPrice = ($OrderPrice-$DiscountedPrice);
			}
			/* apply discount (discount calculation) - ends */

			/* apply reward points - start */
			if(!empty($Input['RewardPoints'])){
				$DiscountedPrice += $Input['RewardPoints'];
				$OrderPrice = ($OrderPrice-$Input['RewardPoints']);
				/*add reward points*/
				$this->Users_model->updateRewardPoints(
					array("TransactionID" => $OrderID, "TransactionDetails" => ''),
					$UserID, $Input['RewardPoints'], 'Subtract', 'Redemption');

			}
			/* apply reward points - ends */

		} /*Add product ends*/

		/* update OrderPrice to orders table */
		$this->db->where('OrderID', $OrderID);
		$this->db->limit(1);
		$this->db->update('ecom_orders', array("OrderPrice"=>$OrderPrice, "DiscountedPrice"=>$DiscountedPrice));

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}
		return array("OrderGUID"=>$OrderGUID, "OrderID"=>$OrderID);
	}

}
