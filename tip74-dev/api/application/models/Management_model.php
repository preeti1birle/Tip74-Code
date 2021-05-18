<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Management_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();	
	}

	/*
	Description: 	Use to add horse data.
	*/
	function addHorse($Input=array(),$StatusID = 2){
        $this->db->trans_start();
        $EntityGUID = get_guid();
        
        /* Add to entity table and get ID. */
        $EntityID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID"=>17, "StatusID"=>$StatusID));
        
		$InsertArray = array_filter(array(
            "HorseID"    => $EntityID,
            "HorseGUID"  => $EntityGUID,
            "HorseName"  =>	$Input['HorseName'],
            "Age"        =>	@$Input['Age'],
			"Description"=>	@$Input['Description']
        ));

        $this->db->insert('tbl_horse_management', $InsertArray);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return $EntityID;
	}

	/*
	Description: 	Use to update horse data.
	*/
	function updateHorseData($HorseID, $Input=array()){

        $UpdateArray = array_filter(array(
            "HorseName"     => @$Input['HorseName'],
            "Description"   => @$Input['Description'],
            "Age"           => @$Input['Age']
        ));

		if(!empty($UpdateArray)){
			/* Update User details to users table. */
			$this->db->where('HorseID', $HorseID);
			$this->db->limit(1);
			$this->db->update('tbl_horse_management', $UpdateArray);
		}
		return TRUE;
	}


	/*
	Description: 	Use to get Horse List
	*/
	function getHorseList($Field='', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'HorseID'         => 'H.HorseID',
                'HorseName'       => 'H.HorseName',
                'Description'     => 'H.Description',
                'Age'             => 'H.Age',
                'Status'          => 'CASE E.StatusID
                                        when "2" then "Active"
                                        when "6" then "Inactive"
                                    END as Status',
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }

        $this->db->select('H.HorseGUID,H.HorseName,H.HorseID');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_entity E, tbl_horse_management H');
        $this->db->where("E.EntityID","H.HorseID",FALSE);

        if (!empty($Where['Keyword'])) {
            $this->db->like("H.HorseName", $Where['Keyword']);
        }
        if (!empty($Where['HorseID'])) {
            $this->db->where("H.HorseID", $Where['HorseID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['HorseName'])) {
            $this->db->where("H.HorseName", $Where['HorseName']);
        }

        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('E.StatusID', 'ASC');
            $this->db->order_by('H.HorseName', 'ASC');
        }

		/* Total records count only if want to get multiple records */
		if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            if ($PageNo != 0) {
                $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
            }
        } else {
            $this->db->limit(1);
        }

		$Query = $this->db->get();	
		if($Query->num_rows()>0){
          if($multiRecords)
          {  
            foreach($Query->result_array() as $Record){

                $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL',array("SectionID" => 'Race',"EntityID" => $Record['HorseID']),TRUE);
                $Record['Media'] = ($MediaData ? $MediaData['Data'] : array());
               
                if (!empty($Record['Media']['Records'])) {
                    $Record['MediaURL'] = $Record['Media']['Records'][0]['MediaURL'];
                }
                $Records[] = $Record;

            }
            $Return['Data']['Records'] = $Records;
            return $Return;
        }else{
            $Record = $Query->row_array();
            
            $MediaData = $this->Media_model->getMedia('E.EntityGUID MediaGUID, CONCAT("' . BASE_URL . '",MS.SectionFolderPath,M.MediaName) AS MediaURL',array("SectionID" => 'Race',"EntityID" => $Record['HorseID']),TRUE);
            $Record['Media'] = ($MediaData ? $MediaData['Data'] : array());
        
            if (!empty($Record['Media']['Records'])) {
                $Record['MediaURL'] = $Record['Media']['Records'][0]['MediaURL'];
            }

            return $Record;

        }
	  }
		return FALSE;		
    }
    
    /*
	Description: 	Use to add jockey data.
	*/
	function addJockey($Input=array(),$StatusID = 2){
        $this->db->trans_start();
        $EntityGUID = get_guid();
        
        /* Add to entity table and get ID. */
        $EntityID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID"=>17, "StatusID"=>$StatusID));
        
		$InsertArray = array_filter(array(
            "JockeyID"     => $EntityID,
            "JockeyGUID"   => $EntityGUID,
			"JockeyName"   => $Input['JockeyName']
        ));

        $this->db->insert('tbl_jockey_management', $InsertArray);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return $EntityID;
    }
    
    /*
	Description: 	Use to get jockey List
	*/
	function getJockeyList($Field='', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'JockeyID'        => 'J.JockeyID',
                'JockeyName'      => 'J.JockeyName',
                'Status'          => 'CASE E.StatusID
                                        when "2" then "Active"
                                        when "6" then "Inactive"
                                    END as Status',
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }

        $this->db->select('J.JockeyGUID,J.JockeyName');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_entity E, tbl_jockey_management J');
        $this->db->where("E.EntityID","J.JockeyID",FALSE);

        if (!empty($Where['Keyword'])) {
            $this->db->like("J.JockeyName", $Where['Keyword']);
        }
        if (!empty($Where['JockeyID'])) {
            $this->db->where("J.JockeyID", $Where['JockeyID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['JockeyName'])) {
            $this->db->where("J.JockeyName", $Where['JockeyName']);
        }

        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('E.StatusID', 'ASC');
            $this->db->order_by('J.JockeyName', 'ASC');
        }

		/* Total records count only if want to get multiple records */
		if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            if ($PageNo != 0) {
                $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
            }
        } else {
            $this->db->limit(1);
        }

		$Query = $this->db->get();	
		if($Query->num_rows()>0){
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                return $Return;
            } else {
                $Record = $Query->row_array();
                return $Record;
            }
		}
		return FALSE;		
    }

    /*
	Description: 	Use to update jockey data.
	*/
	function updateJockeyData($JockeyID, $Input=array()){

        $UpdateArray = array_filter(array(
            "JockeyName"     => @$Input['JockeyName']
        ));

		if(!empty($UpdateArray)){
			/* Update User details to users table. */
			$this->db->where('JockeyID', $JockeyID);
			$this->db->limit(1);
			$this->db->update('tbl_jockey_management', $UpdateArray);
		}
		return TRUE;
    }
    
    /*
	Description: 	Use to add trainer data.
	*/
	function addTrainer($Input=array(),$StatusID = 2){
        $this->db->trans_start();
        $EntityGUID = get_guid();
        
        /* Add to entity table and get ID. */
        $EntityID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID"=>17, "StatusID"=>$StatusID));
        
		$InsertArray = array_filter(array(
            "TrainerID"     => $EntityID,
            "TrainerGUID"   => $EntityGUID,
			"TrainerName"   =>	$Input['TrainerName']
        ));

        $this->db->insert('tbl_trainer_management', $InsertArray);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }

        return $EntityID;
    }

    /*
	Description: 	Use to get trainer List
	*/
	function getTrainerList($Field='', $Where=array(), $multiRecords=FALSE,  $PageNo=1, $PageSize=15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'TrainerID'       => 'T.TrainerID',
                'TrainerName'     => 'T.TrainerName',
                'Status'          => 'CASE E.StatusID
                                        when "2" then "Active"
                                        when "6" then "Inactive"
                                    END as Status',
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }

        $this->db->select('T.TrainerGUID,T.TrainerName');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_entity E, tbl_trainer_management T');
        $this->db->where("E.EntityID","T.TrainerID",FALSE);

        if (!empty($Where['Keyword'])) {
            $this->db->like("T.TrainerName", $Where['Keyword']);
        }
        if (!empty($Where['TrainerID'])) {
            $this->db->where("T.TrainerID", $Where['TrainerID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['TrainerName'])) {
            $this->db->where("T.TrainerName", $Where['TrainerName']);
        }

        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('E.StatusID', 'ASC');
            $this->db->order_by('T.TrainerName', 'ASC');
        }

		/* Total records count only if want to get multiple records */
		if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            if ($PageNo != 0) {
                $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize)); /* for pagination */
            }
        } else {
            $this->db->limit(1);
        }

		$Query = $this->db->get();	
		if($Query->num_rows()>0){
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                return $Return;
            } else {
                $Record = $Query->row_array();
                return $Record;
            }
		}
		return FALSE;		
    }

    /*
	Description: 	Use to update trainer data.
	*/
	function updateTrainerData($TrainerID, $Input=array()){

        $UpdateArray = array_filter(array(
            "TrainerName"     => @$Input['TrainerName']
        ));

		if(!empty($UpdateArray)){
			/* Update User details to users table. */
			$this->db->where('TrainerID', $TrainerID);
			$this->db->limit(1);
			$this->db->update('tbl_trainer_management', $UpdateArray);
		}
		return TRUE;
    }


}