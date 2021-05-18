<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}	

	/*
	Description: 	Use to get notifications.
	*/
	function getNotificationCount($Field='', $Where=array()){
		$this->db->select($Field);
		$this->db->from('tbl_notifications');

		if(!empty($Where['UserID'])){
			$this->db->where("ToUserID", $Where['UserID']);
		}

		if(!empty($Where['NotificationPatternID'])){
			$this->db->where("NotificationPatternID",$Where['NotificationPatternID']);
		}

		if(!empty($Where['FromDate'])){
        	$this->db->where('DATE(EntryDate) BETWEEN "'.$Where['FromDate'].'"  AND "'.date("Y-m-d H:i:s").'"', NULL, FALSE);
		}

		if(!empty($Where['StatusID'])){
			$this->db->where("StatusID",$Where['StatusID']);
		}	
		$Query = $this->db->get();
		//echo $this->db->last_query();	
		return $Query->row_array();
	}



	/*
	Description: 	Use to get notifications.
	*/
	function getNotifications($UserID, $Where=array(), $PageNo=1, $PageSize=15){
		$this->db->select('
			NP.NotificationPatternGUID,
			E.EntityGUID UserGUID,
			ER.EntityGUID RefrenceGUID,
			N.NotificationID,
			IF(NP.NotificationPatternGUID="broadcast",N.RefrenceID,"") AS RefrenceID,
			N.NotificationText,
			N.NotificationMessage,
			DATE_FORMAT(CONVERT_TZ(N.EntryDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") EntryDate,
			N.StatusID,
			IF(U.ProfilePic = "","",CONCAT("'.PROFILE_PICTURE_URL.'",U.ProfilePic)) AS ProfilePic
			');
		$this->db->from('tbl_notifications N');
		$this->db->from('tbl_notification_pattern NP');
		$this->db->from('tbl_users U');
		$this->db->where("N.NotificationPatternID","NP.NotificationPatternID", FALSE);
		$this->db->join('tbl_entity E', 'E.EntityID = N.UserID', 'left');
		$this->db->join('tbl_entity ER', 'ER.EntityID = N.RefrenceID', 'left');
		$this->db->where("U.UserID","N.UserID", FALSE);
		$this->db->where('N.ToUserID',$UserID);

		if(!empty($Where['Status'])){
			$this->db->where("N.StatusID",$Where['Status']);
		}
		if(!empty($Where['NotificationPatternID'])){
			$this->db->where("NP.NotificationPatternID",$Where['NotificationPatternID']);
		}


		$this->db->order_by('N.NotificationID','DESC');

		$TempOBJ = clone $this->db;
		$TempQ = $TempOBJ->get();
		$Return['Data']['TotalRecords'] = $TempQ->num_rows();
		$this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /*for pagination*/

		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			foreach($Query->result_array() as  $Record){
				$Records[] = $Record;
			}
			$Return['Data']['Records'] = $Records;
			return $Return;	
		}else{
			return FALSE;
		}
	}

	/*
	Description: 	Use to get notifications.
	*/
	function getCongratulationsNotification($UserID, $Where=array()){
		$this->db->select('
			N.NotificationID,
			N.NotificationText,
			N.NotificationMessage,
			');
		$this->db->from('tbl_notifications N');
		$this->db->where('N.ToUserID',$UserID);
		$this->db->where("N.ReadCongratulations","No");
		if(!empty($Where['NotificationText'])){
			$this->db->where_in("N.NotificationText",$Where['NotificationText']);
		}
		$this->db->order_by('N.NotificationID','ASC');
		$TempOBJ = clone $this->db;
		$TempQ = $TempOBJ->get();
		$Return['Data']['TotalRecords'] = $TempQ->num_rows();
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			$Return['Data']['Records'] = $Query->row_array();
			return $Return;	
		}else{
			return FALSE;
		}
	}

	/*
	Description: 	Use to delete all users notification.
	*/
	function deleteAll($UserID,$NotificationIDs){
		if (!empty($NotificationIDs)) {
			$this->db->where('ToUserID', $UserID);
			$this->db->where_in('NotificationID', $NotificationIDs);
			$this->db->delete('tbl_notifications');
		}else{
			$this->db->where('ToUserID', $UserID);
			$this->db->delete('tbl_notifications');
		}
	}

	/*
	Description: 	Use to add new notification
	*/
	function addNotification($NotificationPatternGUID, $NotificationText, $UserID, $ToUserID, $RefrenceID='', $NotificationMessage='', $MediaID=''){
		$NotificationPattern = $this->getNotificationPattern($NotificationPatternGUID);
		if($NotificationPattern){
			/* Add notifcation */
			$InsertData = array_filter(array(
				"NotificationPatternID" => 	$NotificationPattern['NotificationPatternID'],
				"UserID" 				=>	$UserID,
				"ToUserID" 				=> 	$ToUserID,
				"RefrenceID" 			=> 	$RefrenceID,
				"NotificationText" 		=> 	$NotificationText,
				"NotificationMessage" 	=> 	$NotificationMessage,
				"MediaID" 				=> 	$MediaID,
				"EntryDate" 			=>	date("Y-m-d H:i:s")
			));
			$this->db->insert('tbl_notifications', $InsertData);
			/*send push message*/
			if($NotificationPattern['SendPushMessage']=='Yes'){

				$this->load->model('Settings_model');
				$SettingData = $this->Settings_model->getSettings($ToUserID);
				if(!empty($SettingData) && $SettingData['PushNotification']=='Yes'){

					/*check pushstatus on user settings*/
					$SendNotification = true;
					if(isset($SettingData[$NotificationPattern['NotificationPatternGUID']])){
						$SendNotification = ($SettingData[$NotificationPattern['NotificationPatternGUID']]=='No' ? false : true);
					}
					if($SendNotification){
						sendPushMessage($ToUserID, $NotificationText, $NotificationMessage, $Data=array("RefrenceID"=>$RefrenceID, "NotificationPatternGUID"=>$NotificationPattern['NotificationPatternGUID']));			
					}
				}

			}	
		}
	}


	/*
	Description: 	Use to get NotificationPatternID by NotificationPatternGUID
	*/
	function getNotificationPattern($NotificationPatternGUID){
		$this->db->select('*');
		$this->db->from('tbl_notification_pattern');
		$this->db->where('NotificationPatternGUID',$NotificationPatternGUID);
		$this->db->where("StatusID",2);
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			return $Query->row_array();
		}else{
			return FALSE;
		}
	}



	/*
	Description: 	Use to delete notification
	*/
	function removeNotification($NotificationPatternGUID, $UserID='', $ToUserID, $RefrenceID=''){
		$NotificationPattern = $this->getNotificationPattern($NotificationPatternGUID);
		/* Delete notifcation */
		$Where = array_filter(array(
			"NotificationPatternID" 	=> 	$NotificationPattern['NotificationPatternID'],
			"UserID" 					=>	$UserID,
			"ToUserID" 					=> 	$ToUserID,
			"RefrenceID" 				=> 	$RefrenceID
		));
		$this->db->where($Where);	
		$this->db->delete('tbl_notifications');
		//echo $this->db->last_query();
	}

	/*
	Description: 	Use to mark all notifiction to read.
	*/
	function markRead($UserID,$NotificationID){
		$UpdateArray = array_filter(array(
			"StatusID" 		=>	2,
			"ModifiedDate"	=> date("Y-m-d H:i:s")
		));

		$this->db->where('NotificationID', $NotificationID);
		$this->db->where('ToUserID', $UserID);
		$this->db->where('StatusID', 1);
		$this->db->update('tbl_notifications', $UpdateArray);
	}

		/*
	Description: 	Use to mark all notifiction to read.
	*/
	function updateNotificationStatus($UserID,$NotificationID){
		$UpdateArray = array_filter(array(
			"ReadCongratulations" => "Yes",
			"ModifiedDate"	=> date("Y-m-d H:i:s")
		));

		$this->db->where('NotificationID', $NotificationID);
		$this->db->where('ToUserID', $UserID);
		$this->db->update('tbl_notifications', $UpdateArray);
	}

	/*
	Description: 	Use to mark all notifiction to read.
	*/
	function markAllRead($UserID){
		$UpdateArray = array_filter(array(
			"StatusID" 		=>	2,
			"ModifiedDate"	=> date("Y-m-d H:i:s")
		));

		$this->db->where('ToUserID', $UserID);
		$this->db->where('StatusID', 1);
		$this->db->update('tbl_notifications', $UpdateArray);
	}





}


