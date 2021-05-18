<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}



	/*
	Description: 	Use to get Cetegories
	*/
	function getAttributes($Field='E.EntityGUID', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=10){

		$this->db->select($Field);
		$this->db->select('
			CASE E.StatusID
			when "2" then "Active"
			when "6" then "Inactive"
			END as Status', false);
		$this->db->from('tbl_entity E');	
		$this->db->from('set_attributes A');
		$this->db->where("E.EntityID","A.EntityID", FALSE);

		if(!empty($Where['EntityID'])){
			$this->db->where("E.EntityID",$Where['EntityID']);
		}


		$this->db->where("E.StatusID",2);

		/* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
			$this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /*for pagination*/
		}else{
			$this->db->limit(1);
		}

		$this->db->order_by('A.AttributeName','ASC');
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


	/*
	Description: 	Use to add new category
	*/
	function addCategory($ParentCategoryID='', $UserID, $CategoryTypeID, $CategoryName, $StatusID){
		$this->db->trans_start();
		$EntityGUID = get_guid();
		/* Add post to entity table and get EntityID. */
		$CategoryID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID"=>3, "UserID"=>$UserID, "StatusID"=>$StatusID));

		/* Add category */
		$InsertData = array_filter(array(
			"CategoryID" 		=>	$CategoryID,
			"CategoryGUID" 		=>	$EntityGUID,	
			"CategoryTypeID" 	=>	$CategoryTypeID,
			"ParentCategoryID" 	=>	$ParentCategoryID,
			"CategoryName" 		=>	$CategoryName
		));
		
		$this->db->insert('set_categories', $InsertData);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}
		return array('CategoryID' => $CategoryID, 'CategoryGUID' => $EntityGUID);
	}



	/*
	Description: 	Use to update category.
	*/
	function editCategory($CategoryID, $Input=array()){
		$UpdateArray = array_filter(array(
			"CategoryName" 			=>	@$Input['CategoryName']
		));

		if(!empty($UpdateArray)){
			/* Update User details to users table. */
			$this->db->where('CategoryID', $CategoryID);
			$this->db->limit(1);
			$this->db->update('set_categories', $UpdateArray);
		}

		$this->Entity_model->updateEntityInfo($CategoryID, array('StatusID'=>@$Input['StatusID']));
		return TRUE;
	}

	/*
	Description: 	Use to get Cetegories
	*/
	function getCategoryTypes($Field='E.EntityGUID', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=10){


		$this->db->select('CT.*');
		$this->db->select($Field);
		$this->db->select('
			CASE CT.StatusID
			when "2" then "Active"
			when "6" then "Inactive"
			END as Status', false);
		$this->db->from('set_categories_type CT');

		if(!empty($Where['CategoryTypeGUID'])){
			$this->db->where("CT.CategoryTypeGUID",$Where['CategoryTypeGUID']);
		}


		if(!empty($CategoryTypeData)){
			$this->db->where("CT.CategoryTypeID",$CategoryTypeData['CategoryTypeID']);
		}

		$this->db->where("CT.StatusID",2);

		/* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
			$this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /*for pagination*/
		}else{
			$this->db->limit(1);
		}

		$this->db->order_by('CT.CategoryTypeName','ASC');
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
	Description: 	Use to get Cetegories
	*/
	function getCategories($Field='', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=10){
		/*Additional fields to select*/
		$Params = array();
		if(!empty($Field)){
			$Params = array_map('trim',explode(',',$Field));
			$Field = '';
			$FieldArray = array(
				'CategoryID'				=>	'C.CategoryID',			
				'SubCategoryNames'			=>	'(SELECT GROUP_CONCAT(CategoryName SEPARATOR ", ") FROM set_categories WHERE ParentCategoryID=C.CategoryID) AS SubCategoryNames',
				'MenuOrder'					=>	'E.MenuOrder',
				'Rating'					=>	'E.Rating',
			);
			foreach($Params as $Param){
				$Field .= (!empty($FieldArray[$Param]) ? ','.$FieldArray[$Param] : '');
			}
		}

		$this->db->select('C.CategoryGUID, C.CategoryID CategoryIDForUse, C.CategoryName, CT.CategoryTypeName');
		$this->db->select($Field);
		$this->db->select('
			CASE E.StatusID
			when "2" then "Active"
			when "6" then "Inactive"
			END as Status', false);

		$this->db->from('set_categories C');
		$this->db->from('set_categories_type CT');
		$this->db->from('tbl_entity E');

		$this->db->where('C.CategoryTypeID','CT.CategoryTypeID',FALSE);
		$this->db->where('C.CategoryID','E.EntityID',FALSE);

		if(!empty($Where['StoreID'])){
			$this->db->where("C.StoreID",$Where['StoreID']);
		}

		if(!empty($Where['CategoryID'])){
			$this->db->where("C.CategoryID",$Where['CategoryID']);
		}      

		if(!empty($Where['CategoryGUID'])){
			$this->db->where("E.EntityGUID",$Where['CategoryGUID']);
		}           

		if(!empty($Where['CategoryTypeID'])){
			$this->db->where("C.CategoryTypeID",$Where['CategoryTypeID']);
		}           

		if(!empty($Where['ParentCategoryID'])){
			$this->db->where("C.ParentCategoryID",$Where['ParentCategoryID']);
		}

		if(!empty($Where['ShowOnlyParent'])){
			$this->db->where("C.ParentCategoryID is NULL", NULL, FALSE);
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

		$this->db->order_by('E.MenuOrder','ASC');
		$Query = $this->db->get();	
		if($Query->num_rows()>0){
			foreach($Query->result_array() as $Record){

				/*get attached media*/
				$MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("'.BASE_URL.'",MS.SectionFolderPath,"",M.MediaName) AS MediaThumbURL,	CONCAT(MS.SectionFolderPath,M.MediaName) AS MediaURL,	M.MediaCaption',
					array("SectionID" => "Category","EntityID" => $Record['CategoryIDForUse']), TRUE);

				$Record['Media'] = ($MediaData ? $MediaData['Data'] : new stdClass());

				$Record['SubCategories'] = array();
				$SubCategories = $this->getCategories('',array("ParentCategoryID"=>$Record['CategoryIDForUse']),true,1,25
				);	

				if($SubCategories){
					$Record['SubCategories'] = $SubCategories['Data'];
				}

				unset($Record['CategoryIDForUse']);
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




}

