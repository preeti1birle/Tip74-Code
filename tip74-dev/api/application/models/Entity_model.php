<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Entity_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
      Description: 	Use to update order of any entity
     */

    function setOrder($sectionsid = array()) {
        if (!empty($sectionsid)) {
            $Order = 1;
            foreach ($sectionsid as $Data) {
                $Data = explode(".", $Data);
                $this->db->where('EntityID', $Data[1]);
                $this->db->limit(1);
                $this->db->update('tbl_entity', array("MenuOrder" => $Order));
                $Order++;
            }
        }
    }

    /*
      Description: 	Use to add new entity to system.
     */

    function addEntity($EntityGUID, $Input = array()) {
        $InsertData = array_filter(array(
            "EntityGUID" => $EntityGUID,
            "EntityTypeID" => $Input['EntityTypeID'],
            "CreatedByUserID" => @$Input['UserID'],
            "EntryDate" => date("Y-m-d H:i:s"),
            "StatusID" => $Input['StatusID']
        ));
        $this->db->insert('tbl_entity', $InsertData);
        $EntityID = $this->db->insert_id();
        /* add event attributes */
        //$this->addEntityAttributes($EntityID,@$Input['Attributes']);
        return $EntityID;
    }

    /*
      Description: 	Use to update user profile info.
     */

    function updateEntityInfo($EntityID, $Input = array()) {
        $UpdateArray = array_filter(array(
            "StatusID" => @$Input['StatusID'],
            "ModifiedDate" => date("Y-m-d H:i:s")
        ));

        if (!empty($UpdateArray)) {
            /* Update entity Data. */
            $this->db->where('EntityID', $EntityID);
            $this->db->limit(1);
            $this->db->update('tbl_entity', $UpdateArray);

            /* Delete Series Cache */
            // $this->db->cache_delete('sports', 'getSeries');
            //          $this->db->cache_delete('admin', 'matches');   
            //          $this->db->cache_delete('admin', 'series');

            /* Delete Matches Cache */
            // $this->db->cache_delete('contest', 'getMatches');
            // $this->db->cache_delete('sports', 'getMatches');
            // $this->db->cache_delete('admin', 'matches');
        }

        /* add event attributes */
        //$this->addEntityAttributes($EntityID,@$Input['Attributes']);
        return TRUE;
    }

    /*
      Description: 	Use to delete
     */

    function deleteEntity($EntityID = '') {
        if (empty($EntityID)) {
            return true;
        }
        $this->db->where(array("EntityID" => $EntityID));
        $this->db->delete('tbl_entity');
        $this->db->limit(1);
        return TRUE;
    }

    /*
      Description: 	Use to get single entity data.
      Note:			$Field should be comma seprated and as per selected tables alias.
     */

    function getEntity($Field = 'E.EntityGUID', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        /* Define section  */
        $Return = array('Data' => array('Records' => array()));
        /* Define variables - ends */

        $this->db->select($Field);
        $this->db->from('tbl_entity E');
        $this->db->from('tbl_entity_type ET');
        $this->db->where("E.EntityTypeID", "ET.EntityTypeID", FALSE);

        if (!empty($Where['CreatedByUserID'])) {
            $this->db->where("E.CreatedByUserID", $Where['CreatedByUserID']);
        }

        if (!empty($Where['EntityID'])) {
            $this->db->where("E.EntityID", $Where['EntityID']);
        }
        if (!empty($Where['EntityGUID'])) {
            $this->db->where("E.EntityGUID", $Where['EntityGUID']);
        }

        if (!empty($Where['EntityTypeName'])) {
            $this->db->where("ET.EntityTypeName", $Where['EntityTypeName']);
        }

        /* Total records count only if want to get multiple records */
        if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
        } else {
            $this->db->limit(1);
        }

        $Query = $this->db->get();
        //echo $this->db->last_query();
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                return $Return;
            } else {
                return $Query->row_array();
            }
        }
        return FALSE;
    }

    /*
      Description:
     */

    function action($EntityID, $ToEntityID, $Action, $Input = array()) {
        $this->db->select('1');
        $this->db->from('tbl_action');
        $this->db->where(array('EntityID' => $EntityID, 'ToEntityID' => $ToEntityID, 'Action' => $Action));
        $this->db->limit(1);
        $Query = $this->db->get();
        if ($Query->num_rows() == 0) {/* add */

            $InsertArray = array_filter(array(
                'EntityID' => $EntityID,
                'ToEntityID' => $ToEntityID,
                'Action' => $Action,
                'Text1' => @$Input['Text1'],
                'Text2' => @$Input['Text2'],
                "EntryDate" => date("Y-m-d H:i:s")
            ));
            $this->db->insert('tbl_action', $InsertArray);
            $QueryString = $Action . 'Count+1';


            /* send notification on like - starts */
            if ($Action == "Liked") {
                /* get From Entity Data */
                $EntityData = $this->Users_model->getUsers("CONCAT_WS(' ',U.FirstName,U.LastName) FullName", array("UserID" => $EntityID));
                /* get To Entity Data */
                $ToEntityData = $this->getEntity("E.EntityGUID PostGUID, E.CreatedByUserID", array("EntityID" => $ToEntityID));
                $NotificationText = $EntityData['FullName'] . " liked your post.";
                $this->Notification_model->addNotification('post_liked', $NotificationText, $EntityID, $ToEntityData['CreatedByUserID'], $ToEntityData['PostGUID']);
            }
            /* send notification on like - ends */


            $Return = 'Added';
        } else {/* remove */
            $this->db->where(array("EntityID" => $EntityID, "ToEntityID" => $ToEntityID, 'Action' => $Action));
            $this->db->delete('tbl_action');
            $this->db->limit(1);
            $QueryString = $Action . 'Count-1';
            $Return = 'Removed';
        }
        /* Update entity like count */
        $this->db->set($Action . 'Count', $QueryString, FALSE);
        $this->db->where('EntityID', $ToEntityID);
        $this->db->update('tbl_entity');
        return $Return;
    }

}
