<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Post_model');
		$this->load->model('Entity_model');
	}

	/*
	Name: 			posts
	Description: 	Use to add new post
	URL: 			/api/post/add	
	*/
	public function add_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('EntityGUID', 'EntityGUID', 'trim|callback_validateEntityGUID');
		$this->form_validation->set_rules('ParentPostGUID', 'ParentPostGUID', 'trim|callback_validateEntityGUID[Post,PostID]');
		$this->form_validation->set_rules('CategoryGUID', 'CategoryGUID', 'trim|callback_validateEntityGUID[Category,CategoryID]');

		$this->form_validation->set_rules('PostType', 'PostType', 'trim|required|in_list[Activity,Comment,Review,Question,Testimonial]');	
		$this->form_validation->set_rules('Privacy', 'Privacy', 'trim|in_list[Public,Friends,Private]');
		$this->form_validation->set_rules('PostContent', 'PostContent', 'trim'.(empty($this->Post['MediaGUIDs']) ? '|required' : ''));
		$this->form_validation->set_rules('MediaGUIDs', 'MediaGUIDs', 'trim'); /* Media GUIDs */
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		/* Define section */
		$this->EntityID = (!empty($this->EntityID) ? $this->EntityID : $this->SessionUserID);
		/* Define section - ends */ 

		$PostData = $this->Post_model->addPost($this->SessionUserID, $this->EntityID, array(
			"ParentPostID"	=>	@$this->PostID,
			//"CategoryID"	=>	@$this->CategoryID,
			"CategoryGUIDs"	=>	@$this->Post['CategoryGUIDs'],		
			"Privacy"		=>	(!empty($this->Post['Privacy']) ? @$this->Post['Privacy'] : 'Public'),
			"PostContent"	=>	$this->Post['PostContent'],
			"PostCaption"	=>	@$this->Post['PostCaption'],
			"PostType"		=>	$this->Post['PostType'],
			"Rating"		=>	@$this->Post['Rating']
		));

		if($PostData){
			$this->Return['Data']['PostGUID'] = $PostData['PostGUID'];

			/* check for media present - associate media with this Post */
			if(!empty($this->Post['MediaGUIDs'])){
				$MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
				foreach($MediaGUIDsArray as $MediaGUID){
					$EntityData=$this->Entity_model->getEntity('E.EntityID MediaID',array('EntityGUID'=>$MediaGUID, 'EntityTypeID'=>6));
					if ($EntityData){
						$this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $PostData['PostID']);
					}
				}
			}
			/* check for media present - associate media with this Post - ends */

			if(!empty($this->Post['EntityGUID'])){
				$EntityData=$this->Entity_model->getEntity('E.EntityTypeID',array('EntityID'=>$this->EntityID));


				if($EntityData['EntityTypeID']==1){ /*To User*/
					/*Remove Notification*/
					$this->Notification_model->removeNotification('Post', '', $this->EntityID);
					$NotificationText = "You have a new references.";

					$this->Notification_model->addNotification('Post', $NotificationText, $this->SessionUserID, $this->EntityID);
					/* send notification - ends */
				}

				elseif($EntityData['EntityTypeID']==3){ /*post in group*/
					$this->load->model('Group_model');
					$Subscribers =$this->Group_model->getSubscribers('S.UserID', array('GroupID'=>$this->EntityID), true);

					if($Subscribers){
						foreach($Subscribers['Data']['Records'] as $Value){
							/* send notification to members - starts */
							$NotificationText = $this->UserFullName." has posted a message in your group.";
							$this->Notification_model->addNotification('PostMessageToGroup', $NotificationText, $this->SessionUserID, $Value['UserID'], $this->EntityID);
							/* send notification to members - ends */
						}
					}
				}

				elseif($EntityData['EntityTypeID']==10){ /*post in event*/
					$this->load->model('Event_model');
					$Subscribers = $this->Event_model->getSubscribers('S.UserID', array('EventID'=>$this->EntityID), true);
					if($Subscribers){
						foreach($Subscribers['Data']['Records'] as $Value){
							/* send notification to members - starts */
							$NotificationText = $this->UserFullName." has posted a message in your event.";
							$this->Notification_model->addNotification('PostMessageToEvent', $NotificationText, $this->SessionUserID, $Value['UserID'], $this->EntityID);
							/* send notification to members - ends */
						}
					}					
				}
			}
		}
	}

	public function edit_post() {
        /* Validation section */
        $this->form_validation->set_rules('PostGUID', 'PostGUID', 'trim|required|callback_validateEntityGUID[Post,PostID]');
        $this->form_validation->set_rules('PostCaption', 'Caption', 'trim|required');
        $this->form_validation->set_rules('PostContent', 'Summary', 'trim|required');
        $this->form_validation->set_rules('MediaGUID', 'MediaGUID', 'trim'); /* Media GUID */
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        /* Define section */
        $this->EntityID = (!empty($this->EntityID) ? $this->EntityID : $this->SessionUserID);

        /* Define section - ends */
        if (!empty($this->Post['MediaGUID'])) {
            $EntityData = $this->Entity_model->getEntity('E.EntityID MediaID', array('EntityGUID' => $this->Post['MediaGUID'], 'EntityTypeID' => 2));
            if ($EntityData) {
                $this->Media_model->addMediaToEntity($EntityData['MediaID'], $this->SessionUserID, $this->PostID);
            }
        }
        $PostData = $this->Post_model->editPost($this->PostID, array(
            "PostContent" => $this->Post['PostContent'],
			"PostCaption" => @$this->Post['PostCaption'],
			"Sort" => @$this->Post['Sort']
        ));
        if ($PostData) {
            $this->Return['ResponseCode']=200;
            $this->Return['Data']['PostGUID'] = $this->PostID;
            /* check for media present - associate media with this Post */
        }
    }


	/*
	Name: 			getPosts
	Description: 	Use to get list of post.
	URL: 			/api/post/getPosts
	*/
	public function getPosts_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('EntityGUID', 'EntityGUID', 'trim|callback_validateEntityGUID');
		$this->form_validation->set_rules('ParentPostGUID', 'ParentPostGUID', 'trim|callback_validateEntityGUID[Post,ParentPostID]');
		$this->form_validation->set_rules('CategoryGUID', 'CategoryGUID', 'trim|callback_validateEntityGUID[Category,CategoryID]');
		$this->form_validation->set_rules('PostType', 'PostType', 'trim|required|in_list[Activity,Review,Question,Comment,Testimonial]');	
		$this->form_validation->set_rules('PostGUID', 'PostGUID', 'trim|callback_validateEntityGUID[Post,PostID]');
		$this->form_validation->set_rules('Filter', 'Filter', 'trim|in_list[Popular,Saved,MyPosts]');
		$this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
		$this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
		$this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$Posts=$this->Post_model->getPosts('
			EU.EntityGUID UserGUID,
			E.EntityGUID PostGUID,

			E.LikedCount,
			E.ViewCount,
			E.SharedCount,
			E.SavedCount,
			
		(SELECT GROUP_CONCAT(CategoryName SEPARATOR ", ") FROM set_categories WHERE set_categories.CategoryTypeID=3 AND CategoryID IN(SELECT EC.CategoryID FROM `tbl_entity_categories` EC  WHERE EC.EntityID=P.PostID)) AS CategoryNames,

			P.PostContent,
			P.PostCaption,
			P.PostType,
			E.EntryDate,
			CONCAT_WS(" ",U.FirstName,U.LastName) FullName,
			IF(U.ProfilePic = "","",CONCAT("'.''.'",U.ProfilePic)) AS ProfilePic,
			
			/* Check self liked or not */
			IF(EXISTS(SELECT 1 FROM tbl_action WHERE EntityID='.$this->SessionUserID.' AND ToEntityID=P.PostID AND Action="Liked"), "Yes", "No") AS IsLiked,

			/* Check self flagged or not */
			IF(EXISTS(SELECT 1 FROM tbl_action WHERE EntityID='.$this->SessionUserID.' AND ToEntityID=P.PostID  AND Action="Flagged"), "Yes", "No") AS IsFlagged,

			/* Check ssaved or not */
			IF(EXISTS(SELECT 1 FROM tbl_action WHERE EntityID='.$this->SessionUserID.' AND ToEntityID=P.PostID  AND Action="Saved"), "Yes", "No") AS IsSaved,

			IF(P.EntityID='.$this->SessionUserID.',"Yes", "No") AS IsMyPost

			',array(
				'Wall' 			=>	(empty($this->EntityID) ? 'Own' : 'Other'),
				'SessionUserID'	=>	$this->SessionUserID,
				'EntityID'		=>	(empty($this->EntityID) ? $this->SessionUserID : $this->EntityID),
				'PostID'		=>	@$this->PostID,
				'ParentPostID'	=>	@$this->ParentPostID,
				'CategoryID'	=>	@$this->CategoryID,	
				'Filter'		=>	@$this->Post['Filter'],
				'Keyword'		=>	@$this->Post['Keyword']
			), TRUE,  @$this->Post['PageNo'], @$this->Post['PageSize']);
		if($Posts){
			$this->Return['Data'] = $Posts['Data'];
		}
	}
	/*
	Name: 			getPost
	Description: 	Use to get single post.
	URL: 			/api/post/getPosts
	*/
	public function getPost_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('PostGUID', 'PostGUID', 'trim|required|callback_validateEntityGUID[Post,PostID]');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */

		$PostData=$this->Post_model->getPosts('
			EU.EntityGUID UserGUID,
			E.EntityGUID PostGUID,

			E.LikedCount,
			E.ViewCount,
			E.SharedCount,
			E.SavedCount,
			
			P.PostContent,
			P.PostCaption,
			E.EntryDate,
			CONCAT_WS(" ",U.FirstName,U.LastName) FullName,
			IF(U.ProfilePic = "","",CONCAT("'.''.'",U.ProfilePic)) AS ProfilePic,
			
			/* Check self liked or not */
			IF(EXISTS(SELECT 1 FROM tbl_action WHERE EntityID='.$this->SessionUserID.' AND ToEntityID=P.PostID AND Action="Liked"), "Yes", "No") AS IsLiked,
			
			/* Check self flagged or not */
			IF(EXISTS(SELECT 1 FROM tbl_action WHERE EntityID='.$this->SessionUserID.' AND ToEntityID=P.PostID  AND Action="Flagged"), "Yes", "No") AS IsFlagged,
			
			/* Check ssaved or not */
			IF(EXISTS(SELECT 1 FROM tbl_action WHERE EntityID='.$this->SessionUserID.' AND ToEntityID=P.PostID  AND Action="Saved"), "Yes", "No") AS IsSaved,

			IF(P.EntityID='.$this->SessionUserID.',"Yes", "No") AS IsMyPost

			',array(
				'PostID'		=>	$this->PostID,
				'SessionUserID'	=>	$this->SessionUserID,
			));
		if($PostData){
			// $this->Entity_model->addViewCount($this->SessionUserID, $this->PostID);
			$this->Return['Data'] = $PostData;
		}
	}


	/*
	Name: 			delete post
	Description: 	Use to delete post by owner.
	URL: 			/api/post/delete
	*/
	public function delete_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('PostGUID', 'PostGUID', 'trim|required|callback_validateEntityGUID[Post,PostID]');
		$this->form_validation->validation($this);  /* Run validation */		
		/* Validation - ends */
		if(!$this->Post_model->deletePost($this->SessionUserID, $this->PostID)){
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"You do not have permission to delete it."; 
		}
	}


	public function addBroadcastScheduling_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('Date', 'Date', 'trim|required');
		$this->form_validation->set_rules('Title', 'Date', 'trim|required');
		$this->form_validation->set_rules('Text', 'Date', 'trim|required');
		$this->form_validation->validation($this);  /* Run validation */

		if($this->Post_model->addBroadcastScheduling($this->Post)){
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"Someting went wrong try again later"; 
		}	
	}

	public function BroadcastSchedulingList_post()
	{	

		$Data = $this->Post_model->BroadcastSchedulingList($this->Post);
		if($Data){
			$this->Return['ResponseCode'] 			=	200;
			$this->Return['Message']      			=	"Success";
			$this->Return['Data']['Records']      	=	$Data;

		}else{
			$this->Return['ResponseCode'] 			=	500;
			$this->Return['Message']      			=	"Someting went wrong try again later";
		}
	}

	public function editBroadcastScheduling_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('ID', 'ID', 'trim|required');
		$this->form_validation->set_rules('Date', 'Date', 'trim|required');
		$this->form_validation->set_rules('Title', 'Date', 'trim|required');
		$this->form_validation->set_rules('Text', 'Date', 'trim|required');
		$this->form_validation->validation($this);  /* Run validation */

		if($this->Post_model->editBroadcastScheduling($this->Post,$this->Post['ID'])){
			$this->Return['ResponseCode'] 	=	500;
			$this->Return['Message']      	=	"Someting went wrong try again later"; 
		}	
	}


}
