<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}

	/*
	Description: 	Use to update page data.
	*/
	function addPage($Input=array()){
		$InsertArray = array_filter(array(
			"Title" 			=>	$Input['Title'],
			"Content" 			=>	@$Input['Content']
		));

		$this->db->select('PageGUID');
		$this->db->from('set_pages P');
		$this->db->where("P.PageGUID",$Input['PageGUID']);
		$this->db->limit(1);

		$Query = $this->db->get();	
		if($Query->num_rows()>0){
				/* Update User details to users table. */
				$this->db->where('PageGUID', $Input['PageGUID']);
				$this->db->limit(1);
				$this->db->update('set_pages', $InsertArray);
				return TRUE;
		}else{
			$InsertArray['PageGUID'] = $Input['PageGUID'];
			/* Insert Page to pages table */
			$this->db->insert('set_pages', $InsertArray);
			return TRUE;

		}

		return FALSE;
	}

	/*
	Description: 	Use to update page data.
	*/
	function editPage($PageGUID, $Input=array()){

		$Input['Content'] = htmlspecialchars($Input['Content'], ENT_QUOTES, 'UTF-8');


		$UpdateArray = array_filter(array(
			"Title" 			=>	@$Input['Title'],
			"Content" 			=>	@$Input['Content']
		));

		if(!empty($UpdateArray)){
			/* Update User details to users table. */
			$this->db->where('PageGUID', $PageGUID);
			$this->db->limit(1);
			$this->db->update('set_pages', $UpdateArray);
		}
		return TRUE;
	}


	/*
	Description: 	Use to get Page
	*/
	function getPage($Field='', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=10){
		$this->db->select($Field);
		$this->db->from('set_pages P');

		if(!empty($Where['PageGUID'])){
			$this->db->where("P.PageGUID",$Where['PageGUID']);
		}

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
				if(!$multiRecords){
					$Record['Content']= ($Record['Content']);	
					return $Record;
				}

				$Records[] = $Record;
			}
			$Return['Data']['Records'] = $Records;
			return $Return;
		}
		return FALSE;		
	}

}