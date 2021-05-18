<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}


	/*
	Description: 	Use to get Get Permitted Modules.
	*/
	function getPermittedModules($UserTypeID){
		$this->db->select('AM.*');
		$this->db->from('admin_user_type_permission AUTP');
		$this->db->from('admin_modules AM');
		$this->db->where("AUTP.ModuleID","AM.ModuleID", FALSE);
		$this->db->where("AUTP.UserTypeID",$UserTypeID);		

		$Query = $this->db->get();	
		//echo $this->db->last_query();
		if($Query->num_rows()>0){
			return $Query->result_array();
		}
		return FALSE;		
	}


	/*------------------------------*/
	/*------------------------------*/	
	function getMenu($UserTypeID, $ParentControlID=NULL) {
		$this->db->select('C.ControlID, C.ControlName, M.ModuleName,C.ModuleIcon');
		$this->db->from('admin_control C');
		$this->db->join('admin_modules M', 'C.ModuleID=M.ModuleID', 'left');
		$this->db->or_group_start(); //this will start grouping

		$this->db->where('C.ModuleID IS NULL', NULL, FALSE);
		$this->db->or_where("C.ModuleID IN (SELECT ModuleID FROM admin_user_type_permission WHERE UserTypeID=$UserTypeID )", NULL, FALSE);
    	$this->db->group_end(); //this will end grouping


    	$this->db->where('C.ParentControlID',$ParentControlID);
    	$this->db->order_by('C.Sort','ASC');
    	$query = $this->db->get();
		//echo 	$this->db->last_query();
		//exit;
    	$return = array();				
    	if($query->num_rows()>0){

    		foreach($query->result_array() as $value){
    			if(empty($value['ModuleName'])){
    				$value['ChildMenu']=$this->getMenu($UserTypeID, $value['ControlID']);
    				if(empty($value['ChildMenu'])){
    					unset($value);
    				}
    			}
    			if(!empty($value)){
    				$data[] = $value;
    			}
    		}

    	}
    	return @$data;
    }








}

