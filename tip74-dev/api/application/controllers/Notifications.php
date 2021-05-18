<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
	}

	/*
	Name: 			notifications
	Description: 	Use to get notifications.
	URL: 			/api/notifications
	*/
	public function index_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('NotificationPatternGUID', 'NotificationPatternGUID', 'trim|callback_validateNotificationPatternGUID');
		$this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
		$this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		// $this->Notification_model->markAllRead($this->SessionUserID); /*mark all notifiction to read.*/
		$NotificationData = $this->Notification_model->getNotifications($this->SessionUserID, array_merge(array("NotificationPatternID"=>@$this->NotificationPatternID), $this->Post),@$this->Post['PageNo'], @$this->Post['PageSize']);
		if($NotificationData){
			$this->Return['Data'] = $NotificationData['Data'];
		}
	}

		/*
	Name: 			notifications
	Description: 	Use to get notifications.
	URL: 			/api/notifications
	*/
	public function getCongratulationsNotification_post()
	{
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */

        $Where = array('NotificationText' => array('Signup Bonus','Signup Deposit Bonus','Referred Bonus Added','Contest Winner Bonus','Welcome to '.SITE_NAME.'!','Admin Bonus'));
		$NotificationData = $this->Notification_model->getCongratulationsNotification($this->SessionUserID,$Where);
		if($NotificationData){
			$this->Return['Data'] = $NotificationData['Data'];
		}
		/*else{
		    $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Records not found";
		}*/
	}

		/*
	Name: 			mark Read
	Description: 	Use to mark single notifiction to read.
	URL: 			/api/notifications/markRead
	*/
	public function updateNotificationStatus_post()
	{
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required');
		$this->form_validation->set_rules('NotificationID', 'NotificationID', 'trim|required');
		$this->form_validation->validation($this);  /* Run validation */		
		
		$this->Notification_model->updateNotificationStatus($this->SessionUserID,$this->input->post('NotificationID'));
		$Where = array('NotificationText' => array('Signup Bonus','Signup Deposit Bonus','Referred Bonus Added','Contest Winner Bonus','Welcome to '.SITE_NAME.'!','Admin Bonus'));
		$NotificationData = $this->Notification_model->getCongratulationsNotification($this->SessionUserID,$Where);
		if($NotificationData){
			$this->Return['Data'] = $NotificationData['Data'];
		}
	}

	/*
	Name: 			getNotificationCount
	Description: 	Use to get count of notifications.
	URL: 			/api/notifications/getNotificationCount
	*/
	public function getNotificationCount_post()
	{
		$NotificationData = $this->Notification_model->getNotificationCount('COUNT(NotificationID) TotalUnread',
			array("UserID"=>$this->SessionUserID, "StatusID"=>1));
		$this->Return['Data'] = array("TotalUnread"=>$NotificationData['TotalUnread']);
	}

	/*
	Name: 			markAllRead
	Description: 	Use to mark all notifiction to read.
	URL: 			/api/notifications/markAllRead
	*/
	public function markAllRead_post()
	{
		$this->Notification_model->markAllRead($this->SessionUserID);
	}

	/*
	Name: 			mark Read
	Description: 	Use to mark single notifiction to read.
	URL: 			/api/notifications/markRead
	*/
	public function markRead_post()
	{
		$this->form_validation->set_rules('NotificationID', 'NotificationID', 'trim|required');
		$this->form_validation->validation($this);  /* Run validation */		
		
		$this->Notification_model->markRead($this->SessionUserID,$this->input->post('NotificationID'));
	}

	/*
	Name: 			deleteAll
	Description: 	Use to delete all users notification
	URL: 			/api/notifications/deleteAll
	*/
	public function deleteAll_post()
	{
		$this->form_validation->set_rules('SessionKey', 'SessionKey', 'trim|required');
		 $this->form_validation->validation($this);  /* Run validation */
		if (!empty($this->Post['NotificationIDs'])) {
			$this->db->where('ToUserID', $this->SessionUserID);
			$this->db->where_in('NotificationID', $NotificationIDs);
			$this->db->from('tbl_notifications');
			$this->db->limit(1);
			$Query = $this->db->get();
            $IsValid = $Query->result_array();
            if(empty($IsValid)){
                $this->Return['ResponseCode'] = 500;
                $this->Return['Message'] = "Invalid User!";
                exit;
            }
			$this->Notification_model->deleteAll($this->SessionUserID,$this->Post['NotificationIDs']);
		}else{
			$this->Notification_model->deleteAll($this->SessionUserID);
		}
	}

	/*Common Validations*/
	/*------------------------------*/
	/*------------------------------*/	
	function validateNotificationPatternGUID($NotificationPatternGUID)
	{		
		if(empty($NotificationPatternGUID)){
			return TRUE;
		}

		$NotificationPattern = $this->Notification_model->getNotificationPattern($NotificationPatternGUID);
		if($NotificationPattern){
			$this->NotificationPatternID = $NotificationPattern['NotificationPatternID'];
			return TRUE;
		}
		$this->form_validation->set_message('validateNotificationPatternGUID', 'Invalid {field}.');  
		return FALSE;
	}



}
