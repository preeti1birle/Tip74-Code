<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Post_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}	

	/*
	Description: 	Use to get list of post.
	Note:			$Field should be comma seprated and as per selected tables alias. 
	*/
	function getPosts($Field='', $Where=array(), $multiRecords=FALSE){
		/* Define section  */
		$Return = array('Data' => array('Records' => array()));
		/* Define variables - ends */
		$this->db->select('P.PostGUID,P.Sort,P.PostID PostIDForUse, P.ParentPostID ParentPostIDForUse');
		$this->db->select($Field);
		$this->db->from('social_post P');
		$this->db->from('tbl_users U');
		$this->db->from('tbl_entity E');
		$this->db->from('tbl_entity EU');
		$this->db->where("P.EntityID","U.UserID", FALSE);
		$this->db->where("P.PostID","E.EntityID", FALSE);
		$this->db->where("P.EntityID","EU.EntityID", FALSE);

		/* Filter - start */
		if(!empty($Where['Filter'])){
			if($Where['Filter']=='Saved'){
				$this->db->from('tbl_action A');
				$this->db->where("A.EntityID", $Where['SessionUserID']);
				$this->db->where("A.ToEntityID","P.PostID", FALSE);
				$this->db->where("A.Action", 'Saved');
			}
			elseif($Where['Filter']=='Liked'){
				$this->db->from('tbl_action A');
				$this->db->where("A.EntityID", $Where['SessionUserID']);
				$this->db->where("A.ToEntityID","P.PostID", FALSE);
				$this->db->where("A.Action", 'Liked');
			}
			elseif($Where['Filter']=='MyPosts'){
				$this->db->where("P.EntityID", $Where['SessionUserID']);
				$this->db->where("P.ToEntityID", $Where['SessionUserID']);
			}		
			elseif($Where['Filter']=='Popular'){
				$this->db->order_by('E.ViewCount','DESC');
			}
		}
		/* Filter - ends */	

		if(!empty($Where['Keyword'])){ /*search in post content*/
			$this->db->group_start();
			$this->db->where('MATCH (P.PostContent) AGAINST ("'.$Where['Keyword'].'")', NULL, FALSE);
			$this->db->or_where('MATCH (P.PostCaption) AGAINST ("'.$Where['Keyword'].'")', NULL, FALSE);

			$this->db->or_like("U.FirstName", $Where['Keyword']);
			$this->db->or_like("U.LastName", $Where['Keyword']);
			$this->db->or_like("CONCAT_WS('',U.FirstName,U.Middlename,U.LastName)", preg_replace('/\s+/', '', $Where['Keyword']), FALSE);
			
			$this->db->group_end();
		}

		if(!empty($Where['ParentPostID'])){
			$this->db->where("P.ParentPostID",$Where['ParentPostID']);
		}

		if(!empty($Where['CategoryID'])){
			//$this->db->where("P.CategoryID",$Where['CategoryID']);
		}
		
		if(!empty($Where['PostType'])){
			$this->db->where("P.PostType",$Where['PostType']);
		}	


		if(!empty($Where['PostID'])){
			$this->db->where("P.PostID",$Where['PostID']);
		}

		if(!empty($Where['EntityGUID'])){
			$this->db->where("E.EntityGUID",$Where['EntityGUID']);
		}



		if(!empty($Where['SessionUserID'])){ /*skip for admin*/

			if(!empty($Where['EntityID'])){ /*viewing others wall*/
				$this->db->group_start();
				$this->db->where("P.ToEntityID",$Where['EntityID']);
				if(!empty($Where['Wall']) && $Where['Wall']=='Own'){/*Follow users posts*/
					$this->db->or_where("EXISTS(SELECT 1 FROM `social_subscribers` WHERE UserID=".$Where['SessionUserID']." AND ToEntityID=P.EntityID AND ACTION='Follow 'AND StatusID=2)", NULL, FALSE);
				}
				$this->db->group_end();
			}

			/* handling privacy - starts */
			$this->db->where("(CASE
				WHEN P.Privacy = 'Friends'
				THEN
				EXISTS(SELECT 1 FROM `social_subscribers` WHERE UserID=P.EntityID AND ToEntityID=".$Where['SessionUserID']." AND ACTION='Friend' AND StatusID=2)
				WHEN P.Privacy = 'Private'
				THEN
				P.`EntityID`=".$Where['SessionUserID']."
				WHEN P.Privacy = 'Public'
				THEN
				true
				ELSE
				false
				END
			)", NULL, FALSE);
			/* handling privacy - ends */
		}


		$this->db->order_by('P.Sort','ASC');
		/* Total records count only if want to get multiple records */
		if($multiRecords){ 
			$TempOBJ = clone $this->db;
			$TempQ = $TempOBJ->get();
			$Return['Data']['TotalRecords'] = $TempQ->num_rows();
			$this->db->limit($this->PageSize, paginationOffset($this->PageNo, $this->PageSize)); /*for pagination*/
		}else{
			$this->db->limit(1);
		}

		$Query = $this->db->get();	
		//echo $this->db->last_query();
		if($Query->num_rows()>0){
			foreach($Query->result_array() as $Record){

				/*get attached media logo - starts*/
				$MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL',array("SectionID" => 'Post',"EntityID" => $Record['PostIDForUse']),TRUE);
				$Record['Media'] = ($MediaData ? $MediaData['Data'] : array());
				/*get attached media - ends*/

				/*get parent post if shared*/
				$Record['ParentPost'] =''; /*define return variable*/
				if(!empty($Record['ParentPostIDForUse'])){
					$Record['ParentPost'] = $this->Post_model->getPosts('E.EntityGUID PostGUID, P.PostContent, P.Caption, E.EntryDate,CONCAT_WS(" ",U.FirstName,U.LastName) FullName,
						IF(U.ProfilePic = "","",CONCAT("' . IMAGE_SERVER_PATH . '",U.ProfilePic)) AS ProfilePic,', array('PostID' => $Record['ParentPostIDForUse'], 'SessionUserID' => $Where['SessionUserID'],));	
				}

				unset($Record['PostIDForUse']);
				unset($Record['ParentPostIDForUse']);
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
	Description: 	Use to add new post
	*/
	function addPost($EntityID, $ToEntityID, $Input=array()){
		$this->db->trans_start();
		$EntityGUID = get_guid();
		/* Add post to entity table and get EntityID. */
		$PostID = $this->Entity_model->addEntity($EntityGUID, array(
			"EntityTypeID"	=>	6,
			"UserID"		=>	$EntityID,
			"StatusID"		=>	2,
			"Rating"		=>	@$Input["Rating"]
		));

		/* Add post */
		$InsertData = array_filter(array(
			"PostID" 		=>	$PostID,
			"PostGUID" 		=>	$EntityGUID,
			"ParentPostID" 	=>	@$Input["ParentPostID"],
			"PostType" 		=>	$Input["PostType"],		
			"EntityID" 		=>	$EntityID,
			"CategoryID" 	=>	@$Input["CategoryID"],
			"ToEntityID" 	=> 	$ToEntityID,
			"PostContent" 	=>	@$Input["PostContent"],
			"PostCaption" 	=>	@$Input["PostCaption"]
		));
		$this->db->insert('social_post', $InsertData);
		//echo $this->db->last_query();

		/* Update entity shared count */
		if(!empty($Input["ParentPostID"])){
			$this->db->set('SharedCount', 'SharedCount+1', FALSE);
			$this->db->where('EntityID', $Input["ParentPostID"]);
			$this->db->limit(1);
			$this->db->update('tbl_entity');
		}

		/*add to category*/
		if(!empty($Input['CategoryGUIDs'])){
			/*Assign categories - starts*/
			$this->load->model('Category_model');
			foreach($Input['CategoryGUIDs'] as $CategoryGUID){
				$CategoryGUID = str_replace("string:","",$CategoryGUID);
				$CategoryData = $this->Category_model->getCategories('CategoryID', array('CategoryGUID'=>$CategoryGUID));
				if($CategoryData){
					$InsertCategory[] = array('EntityID'=>$PostID, 'CategoryID'=>$CategoryData['CategoryID']);
				}
			}
			if(!empty($InsertCategory)){
				$this->db->insert_batch('tbl_entity_categories', $InsertCategory); 		
			}
			/*Assign categories - ends*/
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}
		return array('PostID' => $PostID, 'PostGUID' => $EntityGUID);
	}

	/*
	Description: 	Use to edit post
	*/
	function editPost($PostID, $Input=array()){
		$this->db->trans_start();
		
		/* Add post */
		$InsertData = array_filter(array(
			"PostContent" 	=>	@$Input["PostContent"],
			"PostCaption" 	=>	@$Input["PostCaption"],
			"Sort" 	=>	@$Input["Sort"]
		));
		$this->db->where('PostID', $PostID);
		$this->db->limit(1);
		$this->db->update('social_post', $InsertData);
		//echo $this->db->last_query();

		
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}
		return array('PostID' => $PostID);
	}


	/*
	Description: 	Use to delete post by owner
	*/
	function deletePost($UserID, $PostID){
		$PostData=$this->getPosts('P.EntityID',array('PostID'=>$PostID, 'SessionUserID'=>$UserID));
		if(!empty($PostData) && $UserID==$PostData['EntityID']){
			$this->Entity_model->deleteEntity($PostID);
			return TRUE;
		}
		return FALSE;
	}

	function addBroadcastScheduling($Input=array()){
		
		$InsertData = array_filter(array(
			"Title" 	=>	$Input["Title"],
			"Date" 		=>	$Input["Date"],
			"Text"   	=>	$Input["Text"],
			"Push"   	=>	$Input["Push"],
			"Normal"   	=>	$Input["Normal"],
			"Redirection" => @$Input["Redirection"],

			// "PostType" 		=>	$Input["PostType"],		
		));
		$this->db->insert('tbl_broadcast_scheduling', $InsertData);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE)
		{
			return FALSE;
		}
	}

	function BroadcastSchedulingList($Input=array()){
		$check = '';
		if($Input['ID']){
			$check = "Where ID = ".$Input['ID'];
		}
		 return $this->db->query("SELECT * FROM tbl_broadcast_scheduling ". $check)->result_array();
		
	}


	function editBroadcastScheduling($Input=array(), $ID){
		
		$Data = array_filter(array(
			"Title" 	=>	$Input["Title"],
			"Date" 		=>	$Input["Date"],
			"Text"   	=>	$Input["Text"],
			"Push"   	=>	$Input["Push"],
			"Normal"   	=>	$Input["Normal"],
			"Redirection" => @$Input["Redirection"],
			// "PostType" 		=>	$Input["PostType"],		
		));

		$this->db->where('ID', $Input["ID"]);
		$this->db->limit(1);
		$this->db->update('tbl_broadcast_scheduling',$Data);
	}
}


