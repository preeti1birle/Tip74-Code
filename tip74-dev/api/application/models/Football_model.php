<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Football_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        mongoDBConnection();
    }

    /*
      Description: To get all seasons
     */
    function getSeasons($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'SeasonID'      => 'S.SeasonID',
                'SeasonIDsLive' => 'S.SeasonIDsLive',
                'IsCurrentSeason' => 'S.IsCurrentSeason'
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('S.SeasonName');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('football_sports_seasons S');
        if (!empty($Where['Keyword'])) {
            $this->db->like("S.SeasonName", $Where['Keyword']);
        }
        if (!empty($Where['SeasonID'])) {
            $this->db->where("S.SeasonID", $Where['SeasonID']);
        }
        if (!empty($Where['IsCurrentSeason'])) {
            $this->db->where("S.IsCurrentSeason", $Where['IsCurrentSeason']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else {
            $this->db->order_by('S.IsCurrentSeason', 'DESC');
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
      Description: To get all season weeks
    */
    function getWeeks($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) 
    {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));

            $Field = '';
            $FieldArray = array(
                'WeekID'           => 'W.WeekID',
                'SeasonID'         => 'W.SeasonID',
                'StatusID'         => 'E.StatusID',
                'WeekCount'        => 'W.WeekCount',
                'WeekStartDate'    => 'W.WeekStartDate',
                'WeekEndDate'      => 'W.WeekEndDate',
                'Status'           => 'CASE E.StatusID
                                        when "1" then "Pending"
                                        when "2" then "Running"
                                        when "5" then "Completed"
                                    END as Status',
            );

            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('W.WeekGUID');
        if (!empty($Field)) {
            $this->db->select($Field, FALSE);
        }
        $this->db->from('tbl_entity E, football_sports_season_weeks W');
        $this->db->where("W.WeekID", "E.EntityID", FALSE);
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
        }
        if (!empty($Where['WeekID'])) {
            $this->db->where("W.WeekID", $Where['WeekID']);
        }
        if (!empty($Where['SeasonID'])) {
            $this->db->where("W.SeasonID", $Where['SeasonID']);
        }
        if (!empty($Where['WeekStartDate'])) {
            $this->db->where("W.WeekStartDate <=", $Where['WeekStartDate']);
        }
        if (!empty($Where['WeekEndDate'])) {
            $this->db->where("W.WeekEndDate >=", $Where['WeekEndDate']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
         if (!empty($Where['UpcomingWeekStatus'])) {
            if ($Where['UpcomingWeekStatus'] == 'Pending') {
               $this->db->where_in("E.StatusID", array(1, 2));
            }else{
              $this->db->where_in("E.StatusID", array(5, 2));
            }
        }
        if (!empty($Where['WeekCount'])) {
            $this->db->where("W.WeekCount", $Where['WeekCount']);
        }
         
        if (!empty($Where['OrderBy']) && $Where['OrderBy'] == 'Today') {
            $this->db->order_by('DATE(W.WeekStartDate)="' . date('Y-m-d') . '" DESC', null, FALSE);
            $this->db->order_by('E.StatusID=1 DESC', null, FALSE);
        }
        else if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else{
            $this->db->order_by('W.WeekStartDate', 'ASC');
            $this->db->order_by('E.StatusID', 'ASC');
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
      Description : To Add League Details
     */
    function addLeague($Input = array(),$CompetitionID)
    {
        $this->db->trans_start();

        $LeagueGUID = get_guid();
        $LeagueID   = $this->Entity_model->addEntity($LeagueGUID, array("EntityTypeID" => 7, "StatusID" => 2));

        /* Insert League */
        $InsertArray = array_filter(array(
            'LeagueID'      => $LeagueID,
            'LeagueGUID'    => $LeagueGUID,
            'CompetitionID' => $CompetitionID,
            'LeagueSource'  => 'Manual',
            'LeagueIDLive'  => 'L'.$LeagueID,
            'SeasonID'      => $Input['SeasonID'],
            'LeagueName'    => $Input['LeagueName']
        ));
        $this->db->insert('football_sports_leagues', $InsertArray);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $LeagueID;
    }

    /*
      Description: To get all leagues
     */
    function getLeagues($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'LeagueID'        => 'S.LeagueID',
                'SeasonID'        => 'S.SeasonID',
                'CompetitionID'   => 'S.CompetitionID',
                'StatusID'        => 'E.StatusID',
                'LeagueIDLive'    => 'S.LeagueIDLive',
                'LeagueSource'    => 'S.LeagueSource',
                'LeagueStartDate' => '(SELECT `RoundStartDate` FROM `football_sports_leagues_rounds` WHERE LeagueID = S.LeagueID ORDER BY `RoundStartDate`ASC LIMIT 1) LeagueStartDate',
                'LeagueEndDate'   => '(SELECT `RoundEndDate` FROM `football_sports_leagues_rounds` WHERE LeagueID = S.LeagueID ORDER BY `RoundEndDate`DESC LIMIT 1) LeagueEndDate',
                'LeagueFlag'      => 'IF(S.LeagueFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/LeagueFlag/","league.png"), CONCAT("' . BASE_URL . '","uploads/LeagueFlag/",S.LeagueFlag)) LeagueFlag',
                'TotalRounds'     => '(SELECT COUNT(RoundID) FROM football_sports_leagues_rounds WHERE LeagueID = S.LeagueID) TotalRounds',
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
        $this->db->select('S.LeagueGUID,S.LeagueName');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_entity E, football_sports_leagues S');
        $this->db->where("E.EntityID","S.LeagueID",FALSE);

        if (!empty($Where['Keyword'])) {
            $this->db->like("S.LeagueName", $Where['Keyword']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("S.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['CompetitionID'])) {
            $this->db->where("S.CompetitionID", $Where['CompetitionID']);
        }
        if (!empty($Where['LeagueSource'])) {
            $this->db->where("S.LeagueSource", $Where['LeagueSource']);
        }
        if (!empty($Where['SeasonID'])) {
            $this->db->where("S.SeasonID", $Where['SeasonID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['Filter']) && $Where['Filter'] == 'CurrentSeasonLeagues') {
            $this->db->where("S.SeasonID IN (SELECT SeasonID FROM football_sports_seasons WHERE IsCurrentSeason = 'Yes')",NULL,FALSE);
        }

        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('E.StatusID', 'ASC');
            $this->db->order_by('S.LeagueName', 'ASC');
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
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                if (in_array('CurrentWeekData', $Params)) {
                    $Return['Data']['CurrentWeekData'] = new stdClass();
                    $Query = $this->db->query('SELECT W.WeekGUID,W.WeekStartDate,W.WeekEndDate,CASE E.StatusID when "1" then "Pending" when "2" then "Running" when "5" then "Completed" END as Status FROM `tbl_entity` E, football_sports_season_weeks W WHERE E.EntityID = W.WeekID AND E.StatusID IN (1,2) ORDER BY W.WeekStartDate ASC LIMIT 1');
                    if($Query->num_rows() > 0){
                        $Return['Data']['CurrentWeekData'] = $Query->row_array();
                    }
                }
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('CurrentWeekData', $Params)) {
                    $Record['CurrentWeekData'] = new stdClass();
                    $Query = $this->db->query('SELECT W.WeekGUID,W.WeekStartDate,W.WeekEndDate,CASE E.StatusID when "1" then "Pending" when "2" then "Running" when "5" then "Completed" END as Status FROM `tbl_entity` E, football_sports_season_weeks W WHERE E.EntityID = W.WeekID AND E.StatusID IN (1,2) ORDER BY W.WeekStartDate ASC LIMIT 1');
                    if($Query->num_rows() > 0){
                        $Record['CurrentWeekData'] = $Query->row_array();
                    }
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description: Use to update League data.
     */
    function updateLeagueData($LeagueID,$CompetitionID, $Input = array())
    {
        $UpdateArray = array_filter(array(
            "LeagueName"   => @$Input['LeagueName'],
            "CompetitionID" => @$CompetitionID,
            "LeagueFlag"   => @$Input['LeagueFlag']
        ));

        if (!empty($UpdateArray)) {
            $this->db->where('LeagueID', $LeagueID);
            $this->db->limit(1);
            $this->db->update('football_sports_leagues', $UpdateArray);
        }
        return TRUE;
    }

    /*
      Description: Delete league to system.
    */
    function deleteLeague($LeagueID) {
        $this->db->where('EntityID', $LeagueID);
        $this->db->limit(1);
        $this->db->delete('tbl_entity');
    }

     /*
      Description : To Add Competition Details
     */
    function addCompetition($Input = array())
    {
        $this->db->trans_start();

        $CompetitionGUID = get_guid();
        $CompetitionID   = $this->Entity_model->addEntity($CompetitionGUID, array("EntityTypeID" => 18, "StatusID" => 2));

        /* Insert League */
        $InsertArray = array_filter(array(
            'CompetitionID'      => $CompetitionID,
            'CompetitionGUID'    => $CompetitionGUID,
            'CompetitionSource'  => 'Manual',
            'CompetitionIDLive'  => 'C'.$CompetitionID,
            'CompetitionName'    => $Input['CompetitionName']
        ));
        $this->db->insert('football_sports_competitions', $InsertArray);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $CompetitionID;
    }
     
     /*
      Description: To get all competitions
     */
    function getCompetitions($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'CompetitionID'        => 'S.CompetitionID',
                'StatusID'        => 'E.StatusID',
                'CompetitionIDLive'    => 'S.CompetitionIDLive',
                'CompetitionSource'    => 'S.CompetitionSource',
                'CompetitionFlag'      => 'IF(S.CompetitionFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/CompetitionFlag/","league.png"), CONCAT("' . BASE_URL . '","uploads/CompetitionFlag/",S.CompetitionFlag)) CompetitionFlag',
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
        $this->db->select('S.CompetitionGUID,S.CompetitionName');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('tbl_entity E, football_sports_competitions S');
        $this->db->where("E.EntityID","S.CompetitionID",FALSE);

        if (!empty($Where['Keyword'])) {
            $this->db->like("S.CompetitionName", $Where['Keyword']);
        }
        if (!empty($Where['CompetitionID'])) {
            $this->db->where("S.CompetitionID", $Where['CompetitionID']);
        }
        if (!empty($Where['CompetitionSource'])) {
            $this->db->where("S.CompetitionSource", $Where['CompetitionSource']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
     
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('E.StatusID', 'ASC');
            $this->db->order_by('S.CompetitionName', 'ASC');
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
        if ($Query->num_rows() > 0) {
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
      Description: Use to update Competition data.
     */
    function updateCompetitionData($CompetitionID, $Input = array())
    {
        $UpdateArray = array_filter(array(
            "CompetitionName"   => @$Input['CompetitionName'],
            "CompetitionFlag"   => @$Input['CompetitionFlag']
        ));

        if (!empty($UpdateArray)) {
            $this->db->where('CompetitionID', $CompetitionID);
            $this->db->limit(1);
            $this->db->update('football_sports_competitions', $UpdateArray);
        }
        return TRUE;
    }

      /*
      Description: Delete competition to system.
    */
    function deleteCompetition($CompetitionID) {
        $this->db->where('EntityID', $CompetitionID);
        $this->db->limit(1);
        $this->db->delete('tbl_entity');
    }


    /*
      Description: To get all venues
     */
    function getVenues($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'VenueID'       => 'S.VenueID',
                'VenueIDLive'   => 'S.VenueIDLive',
                'VenueAddress'  => 'S.VenueAddress',
                'VenueCity'     => 'S.VenueCity',
                'VenueCapicity' => 'S.VenueCapicity',
                'VenueSource'   => 'S.VenueSource',
                'VenueImage'    => 'IF(S.VenueImage IS NULL,CONCAT("' . BASE_URL . '","uploads/VenueImage/","venue.jpeg"), CONCAT("' . BASE_URL . '","uploads/VenueImage/",S.VenueImage)) VenueImage'
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('S.VenueName');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('football_sports_venues S');
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("S.VenueName", $Where['Keyword']);
            $this->db->or_like("S.VenueAddress", $Where['Keyword']);
            $this->db->or_like("S.VenueCity", $Where['Keyword']);
            $this->db->group_end();
        }
        if (!empty($Where['VenueID'])) {
            $this->db->where("S.VenueID", $Where['VenueID']);
        }
        if (!empty($Where['VenueSource'])) {
            $this->db->where("S.VenueSource", $Where['VenueSource']);
        }
        if (!empty($Where['VenueCapicity'])) {
            $this->db->where("S.VenueCapicity", $Where['VenueCapicity']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else {
            $this->db->order_by('S.VenueName', 'ASC');
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
      Description : To Add Venue Details
     */
    function addVenue($Input = array())
    {
        $InsertArray = array_filter(array(
            'VenueName'     => @$Input['VenueName'],
            'VenueIDLive'   => get_guid(),
            'VenueSource'   => 'Manual',
            'VenueAddress'  => @$Input['VenueAddress'],
            'VenueCity'     => @$Input['VenueCity'],
            'VenueCapicity' => @$Input['VenueCapicity'],
            'VenueImage'    => @$Input['VenueImage'],
            'CreatedAt'     => date('Y-m-d H:i:s')
        ));
        $this->db->insert('football_sports_venues', $InsertArray);
        $VenueID = $this->db->insert_id();
        if(!$VenueID){
            return FALSE;
        }
        return $VenueID;
    }

    /*
      Description : To Update Venue Details
     */
    function updateVenueDetails($VenueID, $Input = array())
    {
        $UpdateArray = array_filter(array(
            'VenueName'     => @$Input['VenueName'],
            'VenueAddress'  => @$Input['VenueAddress'],
            'VenueCity'     => @$Input['VenueCity'],
            'VenueCapicity' => @$Input['VenueCapicity'],
            'VenueImage'    => @$Input['VenueImage']
        ));
        if (!empty($UpdateArray)) {
            $this->db->where('VenueID', $VenueID);
            $this->db->limit(1);
            $this->db->update('football_sports_venues', $UpdateArray);
        }
        return TRUE;
    }

    /*
      Description: Delete venue to system.
    */
    function deleteVenue($VenueID) {
        $this->db->where('VenueID', $VenueID);
        $this->db->limit(1);
        $this->db->delete('football_sports_venues');
    }

    /*
      Description : To Add Team Details
     */
    function addTeam($Input = array())
    {
        $this->db->trans_start();

        $TeamGUID = get_guid();
        $TeamID   = $this->Entity_model->addEntity($TeamGUID, array("EntityTypeID" => 9, "StatusID" => 2));

        /* Insert Team */
        $InsertArray = array_filter(array(
            'TeamID'      => $TeamID,
            'TeamGUID'    => $TeamGUID,
            'TeamSource'  => 'Manual',
            'TeamIDLive'  => 'T'.$TeamID,
            'LeagueID'    => $Input['LeagueID'],
            'TeamName'    => $Input['TeamName'],
            'TeamNameShort' => $Input['TeamNameShort'],
            'TeamColor'     => $Input['TeamColor']
        ));
        $this->db->insert('football_sports_teams', $InsertArray);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $TeamID;
    }

    /*
      Description: To get all teams
    */
    function getTeams($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));

            $Field = '';
            $FieldArray = array(
                'TeamID'        => 'T.TeamID',
                'LeagueID'      => 'T.LeagueID',
                'StatusID'      => 'E.StatusID',
                'TeamIDLive'    => 'T.TeamIDLive',
                'TeamNameShort' => 'T.TeamNameShort',
                'TeamStandings' => 'T.TeamStandings',
                'TeamColor'     => 'T.TeamColor',
                'TeamPosition'  => 'T.TeamPosition',
                'TeamSource'    => 'T.TeamSource',
                'TeamFlag'      => 'IF(T.TeamFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/TeamFlag/","team.png"), CONCAT("' . BASE_URL . '","uploads/TeamFlag/",T.TeamFlag)) TeamFlag',
                'LeagueName'    => 'L.LeagueName',
                'Status'        => 'CASE E.StatusID
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
        $this->db->select('T.TeamGUID,T.TeamName');
        if (!empty($Field)) {
            $this->db->select($Field, FALSE);
        }
        $this->db->from('tbl_entity E, football_sports_teams T');
        if (!empty($Where['SeasonID']) || in_array($Params, 'LeagueName')) {
            $this->db->from('football_sports_leagues L');
            $this->db->where("T.LeagueID", "L.LeagueID", FALSE);
        }
        $this->db->where("T.TeamID", "E.EntityID", FALSE);
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("T.TeamName", $Where['Keyword']);
            $this->db->or_like("T.TeamNameShort", $Where['Keyword']);
            $this->db->group_end();
        }
        if (!empty($Where['TeamID'])) {
            $this->db->where("T.TeamID", $Where['TeamID']);
        }
        if (!empty($Where['SeasonID'])) {
            $this->db->where("L.SeasonID", $Where['SeasonID']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("T.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['TeamSource'])) {
            $this->db->where("T.TeamSource", $Where['TeamSource']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else{
            $this->db->order_by('T.TeamName', 'ASC');
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
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Records = array();
                foreach ($Query->result_array() as $key => $Record) {
                    $Records[] = $Record;
                    if (in_array('TeamStandings', $Params)) {
                        $Records[$key]['TeamStandings'] = (!empty($Record['TeamStandings'])) ? json_decode($Record['TeamStandings']) : new stdClass();
                    }
                }
                $Return['Data']['Records'] = $Records;
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('TeamStandings', $Params)) {
                    $Record['TeamStandings'] = (!empty($Record['TeamStandings'])) ? json_decode($Record['TeamStandings']) : new stdClass();
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description : To Update Team Details
     */
    function updateTeamDetails($TeamID, $Input = array())
    {
        $UpdateArray = array_filter(array(
            'TeamFlag'      => @$Input['TeamFlag'],
            'TeamName'      => @$Input['TeamName'],
            'TeamNameShort' => @$Input['TeamNameShort'],
            'TeamColor'     => @$Input['TeamColor']
        ));
        if (!empty($UpdateArray)) {
            $this->db->where('TeamID', $TeamID);
            $this->db->limit(1);
            $this->db->update('football_sports_teams', $UpdateArray);
        }
        return TRUE;
    }

    /*
      Description : To Update Team Standings
     */
    function updateTeamStandings($TeamID, $Input = array())
    {
        $TeamStandingsArr = array(
            'Position'    => (int) $Input['Position'],
            'Overall'     => array('GamePlayed' => (int) $Input['OverallGamePlayed'],'Won' => (int) $Input['OverallWon'],'Draw' => (int) $Input['OverallDraw'],'Lost' => (int) $Input['OverallLost'],'GoalFor' => (int) $Input['OverallGoalFor'],'GoalAgainst' => (int) $Input['OverallGoalAgainst']),
            'Home'        => array('GamePlayed' => (int) $Input['HomeGamePlayed'],'Won' => (int) $Input['HomeWon'],'Draw' => (int) $Input['HomeDraw'],'Lost' => (int) $Input['HomeLost']),
            'Away'        => array('GamePlayed' => (int) $Input['AwayGamePlayed'],'Won' => (int) $Input['AwayWon'],'Draw' => (int) $Input['AwayDraw'],'Lost' => (int) $Input['AwayLost']),
            'Points'      => (int) $Input['Points'],
            'GoalDifference' => (int) $Input['GoalDifference']
        );
        $this->db->where('TeamID', $TeamID);
        $this->db->limit(1);
        $this->db->update('football_sports_teams', array('TeamStandings' => json_encode($TeamStandingsArr), 'TeamPosition' => $Input['Position']));
        return TRUE;
    }

    /*
      Description: Delete Team to system.
    */
    function deleteTeam($TeamID) {
        $this->db->where('EntityID', $TeamID);
        $this->db->limit(1);
        $this->db->delete('tbl_entity');
    }

    /*
      Description: To get all league rounds
    */
    function getRounds($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) 
    {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));

            $Field = '';
            $FieldArray = array(
                'RoundID'        => 'R.RoundID',
                'LeagueID'       => 'R.LeagueID',
                'StatusID'       => 'E.StatusID',
                'RoundIDLive'    => 'R.RoundIDLive',
                'RoundStartDate' => 'R.RoundStartDate',
                'RoundEndDate'   => 'R.RoundEndDate',
                'TotalMatches'   => '(SELECT COUNT(MatchID) FROM football_sports_matches WHERE RoundID=R.RoundID) TotalMatches',
                'Status'         => 'CASE E.StatusID
                                        when "1" then "Pending"
                                        when "2" then "Running"
                                        when "5" then "Completed"
                                    END as Status'
            );

            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('R.RoundGUID,R.RoundName');
        if (!empty($Field)) {
            $this->db->select($Field, FALSE);
        }
        $this->db->from('tbl_entity E, football_sports_leagues_rounds R');
        $this->db->where("R.RoundID", "E.EntityID", FALSE);
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->like("R.RoundName", $Where['Keyword']);
        }
        if (!empty($Where['RoundID'])) {
            $this->db->where("R.RoundID", $Where['RoundID']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("R.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['RoundStartDate'])) {
            $this->db->where("R.RoundStartDate <=", $Where['RoundStartDate']);
        }
        if (!empty($Where['RoundEndDate'])) {
            $this->db->where("R.RoundEndDate >=", $Where['RoundEndDate']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['OrderBy']) && $Where['OrderBy'] == 'Today') {
            $this->db->order_by('DATE(R.RoundStartDate)="' . date('Y-m-d') . '" DESC', null, FALSE);
            $this->db->order_by('E.StatusID=1 DESC', null, FALSE);
        }
        else if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else{
            $this->db->order_by('R.RoundName', 'ASC');
            $this->db->order_by('E.StatusID', 'ASC');
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
      Description : To Add Match Details
     */
    function addMatch($Input = array())
    {
        $this->db->trans_start();

        $MatchGUID = get_guid();
        $MatchID   = $this->Entity_model->addEntity($MatchGUID, array("EntityTypeID" => 8, "StatusID" => 1));

        /* Insert Match */
        sscanf(str_replace(array("+","-"), array("",""), $Input['TimeZoneIdentifire']), "%d:%d", $Hours, $Minutes);
        $SecondsDifference = $Hours * 3600 + $Minutes * 60 ;
        if (strpos($Input['TimeZoneIdentifire'], '+') !== false) { // + => -
          $MatchStartDateTime = date('Y-m-d H:i:s',(strtotime($Input['MatchStartDateTime']) - $SecondsDifference));
        }else{ // - => +
          $MatchStartDateTime = date('Y-m-d H:i:s',(strtotime($Input['MatchStartDateTime']) + $SecondsDifference));
        }
        $InsertArray = array_filter(array(
            'MatchID'       => $MatchID,
            'MatchGUID'     => $MatchGUID,
            'MatchSource'   => 'Manual',
            'MatchIDLive'   => 'M'.$MatchID,
            'LeagueID'      => $Input['LeagueID'],
            'WeekID'        => $Input['WeekID'],
            'VenueID'       => $Input['VenueID'],
            'TeamIDLocal'   => $Input['TeamIDLocal'],
            'TeamIDVisitor' => $Input['TeamIDVisitor'],
            'MatchStartDateTime' => $MatchStartDateTime // UTC
        ));
        $this->db->insert('football_sports_matches', $InsertArray);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /*
      Description: To get all fixtures/matches
    */
    function getMatches($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) 
    {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));

            $Field = '';
            $FieldArray = array(
                'MatchID'               => 'M.MatchID',
                'MatchIDLive'           => 'M.MatchIDLive',
                'LeagueID'              => 'M.LeagueID',
                'RoundID'               => 'M.RoundID',
                'WeekID'                => 'M.WeekID',
                'VenueID'               => 'M.VenueID',
                'MatchSource'           => 'M.MatchSource',
                'IsTeamLineUp'          => 'M.IsTeamLineUp',
                'LongestOddsLabel'      => 'M.LongestOddsLabel',
                'TeamIDLocal'           => 'M.TeamIDLocal',
                'TeamIDVisitor'         => 'M.TeamIDVisitor',
                'StatusID'              => 'E.StatusID',
                'MatchScoreDetails'     => 'M.MatchScoreDetails',
                'FullTimePredictionStatics' => 'M.FullTimePredictionStatics',
                'HalfTimePredictionStatics' => 'M.HalfTimePredictionStatics',
                'LeagueGUID'            => 'L.LeagueGUID',
                'LeagueName'            => 'L.LeagueName',
                'LeagueFlag'            => 'IF(L.LeagueFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/LeagueFlag/","league.png"), CONCAT("' . BASE_URL . '","uploads/LeagueFlag/",L.LeagueFlag)) LeagueFlag',
                'RoundGUID'             => 'R.RoundGUID',
                'RoundName'             => 'R.RoundName',
                'RoundStartDate'        => 'DATE_FORMAT(CONVERT_TZ(R.RoundStartDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "%Y-%m-%d") RoundStartDate',
                'RoundEndDate'          => 'DATE_FORMAT(CONVERT_TZ(R.RoundEndDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "%Y-%m-%d") RoundEndDate',
                'VenueName'             => 'V.VenueName',
                'VenueAddress'          => 'V.VenueAddress',
                'VenueCity'             => 'V.VenueCity',
                'VenueCapicity'         => 'V.VenueCapicity',
                'VenueImage'            => 'IF(V.VenueImage IS NULL,CONCAT("' . BASE_URL . '","uploads/VenueImage/","venue.jpeg"), CONCAT("' . BASE_URL . '","uploads/VenueImage/",V.VenueImage)) VenueImage',
                'TeamIDLocal'           => 'TL.TeamID AS TeamIDLocal',
                'TeamIDVisitor'         => 'TV.TeamID AS TeamIDVisitor',
                'TeamIDLiveLocal'       => 'TL.TeamIDLive AS TeamIDLiveLocal',
                'TeamIDLiveVisitor'     => 'TV.TeamIDLive AS TeamIDLiveVisitor',
                'TeamGUIDLocal'         => 'TL.TeamGUID AS TeamGUIDLocal',
                'TeamGUIDVisitor'       => 'TV.TeamGUID AS TeamGUIDVisitor',
                'TeamNameLocal'         => 'TL.TeamName AS TeamNameLocal',
                'TeamNameVisitor'       => 'TV.TeamName AS TeamNameVisitor',
                'TeamNameShortLocal'    => 'TL.TeamNameShort AS TeamNameShortLocal',
                'TeamNameShortVisitor'  => 'TV.TeamNameShort AS TeamNameShortVisitor',
                'TeamColorLocal'        => 'TL.TeamColor AS TeamColorLocal',
                'TeamColorVisitor'      => 'TV.TeamColor AS TeamColorVisitor',
                'TeamStandingsLocal'    => 'TL.TeamStandings AS TeamStandingsLocal',
                'TeamStandingsVisitor'  => 'TV.TeamStandings AS TeamStandingsVisitor',
                'TeamFlagLocal'         => 'IF(TL.TeamFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/TeamFlag/","team.png"), CONCAT("' . BASE_URL . '","uploads/TeamFlag/",TL.TeamFlag)) TeamFlagLocal',
                'TeamFlagVisitor'       => 'IF(TV.TeamFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/TeamFlag/","team.png"), CONCAT("' . BASE_URL . '","uploads/TeamFlag/",TV.TeamFlag)) TeamFlagVisitor',
                'MatchStartDateTime'    => 'DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . (!empty($Where['TimeZone']) ? $Where['TimeZone'] : DEFAULT_TIMEZONE) . '"), "' . DATE_FORMAT . '") MatchStartDateTime',
                'CurrentDateTime'       => 'DATE_FORMAT(CONVERT_TZ(Now(),"+00:00","' . (!empty($Where['TimeZone']) ? $Where['TimeZone'] : DEFAULT_TIMEZONE) . '"), "' . DATE_FORMAT . ' ") CurrentDateTime',
                'MatchDate'             => 'DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . (!empty($Where['TimeZone']) ? $Where['TimeZone'] : DEFAULT_TIMEZONE) . '"), "%Y-%m-%d") MatchDate',
                'MatchTime'             => 'DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . (!empty($Where['TimeZone']) ? $Where['TimeZone'] : DEFAULT_TIMEZONE) . '"), "%H:%i:%s") MatchTime',
                'MatchStartDateTimeUTC' => 'M.MatchStartDateTime as MatchStartDateTimeUTC',
                'IsPredicted'           => '(SELECT IF( EXISTS(SELECT 1 FROM football_sports_matches_prediction
                                                        WHERE football_sports_matches_prediction.MatchID =  M.MatchID AND UserID = ' . $Where['SessionUserID'] . ' LIMIT 1), "Yes", "No")) IsPredicted',
                /*'canPredict'           => '(SELECT IF((SELECT COUNT(MatchID) FROM football_sports_matches_prediction
                                                        WHERE football_sports_matches_prediction.MatchID =  M.MatchID AND UserID = ' . $Where['SessionUserID'] . ' LIMIT 1) < 5, "Yes", "No")) canPredict',*/ 
                'PredictionDetails'     => "(SELECT CONCAT('[',GROUP_CONCAT(JSON_OBJECT('MatchPredictionID',MatchPredictionID,'TeamScoreLocalFT',TeamScoreLocalFT, 'TeamScoreVisitorFT', TeamScoreVisitorFT,'TeamScoreLocalHT',TeamScoreLocalHT, 'TeamScoreVisitorHT', TeamScoreVisitorHT, 'PredictionStatus' ,PredictionStatus, 'IsDoubleUps' ,IsDoubleUps,'ExactScorePoints' , ExactScorePoints,'CorrectResultPoints' , CorrectResultPoints,'BothTeamScorePoints' , BothTeamScorePoints,'LongestOddsScorePoints',LongestOddsScorePoints, 'SavedDateTime',DATE_FORMAT(CONVERT_TZ(SavedDateTime,'+00:00','" . DEFAULT_TIMEZONE . "'), '" . DATE_FORMAT . "'), 'LockedDateTime', DATE_FORMAT(CONVERT_TZ(LockedDateTime,'+00:00','" . DEFAULT_TIMEZONE . "'), '" . DATE_FORMAT . "') )),']') FROM  football_sports_matches_prediction WHERE  MatchID = M.MatchID AND UserID = " . $Where['SessionUserID'] . ") PredictionDetails",                                                        
                'HomeAway'              => 'IF(M.TeamIDLocal = '.$Where['TeamID'].',"Home","Away") HomeAway',
                'OpponentTeamName'      => 'IF(M.TeamIDLocal = '.$Where['TeamID'].',TV.TeamName,TL.TeamName) OpponentTeamName',
                'ResultStatus'          => 'IF(JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Where['TeamID'].',"Won",IF(JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "","Draw","Lost")) ResultStatus',    
                'Status'                => 'CASE E.StatusID
                                                when "1" then "Pending"
                                                when "2" then "Running"
                                                when "3" then "Cancelled"
                                                when "5" then "Completed"  
                                                when "8" then "Abandoned"  
                                                when "9" then "No Result" 
                                            END as Status'
            );

            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('M.MatchGUID');
        if (!empty($Field)) {
            $this->db->select($Field, FALSE);
        }
        if (!empty($Params) && array_keys_exist($Params, array('TeamLastThreeMatchesLocal','TeamLastThreeMatchesVisitor'))) {
            $this->db->select('M.TeamIDLocal TeamIDLocalAsUse, M.TeamIDVisitor TeamIDVisitorAsUse');
        }
        $this->db->from('tbl_entity E, football_sports_matches M');
        $this->db->where("M.MatchID", "E.EntityID", FALSE);
        
        if (!empty($Where['Keyword']) || array_keys_exist($Params, array('LeagueGUID','LeagueName','LeagueFlag'))) {
            $this->db->from('football_sports_leagues L');
            $this->db->where("M.LeagueID", "L.LeagueID", FALSE);
        }
        if (!empty($Where['Keyword']) || array_keys_exist($Params, array('RoundGUID','RoundName','RoundStartDate','RoundEndDate'))) {
            $this->db->from('football_sports_leagues_rounds R');
            $this->db->where("M.RoundID", "R.RoundID", FALSE);
        }
        if (!empty($Where['Keyword']) || array_keys_exist($Params, array('VenueName','VenueAddress','VenueCity','VenueCapicity','VenueImage'))) {
            $this->db->from('football_sports_venues V');
            $this->db->where("M.VenueID", "V.VenueID", FALSE);
        }
        if (!empty($Where['Keyword']) || array_keys_exist($Params, array('TeamGUIDLocal','TeamGUIDVisitor','TeamNameLocal','TeamNameVisitor','TeamNameShortLocal','TeamNameShortVisitor','TeamFlagLocal','TeamFlagVisitor','TeamColorLocal','TeamColorVisitor','TeamStandingsLocal','TeamStandingsVisitor','OpponentTeamName','TeamIDLocal','TeamIDVisitor','TeamIDLiveLocal','TeamIDLiveVisitor'))) {
            $this->db->from('football_sports_teams TL, football_sports_teams TV');
            $this->db->where("M.TeamIDLocal", "TL.TeamID", FALSE);
            $this->db->where("M.TeamIDVisitor", "TV.TeamID", FALSE);
        }
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("L.LeagueName", $Where['Keyword']);
            $this->db->or_like("R.RoundName", $Where['Keyword']);
            $this->db->or_like("V.VenueName", $Where['Keyword']);
            $this->db->or_like("V.VenueAddress", $Where['Keyword']);
            $this->db->or_like("V.VenueCity", $Where['Keyword']);
            $this->db->or_like("TL.TeamName", $Where['Keyword']);
            $this->db->or_like("TV.TeamName", $Where['Keyword']);
            $this->db->or_like("TL.TeamNameShort", $Where['Keyword']);
            $this->db->or_like("TV.TeamNameShort", $Where['Keyword']);
            $this->db->group_end();
        }
        if (!empty($Where['MatchID'])) {
            $this->db->where("M.MatchID", $Where['MatchID']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("M.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['RoundID'])) {
            $this->db->where("M.RoundID", $Where['RoundID']);
        }
        if (!empty($Where['WeekID'])) {
            $this->db->where("M.WeekID", $Where['WeekID']);
        }
        if (!empty($Where['MatchSource'])) {
            $this->db->where("M.MatchSource", $Where['MatchSource']);
        }
        if (!empty($Where['TeamIDLocal'])) {
            $this->db->where("M.TeamIDLocal", $Where['TeamIDLocal']);
        }
        if (!empty($Where['TeamIDVisitor'])) {
            $this->db->where("M.TeamIDVisitor", $Where['TeamIDVisitor']);
        }
        if (!empty($Where['IsTeamLineUp'])) {
            $this->db->where("M.IsTeamLineUp", $Where['IsTeamLineUp']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['IsPredicted'])) {
            $this->db->having("IsPredicted", $Where['IsPredicted']);
        }
        if (!empty($Where['Filters']) && in_array('BothTeamMatches', $Where['Filters'])) {
            $this->db->where("((TeamIDLocal = ".$Where['TeamIDL']." AND TeamIDVisitor = ".$Where['TeamIDV'].") OR (TeamIDLocal = ".$Where['TeamIDV']." AND TeamIDVisitor = ".$Where['TeamIDL']."))", NULL, FALSE);
        }
        if (!empty($Where['Filters']) && in_array('TeamCompletedMatches', $Where['Filters'])) {
            $this->db->where("E.StatusID", 5);
            $this->db->group_start();
            $this->db->where("M.TeamIDLocal", $Where['TeamID']);
            $this->db->or_where("M.TeamIDVisitor", $Where['TeamID']);
            $this->db->group_end();
        }
        if (!empty($Where['OrderByToday']) && $Where['OrderByToday'] == "Yes") {
            $this->db->order_by('DATE(M.MatchStartDateTime)="' . date('Y-m-d') . '" DESC', null, FALSE);
            $this->db->order_by('E.StatusID=1 DESC', null, FALSE);        
        }else if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else{
            $this->db->order_by('E.StatusID', 'ASC');
            $this->db->order_by('M.MatchStartDateTime', 'ASC');
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
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Records = array();
                if (!empty($Where['Filters']) && in_array('DateWiseMatches', $Where['Filters'])) {
                    $MatchesData = $Query->result_array();
                    $AllDates = array_values(array_unique(array_column($MatchesData, 'MatchDate')));
                    $Return['Data']['TotalRecords'] = count($AllDates);
                    foreach($AllDates as $Index => $Date){
                        $Matches = array();
                        $I = 0;
                        foreach ($MatchesData as $Record) {
                            if($Record['MatchDate'] != $Date){
                                continue;
                            }
                            $Matches[] = $Record;
                            if (in_array('TeamStandingsLocal', $Params)) {
                                $Matches[$I]['TeamStandingsLocal'] = (!empty($Record['TeamStandingsLocal'])) ? json_decode($Record['TeamStandingsLocal']) : new stdClass();
                            }
                            if (in_array('TeamStandingsVisitor', $Params)) {
                                $Matches[$I]['TeamStandingsVisitor'] = (!empty($Record['TeamStandingsVisitor'])) ? json_decode($Record['TeamStandingsVisitor']) : new stdClass();
                            }
                            if (in_array('MatchScoreDetails', $Params)) {
                                $Matches[$I]['MatchScoreDetails'] = (!empty($Record['MatchScoreDetails'])) ? json_decode($Record['MatchScoreDetails']) : new stdClass();
                            }
                            if (in_array('PredictionDetails', $Params)) {
                                $Matches[$I]['PredictionDetails'] = (!empty($Record['PredictionDetails'])) ? json_decode($Record['PredictionDetails'],TRUE) : array();
                                /*if (!empty($Record['PredictionDetails']) && $Matches[$I]['PredictionDetails']['PredictionStatus'] == 'Lock') {
                                    $Matches[$I]['FullTimePredictionStatics'] = json_decode($Record['FullTimePredictionStatics']);
                                    $Matches[$I]['HalfTimePredictionStatics'] = json_decode($Record['HalfTimePredictionStatics']);
                                }else{
                                    $Matches[$I]['FullTimePredictionStatics'] = $Matches[$I]['HalfTimePredictionStatics'] = new stdClass();
                                }*/
                            }
                            if (in_array('TeamLastThreeMatchesLocal', $Params)) {
                                $Matches[$I]['TeamLastThreeMatchesLocal'] = array();
                                $Query = $this->db->query('SELECT CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "" THEN "Draw" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Matches[$I]['TeamIDLocalAsUse'].' THEN "Won" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) != '.$Matches[$I]['TeamIDLocalAsUse'].' THEN "Lost" END AS WinnerTeamStatus FROM tbl_entity E, `football_sports_matches` M WHERE E.EntityID = M.MatchID AND E.StatusID = 5 AND( M.TeamIDLocal = '.$Matches[$I]['TeamIDLocalAsUse'].' OR M.TeamIDVisitor = '.$Matches[$I]['TeamIDLocalAsUse'].' ) ORDER BY M.MatchStartDateTime DESC LIMIT 3');
                                if($Query->num_rows() > 0){
                                    $Matches[$I]['TeamLastThreeMatchesLocal'] = array_column($Query->result_array(), 'WinnerTeamStatus');
                                }
                                unset($Matches[$I]['TeamIDLocalAsUse']);
                            }
                            if (in_array('TeamLastThreeMatchesVisitor', $Params)) {
                                $Matches[$I]['TeamLastThreeMatchesVisitor'] = array();
                                $Query = $this->db->query('SELECT CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "" THEN "Draw" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Matches[$I]['TeamIDVisitorAsUse'].' THEN "Won" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) != '.$Matches[$I]['TeamIDVisitorAsUse'].' THEN "Lost" END AS WinnerTeamStatus FROM tbl_entity E, `football_sports_matches` M WHERE E.EntityID = M.MatchID AND E.StatusID = 5 AND( M.TeamIDLocal = '.$Matches[$I]['TeamIDVisitorAsUse'].' OR M.TeamIDVisitor = '.$Matches[$I]['TeamIDVisitorAsUse'].' ) ORDER BY M.MatchStartDateTime DESC LIMIT 3');
                                if($Query->num_rows() > 0){
                                    $Matches[$I]['TeamLastThreeMatchesVisitor'] = array_column($Query->result_array(), 'WinnerTeamStatus');
                                }
                                unset($Matches[$I]['TeamIDVisitorAsUse']);
                            }
                            $I++;
                        }
                        if(!empty($Matches)){
                            $Records[$Index]['MatchDate'] = $Date;
                            $Records[$Index]['Matches']   = $Matches;
                        }
                    }
                }else{
                    foreach ($Query->result_array() as $key => $Record) {
                        $Records[] = $Record;
                        if (in_array('TeamStandingsLocal', $Params)) {
                            $Records[$key]['TeamStandingsLocal'] = (!empty($Record['TeamStandingsLocal'])) ? json_decode($Record['TeamStandingsLocal']) : new stdClass();
                        }
                        if (in_array('TeamStandingsVisitor', $Params)) {
                            $Records[$key]['TeamStandingsVisitor'] = (!empty($Record['TeamStandingsVisitor'])) ? json_decode($Record['TeamStandingsVisitor']) : new stdClass();
                        }
                        if (in_array('MatchScoreDetails', $Params)) {
                            $Records[$key]['MatchScoreDetails'] = (!empty($Record['MatchScoreDetails'])) ? json_decode($Record['MatchScoreDetails']) : new stdClass();
                        }
                        if (in_array('PredictionDetails', $Params)) {
                            $Records[$key]['PredictionDetails'] = (!empty($Record['PredictionDetails'])) ? json_decode($Record['PredictionDetails'],TRUE) : array();
                            /*if (!empty($Record['PredictionDetails']) && $Records[$key]['PredictionDetails']['PredictionStatus'] == 'Lock') {
                                $Records[$key]['FullTimePredictionStatics'] = json_decode($Record['FullTimePredictionStatics']);
                                $Records[$key]['HalfTimePredictionStatics'] = json_decode($Record['HalfTimePredictionStatics']);
                            }else{
                                $Records[$key]['FullTimePredictionStatics'] = $Record['HalfTimePredictionStatics'] = new stdClass();
                            }*/
                        }
                        if (in_array('TeamLastThreeMatchesLocal', $Params)) {
                            $Records[$key]['TeamLastThreeMatchesLocal'] = array();
                            $Query = $this->db->query('SELECT CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "" THEN "Draw" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Records[$key]['TeamIDLocalAsUse'].' THEN "Won" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) != '.$Records[$key]['TeamIDLocalAsUse'].' THEN "Lost" END AS WinnerTeamStatus FROM tbl_entity E, `football_sports_matches` M WHERE E.EntityID = M.MatchID AND E.StatusID = 5 AND( M.TeamIDLocal = '.$Records[$key]['TeamIDLocalAsUse'].' OR M.TeamIDVisitor = '.$Records[$key]['TeamIDLocalAsUse'].' ) ORDER BY M.MatchStartDateTime DESC LIMIT 3');
                            if($Query->num_rows() > 0){
                                $Records[$key]['TeamLastThreeMatchesLocal'] = array_column($Query->result_array(), 'WinnerTeamStatus');
                            }
                            unset($Records[$key]['TeamIDLocalAsUse']);
                        }
                        if (in_array('TeamLastThreeMatchesVisitor', $Params)) {
                            $Records[$key]['TeamLastThreeMatchesVisitor'] = array();
                            $Query = $this->db->query('SELECT CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "" THEN "Draw" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Records[$key]['TeamIDVisitorAsUse'].' THEN "Won" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) != '.$Records[$key]['TeamIDVisitorAsUse'].' THEN "Lost" END AS WinnerTeamStatus FROM tbl_entity E, `football_sports_matches` M WHERE E.EntityID = M.MatchID AND E.StatusID = 5 AND( M.TeamIDLocal = '.$Records[$key]['TeamIDVisitorAsUse'].' OR M.TeamIDVisitor = '.$Records[$key]['TeamIDVisitorAsUse'].' ) ORDER BY M.MatchStartDateTime DESC LIMIT 3');
                            if($Query->num_rows() > 0){
                                $Records[$key]['TeamLastThreeMatchesVisitor'] = array_column($Query->result_array(), 'WinnerTeamStatus');
                            }
                            unset($Records[$key]['TeamIDVisitorAsUse']);
                        }
                    }
                }
                $Return['Data']['Records'] = $Records;
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('TeamStandingsLocal', $Params)) {
                    $Record['TeamStandingsLocal'] = (!empty($Record['TeamStandingsLocal'])) ? json_decode($Record['TeamStandingsLocal']) : new stdClass();
                }
                if (in_array('TeamStandingsVisitor', $Params)) {
                    $Record['TeamStandingsVisitor'] = (!empty($Record['TeamStandingsVisitor'])) ? json_decode($Record['TeamStandingsVisitor']) : new stdClass();
                }
                if (in_array('MatchScoreDetails', $Params)) {
                    $Record['MatchScoreDetails'] = (!empty($Record['MatchScoreDetails'])) ? json_decode($Record['MatchScoreDetails']) : new stdClass();
                }
                if (in_array('PredictionDetails', $Params)) {
                    $Record['PredictionDetails'] = (!empty($Record['PredictionDetails'])) ? json_decode($Record['PredictionDetails'],TRUE) : array();
                    /*if (!empty((array) $Record['PredictionDetails']) && $Record['PredictionDetails']['PredictionStatus'] == 'Lock') {
                        $Record['FullTimePredictionStatics'] = json_decode($Record['FullTimePredictionStatics']);
                        $Record['HalfTimePredictionStatics'] = json_decode($Record['HalfTimePredictionStatics']);
                    }else{
                        $Record['FullTimePredictionStatics'] = $Record['HalfTimePredictionStatics'] = new stdClass();
                    }*/
                }
                if (in_array('TeamLastThreeMatchesLocal', $Params)) {
                    $Record['TeamLastThreeMatchesLocal'] = array();
                    $Query = $this->db->query('SELECT CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "" THEN "Draw" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Record['TeamIDLocalAsUse'].' THEN "Won" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) != '.$Record['TeamIDLocalAsUse'].' THEN "Lost" END AS WinnerTeamStatus FROM tbl_entity E, `football_sports_matches` M WHERE E.EntityID = M.MatchID AND E.StatusID = 5 AND( M.TeamIDLocal = '.$Record['TeamIDLocalAsUse'].' OR M.TeamIDVisitor = '.$Record['TeamIDLocalAsUse'].' ) ORDER BY M.MatchStartDateTime DESC LIMIT 3');
                    if($Query->num_rows() > 0){
                        $Record['TeamLastThreeMatchesLocal'] = array_column($Query->result_array(), 'WinnerTeamStatus');
                    }
                    unset($Record['TeamIDLocalAsUse']);
                }
                if (in_array('TeamLastThreeMatchesVisitor', $Params)) {
                    $Record['TeamLastThreeMatchesVisitor'] = array();
                    $Query = $this->db->query('SELECT CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = "" THEN "Draw" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) = '.$Record['TeamIDVisitorAsUse'].' THEN "Won" WHEN JSON_UNQUOTE(JSON_EXTRACT(M.`MatchScoreDetails`, "$.WinnerTeamID")) != '.$Record['TeamIDVisitorAsUse'].' THEN "Lost" END AS WinnerTeamStatus FROM tbl_entity E, `football_sports_matches` M WHERE E.EntityID = M.MatchID AND E.StatusID = 5 AND( M.TeamIDLocal = '.$Record['TeamIDVisitorAsUse'].' OR M.TeamIDVisitor = '.$Record['TeamIDVisitorAsUse'].' ) ORDER BY M.MatchStartDateTime DESC LIMIT 3');
                    if($Query->num_rows() > 0){
                        $Record['TeamLastThreeMatchesVisitor'] = array_column($Query->result_array(), 'WinnerTeamStatus');
                    }
                    unset($Record['TeamIDVisitorAsUse']);
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description : To Update Match Live Scoring Details
     */
    function manageLiveScoring($MatchID, $Input = array())
    {
        $this->db->trans_start();

        /* Insert Match */
        $LiveScoringArr = array(
            'LocalTeamScore'   => $Input['FullTimeLocalTeamScore'],
            'VisitorTeamScore' => $Input['FullTimeVisitorTeamScore'],
            'HalfTimeScore'    => $Input['HalfTimeLocalTeamScore']."-".$Input['HalfTimeVisitorTeamScore'],
            'FullTimeScore'    => $Input['FullTimeLocalTeamScore']."-".$Input['FullTimeVisitorTeamScore'],
            'WinnerTeam'       => $Input['WinnerTeam'],
            'WinnerTeamID'     => (!empty($Input['WinnerTeamID'])) ? $Input['WinnerTeamID'] : ''
        );
        $this->db->where('MatchID', $MatchID);
        $this->db->limit(1);
        $this->db->update('football_sports_matches', array_filter(array('MatchScoreDetails' => json_encode($LiveScoringArr), 'LongestOddsLabel' => @$Input['LongestOdds'])));

        /* Update Match Status */
        $this->db->query('UPDATE `tbl_entity` SET `StatusID` = '.$Input['StatusID'].'  WHERE `EntityID`= '.$MatchID.' LIMIT 1');

        /* Update Points & Leaderboard On Match Completing */
        if($Input['Status'] == 'Completed'){
            $this->calculatePoints($MatchID,$LiveScoringArr,@$Input['LongestOdds']);
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /*
      Description: Delete Match to system.
    */
    function deleteMatch($MatchID) {
        $this->db->where('EntityID', $MatchID);
        $this->db->limit(1);
        $this->db->delete('tbl_entity');
    }

    /*
      Description : To Assign Match Players
     */
    function assignPlayers($Input = array())
    {
        $this->db->trans_start();

        /* To Delete Already Exist Player */
        $this->db->query('DELETE FROM `football_sports_team_players` WHERE MatchID = '.$Input['MatchID']);

        /* Insert Match Players */
        foreach ($Input['TeamPlayers'] as $Key => $Value) {
            $MatchPlayers[] = array(
                                'MatchID'    => $Input['MatchID'],
                                'LeagueID'   => $Input['LeagueID'],
                                'TeamID'     => $Value['TeamID'],
                                'PlayerID'   => $Value['PlayerID'],
                                'PlayerRole' => $Value['PlayerRole']
                            );
        }
        $this->db->insert_batch('football_sports_team_players',$MatchPlayers);

        /* Update Team Lineup Flag */
        $this->db->where(array('MatchID' => $Input['MatchID'], 'IsTeamLineUp' => 'No'));
        $this->db->limit(1);
        $this->db->update('football_sports_matches',array('IsTeamLineUp' => 'Yes'));
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $PlayerID;
    }

    /*
      Description: To get teams historic results.
    */
    function getTeamsHistoricResults($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));

            $Field = '';
            $FieldArray = array(
                'TeamID'        => 'T.TeamID',
                'LeagueID'      => 'T.LeagueID',
                'StatusID'      => 'E.StatusID',
                'TeamIDLive'    => 'T.TeamIDLive',
                'TeamNameShort' => 'T.TeamNameShort',
                'TeamStandings' => 'T.TeamStandings',
                'TeamColor'     => 'T.TeamColor',
                'TeamPosition'  => 'T.TeamPosition',
                'TeamFlag'      => 'IF(T.TeamFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/TeamFlag/","team.png"), CONCAT("' . BASE_URL . '","uploads/TeamFlag/",T.TeamFlag)) TeamFlag',
                'LeagueName'    => 'L.LeagueName',
                'Status'        => 'CASE E.StatusID
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
        $this->db->select('T.TeamGUID,T.TeamName');
        if (!empty($Field)) {
            $this->db->select($Field, FALSE);
        }
        $this->db->from('tbl_entity E, football_sports_teams T');
        if (!empty($Where['SeasonID']) || in_array($Params, 'LeagueName')) {
            $this->db->from('football_sports_leagues L');
            $this->db->where("T.LeagueID", "L.LeagueID", FALSE);
        }
        $this->db->where("T.TeamID", "E.EntityID", FALSE);
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("T.TeamName", $Where['Keyword']);
            $this->db->or_like("T.TeamNameShort", $Where['Keyword']);
            $this->db->group_end();
        }
        if (!empty($Where['TeamID'])) {
            $this->db->where("T.TeamID", $Where['TeamID']);
        }
        if (!empty($Where['SeasonID'])) {
            $this->db->where("L.SeasonID", $Where['SeasonID']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("T.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else{
            $this->db->order_by('T.TeamName', 'ASC');
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
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Records = array();
                foreach ($Query->result_array() as $key => $Record) {
                    $Records[] = $Record;
                    if (in_array('TeamStandings', $Params)) {
                        $Records[$key]['TeamStandings'] = (!empty($Record['TeamStandings'])) ? json_decode($Record['TeamStandings']) : new stdClass();
                    }
                }
                $Return['Data']['Records'] = $Records;
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('TeamStandings', $Params)) {
                    $Record['TeamStandings'] = (!empty($Record['TeamStandings'])) ? json_decode($Record['TeamStandings']) : new stdClass();
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description : To Add Player Details
     */
    function addPlayer($Input = array())
    {
        $this->db->trans_start();

        $PlayerGUID = get_guid();
        $PlayerID   = $this->Entity_model->addEntity($PlayerGUID, array("EntityTypeID" => 10, "StatusID" => 2));

        /* Insert Player */
        $InsertArray = array_filter(array(
            'PlayerID'    => $PlayerID,
            'PlayerGUID'  => $PlayerGUID,
            'PlayerSource'  => 'Manual',
            'PlayerIDLive'  => 'P'.$PlayerID,
            'PlayerName'    => $Input['PlayerName']
        ));
        $this->db->insert('football_sports_players', $InsertArray);
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return $PlayerID;
    }

    /*
      Description: To get all players
     */
    function getPlayers($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15){
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'PlayerID'          => 'P.PlayerID',
                'PlayerIDLive'      => 'P.PlayerIDLive',
                'PlayerName'        => 'P.PlayerName',
                'PlayerSource'      => 'P.PlayerSource',
                'LeagueID'          => 'TP.LeagueID',
                'TeamID'            => 'TP.TeamID',
                'MatchID'           => 'TP.MatchID',
                'PlayerRole'        => 'TP.PlayerRole',
                'TeamGUID'          => 'T.TeamGUID',
                'PlayerPic'         => 'IF(P.PlayerPic IS NULL,CONCAT("' . BASE_URL . '","uploads/PlayerPic/","player.png"), CONCAT("' . BASE_URL . '","uploads/PlayerPic/",P.PlayerPic)) PlayerPic',
                'PlayerNationality' => 'P.PlayerNationality'
            );
            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('P.PlayerGUID');
        if (!empty($Field))
            $this->db->select($Field, FALSE);
        $this->db->from('football_sports_players P');
        if (array_keys_exist($Params, array('LeagueID','TeamID','MatchID','PlayerRole'))) {
            $this->db->from('football_sports_team_players TP');
            $this->db->where("P.PlayerID", "TP.PlayerID", FALSE);
        }
        if (array_keys_exist($Params, array('TeamGUID'))) {
            $this->db->from('football_sports_teams T');
            $this->db->where("T.TeamID", "TP.TeamID", FALSE);
        }
        if (!empty($Where['Keyword'])) {
            $this->db->group_start();
            $this->db->like("P.PlayerName", $Where['Keyword']);
            $this->db->or_like("P.PlayerNationality", $Where['Keyword']);
            $this->db->group_end();
        }
        if (!empty($Where['PlayerID'])) {
            $this->db->where("P.PlayerID", $Where['PlayerID']);
        }
        if (!empty($Where['PlayerSource'])) {
            $this->db->where("P.PlayerSource", $Where['PlayerSource']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("TP.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['MatchID'])) {
            $this->db->where("TP.MatchID", $Where['MatchID']);
        }
        if (!empty($Where['TeamID'])) {
            $this->db->where("TP.TeamID", $Where['TeamID']);
        }
        if (!empty($Where['PlayerRole'])) {
            $this->db->where("TP.PlayerRole", $Where['PlayerRole']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else {
            $this->db->order_by('P.PlayerName', 'ASC');
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
      Description : To Update Player Details
     */
    function updatePlayerDetails($PlayerID, $Input = array())
    {
        $UpdateArray = array_filter(array(
            'PlayerName' => @$Input['PlayerName'],
            'PlayerPic'  => @$Input['PlayerPic']
        ));
        if (!empty($UpdateArray)) {
            $this->db->where('PlayerID', $PlayerID);
            $this->db->limit(1);
            $this->db->update('football_sports_players', $UpdateArray);
        }
        return TRUE;
    }

    /*
      Description: Delete Player to system.
    */
    function deletePlayer($PlayerID) {
        $this->db->where('EntityID', $PlayerID);
        $this->db->limit(1);
        $this->db->delete('tbl_entity');
    }

    /*
      Description: To Predict a Match Or Fixture (Save & Lock)
     */
    function matchPrediction($Input = array(), $SessionUserID){
        $this->db->trans_start();
        $RemainingPredictions = True;

        /*  0anage Prediction Data */
        $PredictionData = array(
                        "TeamScoreLocalFT"   => $Input['TeamScoreLocalFT'],
                        "TeamScoreVisitorFT" => $Input['TeamScoreVisitorFT'],
                        "TeamScoreLocalHT"   => $Input['TeamScoreLocalHT'],
                        "TeamScoreVisitorHT" => $Input['TeamScoreVisitorHT'],
                        "PredictionStatus"   => $Input['PredictionStatus'],
                        "IsDoubleUps"        => $Input['IsDoubleUps']
                    );

        if($Input['IsExist'] == 'No'){

            /* Insert User Prediction Data */
            $PredictionData['MatchID']   = $Input['MatchID'];
            $PredictionData['UserID']    = $SessionUserID;
            $PredictionData['GameEntryID'] = $Input['GameEntryID'];
            $PredictionData['PredictionDate'] = date('Y-m-d H:i:s');
            $PredictionData['SavedDateTime']  = date('Y-m-d H:i:s');
            if($Input['PredictionStatus'] == 'Lock'){
                $PredictionData['LockedDateTime'] = date('Y-m-d H:i:s');
            }
            $this->db->insert('football_sports_matches_prediction', $PredictionData);
        }else{

            /* Update User Prediction Data */
            if($Input['PredictionStatus'] == 'Lock'){
                $PredictionData['LockedDateTime'] = date('Y-m-d H:i:s');
            }else{
                $PredictionData['SavedDateTime']  = date('Y-m-d H:i:s');
            }
            $PredictionData['GameEntryID']  = $Input['GameEntryID'];
            $this->db->where(array('MatchPredictionID' => $Input['MatchPredictionID'],'MatchID' => $Input['MatchID'], 'UserID' => $SessionUserID));
            $this->db->limit(1);
            $this->db->update('football_sports_matches_prediction', $PredictionData);
        }

        /* Update Prediction Statics */
        if($Input['PredictionStatus'] == 'Lock'){

            $this->db->set('ConsumedPredictions', 'ConsumedPredictions+1', FALSE);
            if($Input['IsDoubleUps'] == 'Yes'){
                $this->db->set('ConsumeDoubleUps', 'ConsumeDoubleUps+1', FALSE);
            }
            $this->db->set('ModifiedDate',date('Y-m-d H:i:s'));
            $this->db->where('UserID', $SessionUserID);
            $this->db->where('WeekID', $Input['WeekID']);
            $this->db->where('GameEntryID', $Input['GameEntryID']);
            $this->db->limit(1);
            $this->db->update('tbl_users_game_entries');

            /* Add Notification */
            $EntreisData = $this->db->query('SELECT (AllowedPredictions - ConsumedPredictions) RemainingPredictions, (AllowedPurchaseDoubleUps - TotalPurchasedDoubleUps) RemainingDoubleUps FROM tbl_users_game_entries WHERE UserID = '. $SessionUserID.' AND WeekID = '.$Input['WeekID'].' AND GameEntryID = '.$Input['GameEntryID'].' LIMIT 1');
            $RemainingPredictions = $RemainingDoubleUps = 0;
            if($EntreisData->num_rows() > 0){
               $RemainingPredictions = $EntreisData->row()->RemainingPredictions;
               // $RemainingDoubleUps   = $EntreisData->row()->RemainingDoubleUps;
               $RemainingDoubleUps   = $EntreisData->row()->RemainingDoubleUps;
            }

            $RemainingPredictionsMsg = 'A Prediction between '.$this->Post['TeamNameLocal'].' Vs '.$this->Post['TeamNameVisitor'].' is successfully done. Week '.$Input['WeekCount'].' remaining prediction balance is '.$RemainingPredictions.' & remaining double ups balance is '.$RemainingDoubleUps;
            $this->Notification_model->addNotification('FootBallPrediction', 'FootBall Prediction', $SessionUserID, $SessionUserID, '', $RemainingPredictionsMsg);
            
            /* To Get Old Prediction Statics */
            $MatchData = $this->db->query('SELECT FullTimePredictionStatics,HalfTimePredictionStatics FROM football_sports_matches WHERE MatchID = '.$Input['MatchID'].' LIMIT 1')->row_array();

            /* Calculate Full Time Prediction Statics Count */
            $PredictionStaticsDataFT = array('TeamWinCountLocal' => 0,'TeamWinPercentLocal' => 0, 'DrawCount' => 0, 'DrawPercent' => 0, 'TeamWinCountVisitor' => 0, 'TeamWinPercentVisitor' => 0);
            $WhoIsWinner = (($Input['TeamScoreLocalFT'] != $Input['TeamScoreVisitorFT']) ? (($Input['TeamScoreLocalFT'] > $Input['TeamScoreVisitorFT']) ? 'Local' : 'Visitor') : 'Draw');
            if(!empty($MatchData['FullTimePredictionStatics'])){
                $PredictionStaticsDataFT = json_decode($MatchData['FullTimePredictionStatics'], TRUE);
            }
            switch ($WhoIsWinner) {
                case 'Local':
                    $PredictionStaticsDataFT['TeamWinCountLocal'] = $PredictionStaticsDataFT['TeamWinCountLocal'] + 1;
                    break;
                case 'Visitor':
                    $PredictionStaticsDataFT['TeamWinCountVisitor'] = $PredictionStaticsDataFT['TeamWinCountVisitor'] + 1;
                    break;
                case 'Draw':
                    $PredictionStaticsDataFT['DrawCount'] = $PredictionStaticsDataFT['DrawCount'] + 1;
                    break;
            }

            /* Calculate Prediction Statics Percent */
            $TotalPrediction = $PredictionStaticsDataFT['TeamWinCountLocal'] + $PredictionStaticsDataFT['TeamWinCountVisitor'] + $PredictionStaticsDataFT['DrawCount'];
            if($PredictionStaticsDataFT['TeamWinCountLocal'] > 0){
                $PredictionStaticsDataFT['TeamWinPercentLocal'] = round(($PredictionStaticsDataFT['TeamWinCountLocal'] * 100) / $TotalPrediction,2);
            }
            if($PredictionStaticsDataFT['TeamWinCountVisitor'] > 0){
                $PredictionStaticsDataFT['TeamWinPercentVisitor'] = round(($PredictionStaticsDataFT['TeamWinCountVisitor'] * 100) / $TotalPrediction,2);
            }
            if($PredictionStaticsDataFT['DrawCount'] > 0){
                $PredictionStaticsDataFT['DrawPercent'] = round(($PredictionStaticsDataFT['DrawCount'] * 100) / $TotalPrediction,2);
            }

            /* Calculate Half Time Prediction Statics Count */
            $PredictionStaticsDataHT = array('TeamWinCountLocal' => 0,'TeamWinPercentLocal' => 0, 'DrawCount' => 0, 'DrawPercent' => 0, 'TeamWinCountVisitor' => 0, 'TeamWinPercentVisitor' => 0);
            $WhoIsWinner = (($Input['TeamScoreLocalHT'] != $Input['TeamScoreVisitorHT']) ? (($Input['TeamScoreLocalHT'] > $Input['TeamScoreVisitorHT']) ? 'Local' : 'Visitor') : 'Draw');
            if(!empty($MatchData['HalfTimePredictionStatics'])){
                $PredictionStaticsDataHT = json_decode($MatchData['HalfTimePredictionStatics'], TRUE);
            }
            switch ($WhoIsWinner) {
                case 'Local':
                    $PredictionStaticsDataHT['TeamWinCountLocal'] = $PredictionStaticsDataHT['TeamWinCountLocal'] + 1;
                    break;
                case 'Visitor':
                    $PredictionStaticsDataHT['TeamWinCountVisitor'] = $PredictionStaticsDataHT['TeamWinCountVisitor'] + 1;
                    break;
                case 'Draw':
                    $PredictionStaticsDataHT['DrawCount'] = $PredictionStaticsDataHT['DrawCount'] + 1;
                    break;
            }

            /* Calculate Prediction Statics Percent */
            $TotalPrediction = $PredictionStaticsDataHT['TeamWinCountLocal'] + $PredictionStaticsDataHT['TeamWinCountVisitor'] + $PredictionStaticsDataHT['DrawCount'];
            if($PredictionStaticsDataHT['TeamWinCountLocal'] > 0){
                $PredictionStaticsDataHT['TeamWinPercentLocal'] = round(($PredictionStaticsDataHT['TeamWinCountLocal'] * 100) / $TotalPrediction,2);
            }
            if($PredictionStaticsDataHT['TeamWinCountVisitor'] > 0){
                $PredictionStaticsDataHT['TeamWinPercentVisitor'] = round(($PredictionStaticsDataHT['TeamWinCountVisitor'] * 100) / $TotalPrediction,2);
            }
            if($PredictionStaticsDataHT['DrawCount'] > 0){
                $PredictionStaticsDataHT['DrawPercent'] = round(($PredictionStaticsDataHT['DrawCount'] * 100) / $TotalPrediction,2);
            }

            /* Update Match Prediction Statics */
            $this->db->where('MatchID', $Input['MatchID']);
            $this->db->limit(1);
            $this->db->update('football_sports_matches', array('FullTimePredictionStatics' => json_encode($PredictionStaticsDataFT), 'HalfTimePredictionStatics' => json_encode($PredictionStaticsDataHT)));
        }
        
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        }
        return ($Input['PredictionStatus'] == 'Lock') ? $RemainingPredictionsMsg : TRUE;
    }

    /*
      Description: To get predictions data
    */
    function getPredictions($Field = '', $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) 
    {
        $Params = array();
        if (!empty($Field)) {
            $Params = array_map('trim', explode(',', $Field));
            $Field = '';
            $FieldArray = array(
                'MatchPredictionID'     => 'MP.MatchPredictionID',
                'MatchID'               => 'MP.MatchID',
                'UserID'                => 'MP.UserID',
                'WinningAmount'         => 'MP.WinningAmount',
                'IsWinning'             => 'MP.IsWinning',
                'TeamScoreLocalHT'      => 'MP.TeamScoreLocalHT',
                'TeamScoreVisitorHT'    => 'MP.TeamScoreVisitorHT',
                'TeamScoreLocalFT'      => 'MP.TeamScoreLocalFT',
                'TeamScoreVisitorFT'    => 'MP.TeamScoreVisitorFT',
                'PredictionStatus'      => 'MP.PredictionStatus',
                'IsDoubleUps'           => 'MP.IsDoubleUps',
                'WinningAmount'         => 'MP.WinningAmount',
                'IsWinning'             => 'MP.IsWinning',
                'WinningDateTime'       => 'DATE_FORMAT(CONVERT_TZ(MP.WinningDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") WinningDateTime',
                'PredictionDate'        => 'DATE_FORMAT(CONVERT_TZ(MP.PredictionDate,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") PredictionDate',
                'SavedDateTime'         => 'DATE_FORMAT(CONVERT_TZ(MP.SavedDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") SavedDateTime',
                'LockedDateTime'        => 'DATE_FORMAT(CONVERT_TZ(MP.LockedDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") LockedDateTime',
                'ExactScorePoints'      => 'MP.ExactScorePoints',
                'CorrectResultPoints'   => 'MP.CorrectResultPoints',
                'BothTeamScorePoints'   => 'MP.BothTeamScorePoints',
                'LongestOddsScorePoints'=> 'MP.LongestOddsScorePoints',
                'LeagueID'              => 'M.LeagueID',
                'WeekID'                => 'M.WeekID',
                'StatusID'              => 'E.StatusID',
                'MatchScoreDetails'     => 'M.MatchScoreDetails',
                'LeagueGUID'            => 'L.LeagueGUID',
                'LeagueName'            => 'L.LeagueName',
                'LeagueFlag'            => 'IF(L.LeagueFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/LeagueFlag/","league.png"), CONCAT("' . BASE_URL . '","uploads/LeagueFlag/",L.LeagueFlag)) LeagueFlag',
                'TeamGUIDLocal'         => 'TL.TeamGUID AS TeamGUIDLocal',
                'TeamGUIDVisitor'       => 'TV.TeamGUID AS TeamGUIDVisitor',
                'TeamNameLocal'         => 'TL.TeamName AS TeamNameLocal',
                'TeamNameVisitor'       => 'TV.TeamName AS TeamNameVisitor',
                'TeamNameShortLocal'    => 'TL.TeamNameShort AS TeamNameShortLocal',
                'TeamNameShortVisitor'  => 'TV.TeamNameShort AS TeamNameShortVisitor',
                'TeamColorLocal'        => 'TL.TeamColor AS TeamColorLocal',
                'TeamColorVisitor'      => 'TV.TeamColor AS TeamColorVisitor',
                'TeamStandingsLocal'    => 'TL.TeamStandings AS TeamStandingsLocal',
                'TeamStandingsVisitor'  => 'TV.TeamStandings AS TeamStandingsVisitor',
                'TeamFlagLocal'         => 'IF(TL.TeamFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/TeamFlag/","team.png"), CONCAT("' . BASE_URL . '","uploads/TeamFlag/",TL.TeamFlag)) TeamFlagLocal',
                'TeamFlagVisitor'       => 'IF(TV.TeamFlag IS NULL,CONCAT("' . BASE_URL . '","uploads/TeamFlag/","team.png"), CONCAT("' . BASE_URL . '","uploads/TeamFlag/",TV.TeamFlag)) TeamFlagVisitor',
                'MatchStartDateTime'    => 'DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") MatchStartDateTime',
                'CurrentDateTime'       => 'DATE_FORMAT(CONVERT_TZ(Now(),"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . ' ") CurrentDateTime',
                'MatchDate'             => 'DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "%Y-%m-%d") MatchDate',
                'MatchTime'             => 'DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "%H:%i:%s") MatchTime',
                'Status'                => 'CASE E.StatusID
                                                when "1" then "Pending"
                                                when "2" then "Running"
                                                when "3" then "Cancelled"
                                                when "5" then "Completed"  
                                                when "8" then "Abandoned"  
                                                when "9" then "No Result" 
                                            END as Status'
            );

            if ($Params) {
                foreach ($Params as $Param) {
                    $Field .= (!empty($FieldArray[$Param]) ? ',' . $FieldArray[$Param] : '');
                }
            }
        }
        $this->db->select('M.MatchGUID');
        if (!empty($Field)) {
            $this->db->select($Field, FALSE);
        }
        $this->db->from('football_sports_matches_prediction MP, football_sports_matches M');
        $this->db->where("MP.MatchID", "M.MatchID", FALSE);
        if (!empty($Where['StatusID']) || array_keys_exist($Params, array('StatusID','Status'))) {
            $this->db->from('tbl_entity E');
            $this->db->where("E.EntityID", "M.MatchID", FALSE);
        }
        if (!empty($Where['Keyword']) || array_keys_exist($Params, array('LeagueGUID','LeagueName','LeagueFlag'))) {
            $this->db->from('football_sports_leagues L');
            $this->db->where("M.LeagueID", "L.LeagueID", FALSE);
        }
        if (!empty($Where['Keyword']) || array_keys_exist($Params, array('TeamGUIDLocal','TeamGUIDVisitor','TeamNameLocal','TeamNameVisitor','TeamNameShortLocal','TeamNameShortVisitor','TeamFlagLocal','TeamFlagVisitor','TeamColorLocal','TeamColorVisitor','TeamStandingsLocal','TeamStandingsVisitor'))) {
            $this->db->from('football_sports_teams TL, football_sports_teams TV');
            $this->db->where("M.TeamIDLocal", "TL.TeamID", FALSE);
            $this->db->where("M.TeamIDVisitor", "TV.TeamID", FALSE);
        }
        if (!empty($Where['Keyword'])) {
            $Where['Keyword'] = trim($Where['Keyword']);
            $this->db->group_start();
            $this->db->like("L.LeagueName", $Where['Keyword']);
            $this->db->or_like("TL.TeamName", $Where['Keyword']);
            $this->db->or_like("TV.TeamName", $Where['Keyword']);
            $this->db->or_like("TL.TeamNameShort", $Where['Keyword']);
            $this->db->or_like("TV.TeamNameShort", $Where['Keyword']);
            $this->db->group_end();
        }
        if (!empty($Where['MatchID'])) {
            $this->db->where("MP.MatchID", $Where['MatchID']);
        }
        if (!empty($Where['UserID'])) {
            $this->db->where("MP.UserID", $Where['UserID']);
        }
        if (!empty($Where['SessionUserID'])) {
            $this->db->where("MP.UserID", $Where['SessionUserID']);
        }
        if (!empty($Where['MatchPredictionID'])) {
            $this->db->where("MP.MatchPredictionID", $Where['MatchPredictionID']);
        }
        if (!empty($Where['PredictionStatus'])) {
            $this->db->where("MP.PredictionStatus", $Where['PredictionStatus']);
        }
        if (!empty($Where['IsDoubleUps'])) {
            $this->db->where("MP.IsDoubleUps", $Where['IsDoubleUps']);
        }
        if (!empty($Where['IsWinning'])) {
            $this->db->where("MP.IsWinning", $Where['IsWinning']);
        }
        if (!empty($Where['LeagueID'])) {
            $this->db->where("M.LeagueID", $Where['LeagueID']);
        }
        if (!empty($Where['WeekID'])) {
            $this->db->where("M.WeekID", $Where['WeekID']);
        }
        if (!empty($Where['GameEntryID'])) {
            $this->db->where("MP.GameEntryID", $Where['GameEntryID']);
        }
        if (!empty($Where['StatusID'])) {
            $this->db->where("E.StatusID", $Where['StatusID']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        }else{
            $this->db->order_by('MP.PredictionDate', 'DESC');
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
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Records = array();
                foreach ($Query->result_array() as $key => $Record) {
                    $Records[] = $Record;
                    if (in_array('TeamStandingsLocal', $Params)) {
                        $Records[$key]['TeamStandingsLocal'] = (!empty($Record['TeamStandingsLocal'])) ? json_decode($Record['TeamStandingsLocal']) : new stdClass();
                    }
                    if (in_array('TeamStandingsVisitor', $Params)) {
                        $Records[$key]['TeamStandingsVisitor'] = (!empty($Record['TeamStandingsVisitor'])) ? json_decode($Record['TeamStandingsVisitor']) : new stdClass();
                    }
                    if (in_array('MatchScoreDetails', $Params)) {
                        $Records[$key]['MatchScoreDetails'] = (!empty($Record['MatchScoreDetails'])) ? json_decode($Record['MatchScoreDetails']) : new stdClass();
                    }
                }
                $Return['Data']['Records'] = $Records;
                return $Return;
            } else {
                $Record = $Query->row_array();
                if (in_array('TeamStandingsLocal', $Params)) {
                    $Record['TeamStandingsLocal'] = (!empty($Record['TeamStandingsLocal'])) ? json_decode($Record['TeamStandingsLocal']) : new stdClass();
                }
                if (in_array('TeamStandingsVisitor', $Params)) {
                    $Record['TeamStandingsVisitor'] = (!empty($Record['TeamStandingsVisitor'])) ? json_decode($Record['TeamStandingsVisitor']) : new stdClass();
                }
                if (in_array('MatchScoreDetails', $Params)) {
                    $Record['MatchScoreDetails'] = (!empty($Record['MatchScoreDetails'])) ? json_decode($Record['MatchScoreDetails']) : new stdClass();
                }
                return $Record;
            }
        }
        return FALSE;
    }

    /*
      Description: To Calculate User Prediction Points (Match Wise)
     */
    function calculatePoints($MatchID,$MatchScoreDetails,$LongestOddsLabel = NULL){

        /* Calculate User Score Points */
        $Query = $this->db->query('SELECT MatchPredictionID,UserID,TeamScoreLocalHT,TeamScoreVisitorHT,TeamScoreLocalFT,TeamScoreVisitorFT,IsDoubleUps FROM football_sports_matches_prediction WHERE MatchID = '.$MatchID.' AND PredictionStatus = "Lock" ORDER BY PredictionDate ASC');
        if($Query->num_rows() > 0){

            /* To Get Defined Scoring Points */
            $ScoringPoints = array_column($this->db->query('SELECT ConfigTypeGUID,ConfigTypeValue FROM set_site_config WHERE ConfigTypeGUID IN ("FullTimeExactScore","HalfTimeExactScore","FullTimeCorrectResult","HalfTimeCorrectResult","BothTeamToScore")')->result_array(), 'ConfigTypeValue', 'ConfigTypeGUID');
            foreach($Query->result_array() as $Value){

                /* Calculate Correct Result Points */
                $CorrectResultPointsFT = $CorrectResultPointsHT = $LongestOddsScorePoints = 0;
                $DoubleUpMultiplier  = ($Value['IsDoubleUps'] == 'Yes') ? 2 : 1;
                $MatchResult = '';
                list($TeamScoreLocalFT,$TeamScoreVisitorFT) = explode("-", $MatchScoreDetails['FullTimeScore']);
                list($TeamScoreLocalHT,$TeamScoreVisitorHT) = explode("-", $MatchScoreDetails['HalfTimeScore']);

                /* Full Time Calculation */
                if($TeamScoreLocalFT == $TeamScoreVisitorFT){ // Draw
                    $CorrectResultPointsFT = ($Value['TeamScoreLocalFT'] == $Value['TeamScoreVisitorFT']) ? $ScoringPoints['FullTimeCorrectResult'] : 0;
                    $MatchResult = 'Draw';
                }else if($TeamScoreLocalFT > $TeamScoreVisitorFT){ // Team Local Win
                    $CorrectResultPointsFT = ($Value['TeamScoreLocalFT'] > $Value['TeamScoreVisitorFT']) ? $ScoringPoints['FullTimeCorrectResult']  : 0;
                    $MatchResult = 'Home';
                }else if($TeamScoreLocalFT < $TeamScoreVisitorFT){ // Team Visitor Win
                    $CorrectResultPointsFT = ($Value['TeamScoreLocalFT'] < $Value['TeamScoreVisitorFT']) ? $ScoringPoints['FullTimeCorrectResult']  : 0;
                    $MatchResult = 'Away';
                }

                /* Half Time Calculation */
                if($TeamScoreLocalHT == $TeamScoreVisitorHT){ // Draw
                    $CorrectResultPointsHT = ($Value['TeamScoreLocalHT'] == $Value['TeamScoreVisitorHT']) ? $ScoringPoints['HalfTimeCorrectResult'] : 0;
                }else if($TeamScoreLocalHT > $TeamScoreVisitorHT){ // Team Local Win
                    $CorrectResultPointsHT = ($Value['TeamScoreLocalHT'] > $Value['TeamScoreVisitorHT']) ? $ScoringPoints['HalfTimeCorrectResult']  : 0;
                }else if($TeamScoreLocalHT < $TeamScoreVisitorHT){ // Team Visitor Win
                    $CorrectResultPointsHT = ($Value['TeamScoreLocalHT'] < $Value['TeamScoreVisitorHT']) ? $ScoringPoints['HalfTimeCorrectResult']  : 0;
                }

                /* Longest Odds Points Calculation (Define Equals To Correct Result Points Full Time) */
                if(!empty($LongestOddsLabel) && $LongestOddsLabel == $MatchResult){
                    $LongestOddsScorePoints = $CorrectResultPointsFT;
                }
                /* Exact score calculation for full time and half time */
                $ExactScorePointsFT = (($Value['TeamScoreLocalFT'] == $MatchScoreDetails['LocalTeamScore']) && ($Value['TeamScoreVisitorFT'] == $MatchScoreDetails['VisitorTeamScore'])) ? $ScoringPoints['FullTimeExactScore'] : 0;
                $ExactScorePointsHT = (($Value['TeamScoreLocalHT'] == $TeamScoreLocalHT) && ($Value['TeamScoreVisitorHT'] == $TeamScoreVisitorHT)) ? $ScoringPoints['HalfTimeExactScore'] : 0;
                
                /* Update User Scoring Points */
                $UpdateData = array();
                $UpdateData['CorrectResultPoints'] = ($CorrectResultPointsFT + $CorrectResultPointsHT) * $DoubleUpMultiplier;
                $UpdateData['ExactScorePoints']    = ($ExactScorePointsFT + $ExactScorePointsHT) * $DoubleUpMultiplier;
                $UpdateData['BothTeamScorePoints'] = ($Value['TeamScoreLocalFT'] > 0 && $MatchScoreDetails['LocalTeamScore'] > 0 && $Value['TeamScoreVisitorFT'] > 0 && $MatchScoreDetails['VisitorTeamScore'] > 0) ? ($DoubleUpMultiplier * $ScoringPoints['BothTeamToScore']) : 0;
                $UpdateData['LongestOddsScorePoints'] = ($LongestOddsScorePoints > 0) ? ($DoubleUpMultiplier * $LongestOddsScorePoints) : 0;
                if(array_sum($UpdateData) <= 0){
                    continue;
                }

                $this->db->where(array('MatchPredictionID' => $Value['MatchPredictionID'],'MatchID' => $MatchID, 'UserID' => $Value['UserID']));
                $this->db->limit(1);
                $this->db->update('football_sports_matches_prediction',$UpdateData);
            }

            /* Update Leaderboards */
            $this->updateLeaderboards($MatchID);
        }
    }


    /*
      Description: To Update All Leaderboards (Match Wise)
     */
    function updateLeaderboards($MatchID){

        /* To Get Match Predictions */
        // $Query = $this->db->query('SELECT GE.EntryNo,MP.MatchPredictionID,MP.UserID,MP.ExactScorePoints,MP.CorrectResultPoints, MP.BothTeamScorePoints, MP.LongestOddsScorePoints, (MP.ExactScorePoints + MP.CorrectResultPoints + MP.BothTeamScorePoints + MP.LongestOddsScorePoints) TotalPoints FROM `football_sports_matches_prediction` MP, tbl_users_game_entries GE WHERE MP.GameEntryID = GE.GameEntryID AND MP.MatchID = '.$MatchID.' AND MP.PredictionStatus = "Lock"');
        $Query = $this->db->query('SELECT GE.EntryNo,MP.MatchPredictionID,MP.UserID,SUM(MP.ExactScorePoints) as ExactScorePoints,SUM(MP.CorrectResultPoints) as CorrectResultPoints, SUM(MP.BothTeamScorePoints) as BothTeamScorePoints, SUM(MP.LongestOddsScorePoints) as LongestOddsScorePoints, (SUM(MP.ExactScorePoints) + SUM(MP.CorrectResultPoints) + SUM(MP.BothTeamScorePoints) + SUM(MP.LongestOddsScorePoints)) TotalPoints FROM `football_sports_matches_prediction` MP, tbl_users_game_entries GE WHERE MP.GameEntryID = GE.GameEntryID AND MP.MatchID ='.$MatchID.' AND MP.PredictionStatus = "Lock" GROUP BY GE.EntryNo');
        if($Query->num_rows() == 0){
            return;
        }

        /* To Get Match Details */
        $Match = $this->db->query('SELECT M.MatchID,M.LeagueID,M.WeekID,L.SeasonID FROM football_sports_matches M, football_sports_leagues L WHERE M.LeagueID = L.LeagueID AND M.MatchID = '.$MatchID.' LIMIT 1')->row_array();

        /* Manage Leaderboards */
        foreach($Query->result_array() as $Value){

            /* Update Match Leaderboard */
            // $this->fantasydb->tbl_leaderboard_match->updateOne(
            //         ['_id'    => $Match['MatchID'].$Value['UserID']],
            //         ['$set'   => array('UserID' => (int) $Value['UserID'], 'MatchID' => (int) $Match['MatchID'], 'ExactScorePoints' => (int) $Value['ExactScorePoints'], 'CorrectResultPoints' => (int) $Value['CorrectResultPoints'], 'BothTeamScorePoints' => (int) $Value['BothTeamScorePoints'], 'LongestOddsScorePoints' => (int) $Value['LongestOddsScorePoints'], 'TotalPoints' => (int) $Value['TotalPoints'])],
            //         ['upsert' => true]
            //     );

            /* Update League Week Leaderboard */
            $this->fantasydb->tbl_leaderboard_league_week->updateOne(
                    ['_id'    => $Value['UserID'].$Match['WeekID'].$Value['EntryNo']],
                    ['$set'   => array('MatchPredictionID' => (int) $Value['MatchPredictionID'],'UserID' => (int) $Value['UserID'],'EntryNo' => (int) $Value['EntryNo'], 'LeagueID' => (int) $Match['LeagueID'], 'WeekID' => (int) $Match['WeekID']), '$inc' => ['ExactScorePoints' => (int) $Value['ExactScorePoints'], 'CorrectResultPoints' => (int) $Value['CorrectResultPoints'], 'BothTeamScorePoints' => (int) $Value['BothTeamScorePoints'], 'LongestOddsScorePoints' => (int) $Value['LongestOddsScorePoints'], 'TotalPoints' => (int) $Value['TotalPoints']]],
                    ['upsert' => true]
                );

            /* Update League Season Leaderboard */
            // $this->fantasydb->tbl_leaderboard_league_season->updateOne(
            //         ['_id'    => $Match['LeagueID'].$Match['SeasonID'].$Value['UserID']],
            //         ['$set'   => array('UserID' => (int) $Value['UserID'], 'LeagueID' => (int) $Match['LeagueID'], 'SeasonID' => (int) $Match['SeasonID']), '$inc' => ['ExactScorePoints' => (int) $Value['ExactScorePoints'], 'CorrectResultPoints' => (int) $Value['CorrectResultPoints'], 'BothTeamScorePoints' => (int) $Value['BothTeamScorePoints'], 'LongestOddsScorePoints' => (int) $Value['LongestOddsScorePoints'], 'TotalPoints' => (int) $Value['TotalPoints']]],
            //         ['upsert' => true]
            //     );
        }
    }

    /*
      Description: To get match wise leaderboard (MongoDB)
     */
    function getLeaderBoardMatchWise($Where = array(), $PageNo = 1, $PageSize = 25)
    {
        /* Get Leaderboard Data */
        $LeaderBoardData  = iterator_to_array($this->fantasydb->tbl_leaderboard_match->aggregate([
                                        [
                                            '$match' => ['MatchID' => (int) $Where['MatchID']]
                                        ],[
                                            '$lookup' => ['from' => 'tbl_users','localField' => 'UserID','foreignField' => '_id','as' => 'users']
                                        ],[
                                            '$unwind' => '$users'
                                        ],[
                                            '$project' => ['_id' => 0, 'UserGUID' => '$users.UserGUID', 'Email' => '$users.Email', 'FirstName' => '$users.FirstName', 'LastName' => '$users.LastName', 'Username' => '$users.Username', 'ProfilePic' => '$users.ProfilePic', 'ExactScorePoints' => 1, 'CorrectResultPoints' => 1, 'BothTeamScorePoints' => 1, 'LongestOddsScorePoints' => 1, 'TotalPoints' => 1]
                                        ],[
                                            '$skip' => paginationOffset($PageNo, $PageSize)
                                        ],[
                                            '$limit' => $PageSize
                                        ],[
                                            '$sort' => ['TotalPoints' => -1]
                                        ]
                                    ]));
        if (count($LeaderBoardData) > 0) {
            $Return['Data']['TotalRecords'] = $this->fantasydb->tbl_leaderboard_match->count(['MatchID' => (int) $Where['MatchID']]);
            $Return['Data']['Records'] = $LeaderBoardData;
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: To get league week wise leaderboard (MongoDB)
     */
    function getLeaderBoardLeagueWeekWise($Where = array(), $PageNo = 1, $PageSize = 25)
    {
        /* Get Leaderboard Data */
        $LeaderBoardData  = iterator_to_array($this->fantasydb->tbl_leaderboard_league_week->aggregate([
                                        [
                                            '$match' => ['WeekID' => (int) $Where['WeekID']]
                                        ],[
                                            '$lookup' => ['from' => 'tbl_users','localField' => 'UserID','foreignField' => '_id','as' => 'users']
                                        ],[
                                            '$unwind' => '$users'
                                        ],[
                                            '$project' => ['_id' => 0, 'UserGUID' => '$users.UserGUID', 'Email' => '$users.Email', 'FirstName' => '$users.FirstName', 'LastName' => '$users.LastName', 'Username' => '$users.Username', 'ProfilePic' => '$users.ProfilePic', 'ExactScorePoints' => 1, 'CorrectResultPoints' => 1, 'BothTeamScorePoints' => 1, 'LongestOddsScorePoints' => 1, 'TotalPoints' => 1, 'EntryNo' => 1, 'MatchPredictionID'=> 1]
                                        ],[
                                            '$skip' => paginationOffset($PageNo, $PageSize)
                                        ],[
                                            '$limit' => $PageSize
                                        ],[
                                            '$sort' => ['TotalPoints' => -1]
                                        ]
                                    ]));
        if (count($LeaderBoardData) > 0) {
            $Return['Data']['TotalRecords'] = $this->fantasydb->tbl_leaderboard_league_week->count(['LeagueID' => (int) $Where['LeagueID'], 'WeekID' => (int) $Where['WeekID']]);
            $Return['Data']['Records'] = $LeaderBoardData;
            return $Return;
        }
        return FALSE;
    }

    /*
      Description: To get league season wise leaderboard (MongoDB)
     */
    function getLeaderBoardLeagueSeasonWise($Where = array(), $PageNo = 1, $PageSize = 25)
    {
        /* Get Leaderboard Data */
        $LeaderBoardData  = iterator_to_array($this->fantasydb->tbl_leaderboard_league_season->aggregate([
                                        [
                                            '$match' => ['LeagueID' => (int) $Where['LeagueID'], 'SeasonID' => (int) $Where['SeasonID']]
                                        ],[
                                            '$lookup' => ['from' => 'tbl_users','localField' => 'UserID','foreignField' => '_id','as' => 'users']
                                        ],[
                                            '$unwind' => '$users'
                                        ],[
                                            '$project' => ['_id' => 0, 'UserGUID' => '$users.UserGUID', 'Email' => '$users.Email', 'FirstName' => '$users.FirstName', 'LastName' => '$users.LastName', 'Username' => '$users.Username', 'ProfilePic' => '$users.ProfilePic', 'ExactScorePoints' => 1, 'CorrectResultPoints' => 1, 'BothTeamScorePoints' => 1, 'LongestOddsScorePoints' => 1, 'TotalPoints' => 1]
                                        ],[
                                            '$skip' => paginationOffset($PageNo, $PageSize)
                                        ],[
                                            '$limit' => $PageSize
                                        ],[
                                            '$sort' => ['TotalPoints' => -1]
                                        ]
                                    ]));
        if (count($LeaderBoardData) > 0) {
            $Return['Data']['TotalRecords'] = $this->fantasydb->tbl_leaderboard_league_season->count(['LeagueID' => (int) $Where['LeagueID'], 'SeasonID' => (int) $Where['SeasonID']]);
            $Return['Data']['Records'] = $LeaderBoardData;
            return $Return;
        }
        return FALSE;
    }

}


