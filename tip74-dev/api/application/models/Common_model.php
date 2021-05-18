<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		mongoDBConnection();
	}	
	
	/*
	Description: Use to Save POST input to DB
	*/
	function addInputLog($Response){
		if(!API_SAVE_LOG){
			return TRUE;
		}
		$this->fantasydb->log_api->insertOne(array(
			'URL' 		=> current_url(),
			'RawData'	=> @file_get_contents("php://input"),
			'DataJ'		=> array_merge(array("API" => $this->classFirstSegment = $this->uri->segment(2)), $this->Post, $_FILES),
			'Response'	=> $Response,
			'EntryDate'    => date('Y-m-d H:i:s')
		));
	}


	/*
	Description: 	Use to get EntityTypeID by EntityTypeName
	*/
	function getEntityTypeID($EntityTypeName){
		if(empty($EntityTypeName)){return FALSE;}
		$this->db->select('EntityTypeID');
		$this->db->from('tbl_entity_type');
		$this->db->where('EntityTypeName',$EntityTypeName);
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			return $Query->row()->EntityTypeID;
		}else{
			return FALSE;
		}
	}


	/*
	Description: 	Use to get SectionID by SectionID
	*/
	function getSection($SectionID){
		if(empty($SectionID)){return FALSE;}
		$this->db->select('*');
		$this->db->from('tbl_media_sections');
		$this->db->where('SectionID',$SectionID);
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			return $Query->row_array();
		}else{
			return FALSE;
		}
	}


	/*
	Description: 	Use to get DeviceTypeID by DeviceTypeName
	*/
	function getDeviceTypeID($DeviceTypeName){
		if(empty($DeviceTypeName)){return FALSE;}
		$this->db->select('DeviceTypeID');
		$this->db->from('set_device_type');
		$this->db->where('DeviceTypeName',$DeviceTypeName);
		$this->db->limit(1);
		$Query = $this->db->get();	
		if($Query->num_rows()>0){
			return $Query->row()->DeviceTypeID;
		}else{
			return FALSE;
		}
	}
	/*
	Description: 	Use to get SourceID by SourceName
	*/
	function getSourceID($SourceName){
		if(empty($SourceName)){return FALSE;}
		$this->db->select('SourceID');
		$this->db->from('set_source');
		$this->db->where('SourceName',$SourceName);
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			return $Query->row()->SourceID;
		}else{
			return FALSE;
		}
	}

	/*
	Description: 	Use to get SourceID by SourceName
	*/
	function getStatusID($Status){
		if(empty($Status)){return FALSE;}
		$Query = $this->db->query("SELECT `StatusID` FROM `set_status` WHERE FIND_IN_SET('".$Status."',StatusName) LIMIT 1");	
		if($Query->num_rows()>0){
			return $Query->row()->StatusID;
		}else{
			return FALSE;
		}
	}



     /*
	Description: 	Use to get ReferralCode
	*/
	function getReferralCode($ReferralCode){
		if(empty($ReferralCode)){return FALSE;}
		$this->db->select('ReferralCodeID, UserID');
		$this->db->from('tbl_referral_codes');
		$this->db->where('ReferralCode',$ReferralCode);
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			return $Query->row();
		}else{
			return FALSE;
		}
	}


     /*
	Description: 	Use to get EntityID by MenuGUID
	*/
	function getCategoryTypeName($CategoryTypeName){
		if(empty($CategoryTypeName)){return FALSE;}
		$this->db->select('CategoryTypeID');
		$this->db->from('set_categories_type');
		$this->db->where('CategoryTypeName',$CategoryTypeName);
		$this->db->limit(1);
		$Query = $this->db->get();		
		if($Query->num_rows()>0){
			return $Query->row()->CategoryTypeID;
		}else{
			return FALSE;
		}
	}

	/*
	Description: 	Use to get UserType
	*/
	function getUserTypes($Field='', $Input=array(), $multiRecords=FALSE){

		$Params = array();
		if(!empty($Field)){
			$Params = array_map('trim',explode(',',$Field));
		}

		$this->db->select('UT.UserTypeID UserTypeIDForUse, UT.UserTypeGUID, UT.UserTypeName, UT.IsAdmin,(SELECT COUNT(*) FROM `tbl_users` WHERE `UserTypeID` = UT.UserTypeID) AS UserCount');
		$this->db->select($Field,false);
		$this->db->from('tbl_users_type UT');

		if(!empty($Input['UserTypeID'])){
			$this->db->where('UT.UserTypeID',$Input['UserTypeID']);
			$this->db->limit(1);			
		}

		if(!empty($Input['UserTypeGUID'])){
			$this->db->where("UT.UserTypeGUID",$Input['UserTypeGUID']);
			$this->db->limit(1);
		}

		/* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
		}else{
			$this->db->limit(1);
		}

		$Query = $this->db->get();
		if($Query->num_rows()>0){
			foreach($Query->result_array() as $Record){
				$ModuleData = $this->getModules("M.ModuleTitle, M.ModuleName, ModelType", array("UserTypeID" => $Record['UserTypeIDForUse'], "Permitted" => (@$Input['Permitted'] ? TRUE:'')), TRUE);
				$Record['PermittedModules'] = ($ModuleData ? $ModuleData['Data']['Records'] : new stdClass());
				$Record['UserTypeID'] 		= $Record['UserTypeIDForUse'];		
				unset($Record['UserTypeIDForUse']);		
				if(!$multiRecords){
					return $Record;
				}
				$Records[] = $Record;
			}
			$Return['Data']['Records'] = $Records;
			return $Return;
		}
		return FALSE;		
	}

	/*
	Description: 	Use to get Modules
	*/
	function getModules($Field='', $Input=array(), $multiRecords=FALSE){
		$Params = array();
		if(!empty($Field)){
			$Params = array_map('trim',explode(',',$Field));
		}
		$this->db->select($Field,false);
		$this->db->from('admin_modules M');

		if(!empty($Input['UserTypeID'])){
			if(empty($Input['Permitted'])){
				$this->db->select("IF(UTP.UserTypeID,'Yes','No') Permission,UTP.IsDefault",false);
				$this->db->join('admin_user_type_permission UTP', "M.ModuleID=UTP.ModuleID AND UTP.UserTypeID='".$Input['UserTypeID']."' ", 'left');
			}else{
				$this->db->from('admin_user_type_permission AUTP');
				$this->db->where("AUTP.ModuleID","M.ModuleID", FALSE);
				$this->db->where("AUTP.UserTypeID",$Input['UserTypeID']);		
			}
		}

		if(!empty($Input['ModuleName'])){
			$this->db->where("M.ModuleName",$Input['ModuleName']);
			$this->db->limit(1);		
		}
		$this->db->order_by('M.ModuleTitle','ASC');

		$Query = $this->db->get();
		if($Query->num_rows()>0){
			foreach($Query->result_array() as $Record){
				if(!$multiRecords){
					return $Record;
				}
				$Records[] = $Record;
			}
			$Return['Data']['Records'] = $Records;
			return $Return;
		}
		return FALSE;		
	}

	/*
	Description: 	Use to add new user type.
	*/
	public function saveUserType($Input=array()) {
		$GetGUID = get_guid();

		$InsertData = array_filter(array(
			"UserTypeGUID"			=>	$GetGUID,
			"UserTypeName" 			=>	$Input['GroupName'],
			"IsAdmin" 				=>	(!empty($Input['IsAdmin']) ? "Yes" : "No")
		));
		if(!empty($InsertData)){
			$Query 		= $this->db->insert('tbl_users_type', $InsertData);
			return array('UserTypeID' => $this->db->insert_id(), 'UserTypeGUID' => $GetGUID);
		}		
		return false;
	}


	/*
	Description: 	Use to edit user type.
	*/
	public function editUserType($UserTypeID, $Input=array()) {
		/*delete group permissions*/
		$this->db->where("UserTypeID",$UserTypeID);
		$this->db->delete('admin_user_type_permission');
		
		$UpdateData = array(
			"UserTypeName" 	=>	$Input['GroupName'],
		);
		$this->db->where("UserTypeID",$UserTypeID);
        $this->db->limit(1);
        $this->db->update('tbl_users_type', $UpdateData);

		if(!empty($Input['ModuleName'])){ /*Update permissions*/
        	array_push($Input['ModuleName'], 'dashboard');
			foreach($Input['ModuleName'] as $ModuleName){
				$ModuleData = $this->getModules("M.ModuleID", array("ModuleName" => $ModuleName));
				if(!empty($ModuleData)){
					$IsDefault = NULL;
					if ($Input['DefaultModule'] == $ModuleName) {
						$IsDefault = 'Yes';
					}
					$InsertData[] = array('UserTypeID'=>$UserTypeID,'ModuleID' => $ModuleData['ModuleID'],'IsDefault' => $IsDefault);
				}
			}
			$this->db->insert_batch('admin_user_type_permission', $InsertData); 
		}		
		return true;
	}

	/*
	Description: 	Use to delete users and user type.
	*/
	public function deleteUserType($UserTypeID, $Input=array()) {
		/*delete group */
		$this->db->where("UserTypeID",$UserTypeID);
		$this->db->delete('tbl_users_type');
		return true;
	}

	/*
	Description: use for check user type is unique edit time.
	*/
	function CheckUserTypeUnique($UserTypeName)
	{
		if (empty($UserTypeName)) {
			return FALSE;
		}
		$this->db->select('UserTypeID');
		$this->db->from('tbl_users_type');
		$this->db->where('UserTypeName', $UserTypeName);
		$this->db->limit(1);
		$Query = $this->db->get();
		if ($Query->num_rows() > 0) {
			return $Query->row()->UserTypeID;
		} else {
			return FALSE;
		}
	}


}


