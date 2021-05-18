<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class State_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}


	/*
	Description: 	Use to get Cetegories
	*/
	function getState($multiRecords=FALSE,  $PageNo=1, $PageSize=10){
		$this->db->select('*');
        $this->db->select('
			CASE S.Status
			when "2" then "Active"
			when "1" then "Inactive"
			END as Status', false);
        $this->db->from('set_location_state S');	
		/* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
			$this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /*for pagination*/
		}else{
			$this->db->limit(1);
		}

		$this->db->order_by('StateName','ASC');
		$Query = $this->db->get();	
		//echo $this->db->last_query();
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

    function getStateByName($name){
        $this->db->select('*');
        $this->db->select('
			CASE S.Status
			when "2" then "Active"
			when "1" then "Inactive"
			END as Status', false);
        $this->db->from('set_location_state S');
        $this->db->where("StateName",$name);
        $Query = $this->db->get();
        if($Query->num_rows()>0){
			return $Query->row();
        }
    }
    
    /*
	Description: 	Use to update category.
	*/
	function editState($Name, $Input){
        if($Input == "Active"){
            $Input['Status'] = 2;
        }else{
            $Input['Status'] = 1;
        }
        $UpdateArray = array(
			"Status" 			=>	$Input['Status']
		);

		if(!empty($UpdateArray)){
			/* Update User details to users table. */
			$this->db->where('StateName', $Name);
			$this->db->limit(1);
			$this->db->update('set_location_state', $UpdateArray);
		}
	}
}