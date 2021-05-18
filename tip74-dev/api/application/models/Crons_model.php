<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crons_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
      Description: To Excecute curl request
     */

    function ExecuteCurl($Url, $Params = '') {
        $Curl = curl_init($Url);
        if (!empty($Params)) {
            curl_setopt($Curl, CURLOPT_POSTFIELDS, $Params);
        }
        curl_setopt($Curl, CURLOPT_HEADER, 0);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, TRUE);
        $Response = curl_exec($Curl);
        curl_close($Curl);
        return $Response;
    }

    /*
      Description: To fetch sports api data
     */
    function callSportsAPI($ApiUrl) {
        $Response = json_decode($this->ExecuteCurl($ApiUrl . SPORTMONKS_TOKEN), TRUE);
        if (!empty($Response['error']['code']) && $Response['error']['code'] == 401) {
            $Response = json_decode($this->ExecuteCurl($ApiUrl . SPORTMONKS_TOKEN), TRUE);
        }
        return $Response;
    }

    /*
      Description: To get football seasons data
    */
    function getSeasonsLive_Football($CronID) {

    	/* Get Seasons Live Data */
    	$Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'seasons?per_page=200&api_token=');
    	if(empty($Response['data'])){
    		$this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
    	}

    	/* To Get All Seasons Data */
        $SeasonsIds  = array();
        $SeasonsData = $this->Football_model->getSeasons('SeasonName,SeasonID', array(), true, 0);
        if ($SeasonsData) {
            $SeasonsIds = array_column($SeasonsData['Data']['Records'], 'SeasonID', 'SeasonName');
        }

    	/* Manage Data */
    	foreach(array_values(array_unique(array_column($Response['data'], 'name'))) as $Season){

    		$SeasonName = str_replace("/", "-", $Season);
    		if(explode("-", $SeasonName)[1] < 2019){ // Insert from 2019
    			continue;
    		}

    		$SeasonLiveData = array();
    		foreach($Response['data'] as $Value){	

    			if($Value['name'] != $Season){
    				continue;
    			}

    			$SeasonLiveData[$SeasonName] = array(
	    								'SeasonIDsLive'   => (empty($SeasonLiveData[$SeasonName]['SeasonIDsLive'])) ? $Value['id'] : ($SeasonLiveData[$SeasonName]['SeasonIDsLive'].",".$Value['id']),
	    								'SeasonName'      => $SeasonName,
	    								'IsCurrentSeason' => ($Value['is_current_season']) ? 'Yes' : 'No'  ,
	    							);
	    	}

            /* Check If already exist into database */
            if (!isset($SeasonsIds[$SeasonName])) {
                $this->db->insert('football_sports_seasons',array_values($SeasonLiveData)[0]);
            }else{
                $this->db->where('SeasonID', $SeasonsIds[$SeasonName]);
                $this->db->limit(1);
                $this->db->update('football_sports_seasons',array_values($SeasonLiveData)[0]);
            }
    	}
    }

    /*
      Description: To get football season custom weeks (Friday to Thursday)
    */
    function getSeasonWeeksLive_Football($CronID) {

        /* Get Current Season Data */
        $LeaguesData = $this->Football_model->getLeagues('SeasonID,LeagueID', array('Filter' => 'CurrentSeasonLeagues', 'LeagueSource' => 'API'), true, 0);
        if(empty($LeaguesData)){
            $this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
        }

        $SeasonID  = $LeaguesData['Data']['Records'][0]['SeasonID'];
        $LeagueIDs = array_column($LeaguesData['Data']['Records'], 'LeagueID');

        /* Get League Round Start Date */
        $RoundStartDate = $this->db->query('SELECT RoundStartDate FROM `football_sports_leagues_rounds` WHERE LeagueID IN ('.implode(",", $LeagueIDs).') ORDER BY`RoundStartDate` ASC LIMIT 1')->row()->RoundStartDate;

        /* Get League Round End Date */
        $RoundEndDate = $this->db->query('SELECT RoundEndDate FROM `football_sports_leagues_rounds` WHERE LeagueID IN ('.implode(",", $LeagueIDs).') ORDER BY`RoundStartDate` DESC LIMIT 1')->row()->RoundEndDate;

        if(date('l',strtotime($RoundStartDate)) != 'Friday'){
            $RoundStartDate = date('Y-m-d', strtotime('previous friday', strtotime($RoundStartDate)));
        }
        if(date('l',strtotime($RoundEndDate)) != 'Thursday'){
            $RoundEndDate = date('Y-m-d', strtotime('next thursday', strtotime($RoundEndDate)));
        }

        /* To get all dates */
        $AllDates = get_date_range($RoundStartDate,$RoundEndDate);
        $IsFriday = $IsThursday = FALSE;
        $CurrentDate = date('Y-m-d');
        $WeekCount = 1;
        foreach($AllDates as $Date){
            if(!$IsThursday && date('l',strtotime($Date)) == 'Thursday'){
                $ThursdayDate = $Date;
                $IsThursday = TRUE;
            }
            if(!$IsFriday && date('l',strtotime($Date)) == 'Friday'){
                $FridayDate = $Date;
                $IsFriday = TRUE;
            }
            if($IsThursday && $IsFriday){
                $IsFriday = $IsThursday = FALSE;

                /* To check if week is already exist */
                $Query = $this->db->query("SELECT WeekID FROM football_sports_season_weeks WHERE WeekStartDate = '".$FridayDate."' AND WeekEndDate = '".$ThursdayDate."' LIMIT 1");

                /* Week Status */
                $WeekStatus = 1;
                if((strtotime($FridayDate) <= strtotime($CurrentDate)) && (strtotime($ThursdayDate) >= strtotime($CurrentDate))){
                    $WeekStatus = 2;
                }else if((strtotime($FridayDate) < strtotime($CurrentDate)) && (strtotime($ThursdayDate) < strtotime($CurrentDate))){
                    $WeekStatus = 5;
                }

                if($Query->num_rows() == 0){

                    /* Add weeks to entity table and get EntityID. */
                    $WeekGUID = get_guid();
                    $WeekID   = $this->Entity_model->addEntity($WeekGUID, array("EntityTypeID" => 16, "StatusID" => $WeekStatus));
                    $InsertData[] = array(
                                        'WeekID'        => $WeekID,
                                        'WeekGUID'      => $WeekGUID,
                                        'SeasonID'      => $SeasonID,
                                        'WeekCount'     => $WeekCount,
                                        'WeekStartDate' => $FridayDate,
                                        'WeekEndDate'   => $ThursdayDate
                                    );
                }else{

                    $UpdateStatusData[] = array(
                                        'EntityID'  => $Query->row()->WeekID,
                                        'StatusID'  => $WeekStatus
                                    );
                }
                $WeekCount++;
            }
        }

        /* Insert Data */
        if(!empty($InsertData)){
            $this->db->insert_batch('football_sports_season_weeks',$InsertData);
        }

        /* Update Status Data */
        if(!empty($UpdateStatusData)){
            $this->db->update_batch('tbl_entity',$UpdateStatusData,'EntityID');
        }
    }   

    /*
      Description: To get football leagues data
    */
    function getLeaguesLive_Football($CronID) {

    	/* Get Leagues Live Data */
    	$Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'leagues?per_page=20&api_token=');
    	if(empty($Response['data'])){
    		$this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
    	}

    	/* Manage Data */
    	foreach($Response['data'] as $Value){

           /* Get Season ID */
           $SeasonID = $this->db->query("SELECT SeasonID FROM football_sports_seasons WHERE FIND_IN_SET(".$Value['current_season_id'].", SeasonIDsLive) LIMIT 1")->row()->SeasonID;

           /* To check if league is already exist with particular season */
           $Query = $this->db->query("SELECT LeagueID FROM football_sports_leagues WHERE LeagueIDLive = ".$Value['id']. " AND SeasonID = ".$SeasonID. " LIMIT 1");
           if($Query->num_rows() == 0){

                /* Add league to entity table and get EntityID. */
                $LeagueGUID = get_guid();
                $LeagueID   = $this->Entity_model->addEntity($LeagueGUID, array("EntityTypeID" => 7, "StatusID" => 2));
                $InsertData = array(
                                    'LeagueID'     => $LeagueID,
                                    'LeagueGUID'   => $LeagueGUID,
                                    'LeagueIDLive' => $Value['id'],
                                    'SeasonID'     => $SeasonID,
                                    'SeasonIDLive' => $Value['current_season_id'],
                                    'LeagueName'   => $Value['name'],
                                    'LeagueFlag'   => (!empty($Value['logo_path'])) ? downloadImagesFromLiveServer($Value['logo_path'],'LeagueFlag') : ''
                                );
                $this->db->insert('football_sports_leagues', array_filter($InsertData));
           }else if(!$Value['active']){

                /* Update league status. */
                $this->db->where('EntityID', $Query->row()->LeagueID);
                $this->db->limit(1);
                $this->db->update('tbl_entity', array('StatusID' => 6));
            }
    	}
    }

    /*
      Description: To get football venues data
    */
    function getVenuesLive_Football($CronID) {

    	/* Get Seasons Live Data */
    	$SeasonsData = $this->Football_model->getSeasons('SeasonIDsLive', array('IsCurrentSeason' => 'Yes'), true, 0);
    	if(empty($SeasonsData)){
    		$this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
    	}

    	/* To Get All Venues Data */
        $VenuesIds  = $VenuesLiveIds = array();
        $VenuesData = $this->Football_model->getVenues('VenueIDLive,VenueID', array('VenueSource' => 'API'), true, 0);
        if ($VenuesData) {
            $VenuesIds = array_column($VenuesData['Data']['Records'], 'VenueID', 'VenueIDLive');
            $VenuesLiveIds = array_column($VenuesData['Data']['Records'], 'VenueIDLive');
        }

    	foreach($SeasonsData['Data']['Records'] as $Season){
            
            foreach(explode(",", $Season['SeasonIDsLive']) as $SeasonIDLive){

    		/* Get Venues By Season Live ID */
    		$Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'venues/season/'.$SeasonIDLive.'?per_page=150&api_token=');
    		if(empty($Response['data'])){
    			continue;
    		}

    		$InsertData = $UpdateData = array();
    		foreach($Response['data'] as $Venue){

    			/* Check If already exist into database */
	    		if (!isset($VenuesIds[$Venue['id']]) && !in_array($Venue['id'], $VenuesLiveIds)) {
	    			$VenuesLiveIds[] = $Venue['id'];
	    			$InsertData[] = array(
	    								'VenueIDLive'    => $Venue['id'],
	    								'VenueName'      => $Venue['name'],
	    								'VenueAddress'   => $Venue['address'],
	    								'VenueCity'      => $Venue['city'],
	    								'VenueCapicity'  => $Venue['capacity'],
                                        // 'VenueImage'     => (!empty($Venue['image_path'])) ? downloadImagesFromLiveServer($Venue['image_path'],'VenueImage') : '',
	    								'CreatedAt'      => date('Y-m-d H:i:s')
	    							);
	    		}else{
	    			$UpdateData[] = array(
	    								'VenueID'        => $VenuesIds[$Venue['id']],
	    								'VenueName'      => $Venue['name'],
	    								'VenueAddress'   => $Venue['address'],
	    								'VenueCity'      => $Venue['city'],
	    								'VenueCapicity'  => $Venue['capacity']
	    							);
	    		}
    		}

    		/* Insert Data */
	    	if(!empty($InsertData)){
	    		$this->db->insert_batch('football_sports_venues',$InsertData);
	    	}

	    	/* Update Data */
	    	if(!empty($UpdateData)){
	    		$this->db->update_batch('football_sports_venues',$UpdateData,'VenueID');
	    	}
    	  }
        }
    }

    /*
      Description: To get football teams data
    */
    function getTeamsLive_Football($CronID) {

    	/* Get Seasons Live Data */
    	$SeasonsData = $this->Football_model->getSeasons('SeasonIDsLive', array('IsCurrentSeason' => 'Yes'), true, 0);
    	if(empty($SeasonsData)){
    		$this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
    	}

    	foreach($SeasonsData['Data']['Records'] as $Season){

            foreach(explode(",", $Season['SeasonIDsLive']) as $SeasonIDLive){

                /* Get Teams By Season Live ID */
                $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'teams/season/'.$SeasonIDLive.'?per_page=100&api_token=');
                if(empty($Response['data'])){
                    continue;
                }

                foreach($Response['data'] as $Value){

                   /* Get League ID */
                   $LeagueID = $this->db->query("SELECT LeagueID FROM football_sports_leagues WHERE SeasonIDLive = ".$Value['current_season_id']." LIMIT 1")->row()->LeagueID;

                    /* To check if team is already exist with particular league */
                   $Query = $this->db->query("SELECT TeamID FROM football_sports_teams WHERE TeamIDLive = ".$Value['id']. " AND LeagueID = ".$LeagueID. " LIMIT 1");
                   if($Query->num_rows() == 0){

                        /* Add team to entity table and get EntityID. */
                        $TeamGUID = get_guid();
                        $TeamID   = $this->Entity_model->addEntity($TeamGUID, array("EntityTypeID" => 9, "StatusID" => 2));
                        $InsertData = array(
                                            'TeamID'         => $TeamID,
                                            'TeamGUID'       => $TeamGUID,
                                            'TeamIDLive'     => $Value['id'],
                                            'LeagueID'       => $LeagueID,
                                            'TeamName'       => $Value['name'],
                                            'TeamNameShort'  => $Value['short_code'],
                                            'TeamFlag'       => (!empty($Value['logo_path'])) ? downloadImagesFromLiveServer($Value['logo_path'],'TeamFlag') : ''
                                        );
                        $this->db->insert('football_sports_teams', array_filter($InsertData));
                   }else{

                        $UpdateData = array(
                                            'TeamName'       => $Value['name'],
                                            'TeamNameShort'  => $Value['short_code']
                                        );

                        /* Update team data */
                        $this->db->where('TeamID', $Query->row()->TeamID);
                        $this->db->limit(1);
                        $this->db->update('football_sports_teams', array_filter($UpdateData));
                    }
                }
            }
    	}
    }

    /*
      Description: To get football teams standings data
    */
    function getTeamsStandingsLive_Football($CronID) {

        /* Get Seasons Live Data */
        $SeasonsData = $this->Football_model->getSeasons('SeasonIDsLive', array('IsCurrentSeason' => 'Yes'), true, 0);
        if(empty($SeasonsData)){
            $this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
        }

        foreach($SeasonsData['Data']['Records'] as $Season){

            foreach(explode(",", $Season['SeasonIDsLive']) as $SeasonIDLive){

                /* Get Teams Standings By Season Live ID */
                $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'standings/season/'.$SeasonIDLive.'?per_page=100&api_token=');
                if(empty($Response['data'])){
                    continue;
                }

                $UpdateData = array();
                foreach($Response['data'] as $Value){
                    if(empty($Value['standings']['data'])){
                        continue;
                    }

                    /* Get League ID */
                    $LeagueID = $this->db->query("SELECT LeagueID FROM football_sports_leagues WHERE SeasonIDLive = ".$Value['season_id']." LIMIT 1")->row()->LeagueID;

                    /* To Get All Live Team Ids */
                    $TeamsLiveIds = array();
                    $TeamsData = $this->db->query('SELECT TeamID,TeamIDLive FROM football_sports_teams WHERE LeagueID = '.$LeagueID);
                    if($TeamsData->num_rows() > 0){
                        $TeamsLiveIds = array_column($TeamsData->result_array(), 'TeamID', 'TeamIDLive');
                    }

                    foreach($Value['standings']['data'] as $Team){
                        if (!isset($TeamsLiveIds[$Team['team_id']])) {
                            continue;
                        }

                        /* Create Team Standings Array */
                        $TeamStandings = array();
                        $TeamStandings['Position']               = (int) @$Team['position'];
                        $TeamStandings['Overall']['GamePlayed']  = (int) @$Team['overall']['games_played'];
                        $TeamStandings['Overall']['Won']         = (int) @$Team['overall']['won'];
                        $TeamStandings['Overall']['Draw']        = (int) @$Team['overall']['draw'];
                        $TeamStandings['Overall']['Lost']        = (int) @$Team['overall']['lost'];
                        $TeamStandings['Overall']['GoalFor']     = (int) @$Team['overall']['goals_scored'];
                        $TeamStandings['Overall']['GoalAgainst'] = (int) @$Team['overall']['goals_against'];
                        $TeamStandings['Home']['GamePlayed']     = (int) @$Team['home']['games_played'];
                        $TeamStandings['Home']['Won']            = (int) @$Team['home']['won'];
                        $TeamStandings['Home']['Draw']           = (int) @$Team['home']['draw'];
                        $TeamStandings['Home']['Lost']           = (int) @$Team['home']['lost'];
                        $TeamStandings['Away']['GamePlayed']     = (int) @$Team['away']['games_played'];
                        $TeamStandings['Away']['Won']            = (int) @$Team['away']['won'];
                        $TeamStandings['Away']['Draw']           = (int) @$Team['away']['draw'];
                        $TeamStandings['Away']['Lost']           = (int) @$Team['away']['lost'];
                        $TeamStandings['Points']                 = (int) @$Team['points'];
                        $TeamStandings['GoalDifference']         = (int) @$Team['total']['goal_difference'];
                        $UpdateData[]  = array(
                                            'TeamID'        => $TeamsLiveIds[$Team['team_id']],
                                            'TeamStandings' => (!empty($TeamStandings)) ? json_encode($TeamStandings) : NULL,
                                            'TeamPosition'  => $Team['position']
                                        );
                    }
                }

                /* Update Data */
                if(!empty($UpdateData)){
                    $this->db->update_batch('football_sports_teams',$UpdateData,'TeamID');
                }
            }
        }
    }

    /*
      Description: To get football league rounds data
    */
    function getLeagueRoundsLive_Football($CronID) {

        /* Get Seasons Live Data */
        $SeasonsData = $this->Football_model->getSeasons('SeasonIDsLive', array('IsCurrentSeason' => 'Yes'), true, 0);
        if(empty($SeasonsData)){
            $this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
        }

        /* To Get All Rounds Data */
        $RoundsIds  = array();
        $RoundsData = $this->Football_model->getRounds('RoundIDLive,RoundID', array(), true, 0);
        if ($RoundsData) {
            $RoundsIds = array_column($RoundsData['Data']['Records'], 'RoundID', 'RoundIDLive');
        }

        $CurrentDate = date('Y-m-d');
        foreach($SeasonsData['Data']['Records'] as $Season){
            
            foreach(explode(",", $Season['SeasonIDsLive']) as $SeasonIDLive){

            /* Get Rounds By Season Live ID */
            $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'rounds/season/'.$SeasonIDLive.'?per_page=100&api_token=');
            if(empty($Response['data'])){
                continue;
            }

            $InsertData = $UpdateData = $UpdateStatusData = array();
            foreach($Response['data'] as $Round){

                /* Get League ID */
                $LeagueID = $this->db->query("SELECT LeagueID FROM football_sports_leagues WHERE SeasonIDLive = ".$Round['season_id']." LIMIT 1")->row()->LeagueID;

                /* Round Status */
                $RoundStatus = 1;
                if((strtotime($Round['start']) <= strtotime($CurrentDate)) && (strtotime($Round['end']) >= strtotime($CurrentDate))){
                    $RoundStatus = 2;
                }else if((strtotime($Round['start']) < strtotime($CurrentDate)) && (strtotime($Round['end']) < strtotime($CurrentDate))){
                    $RoundStatus = 5;
                }

                /* Check If already exist into database */
                if (!isset($RoundsIds[$Round['id']])) {

                    /* Add rounds to entity table and get EntityID. */
                    $RoundGUID = get_guid();
                    $RoundID   = $this->Entity_model->addEntity($RoundGUID, array("EntityTypeID" => 15, "StatusID" => $RoundStatus));
                    $InsertData[] = array(
                                        'RoundID'        => $RoundID,
                                        'RoundGUID'      => $RoundGUID,
                                        'RoundIDLive'    => $Round['id'],
                                        'LeagueID'       => $LeagueID,
                                        'RoundName'      => $Round['name'],
                                        'RoundStartDate' => $Round['start'],
                                        'RoundEndDate'   => $Round['end']
                                    );
                }else{
                    $UpdateData[] = array(
                                        'RoundID'        => $RoundsIds[$Round['id']],
                                        'RoundName'      => $Round['name'],
                                        'RoundStartDate' => $Round['start'],
                                        'RoundEndDate'   => $Round['end']
                                    );

                    $UpdateStatusData[] = array(
                                        'EntityID'  => $RoundsIds[$Round['id']],
                                        'StatusID'  => $RoundStatus
                                    );
                }
            }

            /* Insert Data */
            if(!empty($InsertData)){
                $this->db->insert_batch('football_sports_leagues_rounds',$InsertData);
            }

            /* Update Data */
            if(!empty($UpdateData)){
                $this->db->update_batch('football_sports_leagues_rounds',$UpdateData,'RoundID');
            }

            /* Update Status Data */
            if(!empty($UpdateStatusData)){
                $this->db->update_batch('tbl_entity',$UpdateStatusData,'EntityID');
            }
          }
        }
    }

    /*
      Description: To get football matches data
    */
    function getMatchesLive_Football($CronID) {

        /* Get Current Season Leagues Data */
        $LeaguesData = $this->Football_model->getLeagues('LeagueID,LeagueIDLive,LeagueStartDate,LeagueEndDate', array('Filter' => 'CurrentSeasonLeagues','LeagueSource' => 'API'), true, 0);
        if(empty($LeaguesData)){
            $this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
        }

        /* To Get All Live Venues Ids */
        $VenuesLiveIds = $WeeksDates = array();
        $VenuesData    = $this->db->query('SELECT VenueID,VenueIDLive FROM football_sports_venues WHERE VenueSource = "API" ');
        if($VenuesData->num_rows() > 0){
            $VenuesLiveIds = array_column($VenuesData->result_array(), 'VenueID', 'VenueIDLive');
        }

        foreach($LeaguesData['Data']['Records'] as $Value){

            /* To Get All Live Match Ids */
            $MatchLiveIds = $RoundsLiveIds = $TeamsLiveIds = $TeamLiveColors = array();
            $MatchesData  = $this->db->query('SELECT MatchID,MatchIDLive FROM football_sports_matches WHERE LeagueID = '.$Value['LeagueID']);
            if($MatchesData->num_rows() > 0){
                $MatchLiveIds = array_column($MatchesData->result_array(), 'MatchID', 'MatchIDLive');
            }

            /* To Get All Live Rounds Ids */
            $RoundsData = $this->db->query('SELECT RoundID,RoundIDLive FROM football_sports_leagues_rounds WHERE LeagueID = '.$Value['LeagueID']);
            if($RoundsData->num_rows() > 0){
                $RoundsLiveIds = array_column($RoundsData->result_array(), 'RoundID', 'RoundIDLive');
            }

            /* To Get All Live Team Ids */
            $TeamsData = $this->db->query('SELECT TeamID,TeamIDLive,TeamColor FROM football_sports_teams WHERE LeagueID = '.$Value['LeagueID']);
            if($TeamsData->num_rows() > 0){
                $TeamsLiveIds = array_column($TeamsData->result_array(), 'TeamID', 'TeamIDLive');
                $TeamLiveColors = array_column($TeamsData->result_array(), 'TeamColor', 'TeamIDLive');
            }

            for ($PageNo = 1; $PageNo < 10; $PageNo++) { 

                /* Get Matches By League ID */
                $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'fixtures/between/'.$Value['LeagueStartDate'].'/'.$Value['LeagueEndDate'].'?page='.$PageNo.'&per_page=150&status=NS&leagues='.$Value['LeagueIDLive'].'&api_token=');
                if(empty($Response['data'])){
                    break;
                }

                $InsertData = $UpdateData = array();
                foreach($Response['data'] as $Match){

                    /* Manage Local Team Colors */
                    if(!empty($Match['colors']['localteam']['color']) && empty($TeamLiveColors[$Match['localteam_id']])){
                        $TeamLiveColors[$Match['localteam_id']] = $Match['colors']['localteam']['color'];
                        $this->db->query("UPDATE `football_sports_teams` SET `TeamColor` = '".$Match['colors']['localteam']['color']."' WHERE `TeamIDLive` = ".$Match['localteam_id']." AND LeagueID = ".$Value['LeagueID']." LIMIT 1");
                    }

                    /* Manage Visitor Team Colors */
                    if(!empty($Match['colors']['visitorteam']['color']) && empty($TeamLiveColors[$Match['visitorteam_id']])){
                        $TeamLiveColors[$Match['visitorteam_id']] = $Match['colors']['visitorteam']['color'];
                        $this->db->query("UPDATE `football_sports_teams` SET `TeamColor` = '".$Match['colors']['visitorteam']['color']."' WHERE `TeamIDLive` = ".$Match['visitorteam_id']." AND LeagueID = ".$Value['LeagueID']." LIMIT 1");
                    }

                    /* Manage Venue ID */
                    if(!isset($VenuesLiveIds[$Match['venue_id']])){

                        /* Get Venue Details By Venue Live ID */
                        $VenueResponse = $this->callSportsAPI(SPORTMONKS_API_URL . 'venues/'.$Match['venue_id'].'?api_token=');
                         if(empty($VenueResponse['data']))
                        {
                            continue;
                        }
                        $VenueArr = array(
                                        'VenueIDLive'   => $VenueResponse['data']['id'],
                                        'VenueName'     => $VenueResponse['data']['name'],
                                        'VenueAddress'  => $VenueResponse['data']['address'],
                                        'VenueCity'     => $VenueResponse['data']['city'],
                                        'VenueCapicity' => $VenueResponse['data']['capacity'],
                                        'CreatedAt'     => date('Y-m-d H:i:s')
                                    );
                        $this->db->insert('football_sports_venues',array_filter($VenueArr));
                        $VenuesLiveIds[$Match['venue_id']] = $this->db->insert_id();
                    }

                    /* Get WeekID */
                    $MatchStartDate = date('Y-m-d',strtotime($Match['time']['starting_at']['date_time']));
                    if(!isset($WeeksDates[$MatchStartDate])){
                        $WeeksDates[$MatchStartDate] = $this->db->query("SELECT WeekID FROM `football_sports_season_weeks` WHERE `WeekStartDate` <= '".$MatchStartDate."' AND `WeekEndDate` >= '".$MatchStartDate."'")->row()->WeekID;
                    }

                    $MatchScoreDetails = array();
                    $MatchScoreDetails['LocalTeamScore']   = (int) @$Match['scores']['localteam_score'];
                    $MatchScoreDetails['VisitorTeamScore'] = (int) @$Match['scores']['visitorteam_score'];
                    $MatchScoreDetails['HalfTimeScore']    = (!empty($Match['scores']['ht_score'])) ? $Match['scores']['ht_score'] : '';
                    $MatchScoreDetails['FullTimeScore']    = (!empty($Match['scores']['ft_score'])) ? $Match['scores']['ft_score'] : '';
                    if($Match['time']['status'] == 'FT'){
                        $MatchScoreDetails['WinnerTeam']    = ((!empty($Match['winner_team_id'])) ? (($Match['winner_team_id'] == $Match['localteam_id']) ? 'Local' : 'Visitor') : 'Draw');
                        $MatchScoreDetails['WinnerTeamID']  = (!empty($Match['winner_team_id'])) ? $TeamsLiveIds[$Match['winner_team_id']] : '';
                    }

                    /* Manage Match Status */
                    switch ($Match['time']['status']) {
                        case 'NS':
                        case 'ABAN':
                        case 'SUSP':
                        case 'DELAYED':
                            $MatchStatus = 1;
                            break;
                        case 'LIVE':
                        case 'HT':
                        case 'ET':
                        case 'PEN_LIVE':
                        case 'BREAK':
                        case 'INT':
                            $MatchStatus = 2;
                            break;
                        case 'CANCL':
                        case 'POSTP':
                            $MatchStatus = 3;
                            break;
                        case 'FT':
                        case 'AET':
                        case 'FT_PEN':
                            $MatchStatus = 5;
                            break;
                        default:
                            $MatchStatus = 1;
                            break;
                    }

                    /* Check If already exist into database */
                    if (!isset($MatchLiveIds[$Match['id']])) {

                        /* Add matches to entity table and get EntityID. */
                        $MatchGUID = get_guid();
                        $MatchID   = $this->Entity_model->addEntity($MatchGUID, array("EntityTypeID" => 8, "StatusID" => $MatchStatus));
                        $InsertData[] = array(
                                            'MatchID'            => $MatchID,
                                            'MatchGUID'          => $MatchGUID,
                                            'MatchIDLive'        => $Match['id'],
                                            'LeagueID'           => $Value['LeagueID'],
                                            'WeekID'             => $WeeksDates[$MatchStartDate],
                                            'RoundID'            => $RoundsLiveIds[$Match['round_id']],
                                            'VenueID'            => $VenuesLiveIds[$Match['venue_id']],
                                            'TeamIDLocal'        => $TeamsLiveIds[$Match['localteam_id']],
                                            'TeamIDVisitor'      => $TeamsLiveIds[$Match['visitorteam_id']],
                                            'MatchStartDateTime' => $Match['time']['starting_at']['date_time'],
                                            'MatchScoreDetails'  => json_encode($MatchScoreDetails)
                                        );
                    }else{
                        $UpdateData[] = array(
                                            'MatchID'            => $MatchLiveIds[$Match['id']],
                                            'WeekID'             => $WeeksDates[$MatchStartDate],
                                            'MatchStartDateTime' => $Match['time']['starting_at']['date_time'],
                                            'MatchScoreDetails'  => json_encode($MatchScoreDetails)
                                        );
                    }
                }

                /* Insert Data */
                if(!empty($InsertData)){
                    $this->db->insert_batch('football_sports_matches',$InsertData);
                }

                /* Update Data */
                if(!empty($UpdateData)){
                    $this->db->update_batch('football_sports_matches',$UpdateData,'MatchID');
                }
            }
        }
    }

    /*
      Description: To get football live matches football data
    */
    function getMatchScoreLive_Football($CronID) {

        /* Get Live Running Matches By League ID */
       $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'livescores?per_page=150&api_token=');

        // SELECT GROUP_CONCAT(`MatchIDLive`) FROM `football_sports_matches` WHERE `MatchID` IN (SELECT MatchID FROM `football_sports_matches_prediction` )

        // $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'fixtures/multi/16840082,16840092,16840097,16840098,16840099,16840101,16840113,16481420,16924615,16924617,16924623,16924635,17082861?include=lineup&api_token=');
        if(empty($Response['data'])){
            $this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
        }
        $LongestOddsLabelArr = array("1" => "Home", "X" => "Draw", "2" => "Away");
        foreach($Response['data'] as $Match){

            /* Not Started Match */
            if($Match['time']['status'] == 'NS'){
                continue;
            }

            /* To Get Match Details */
            $MatchDetails = $this->db->query('SELECT M.MatchID,M.MatchIDLive,M.LeagueID,E.StatusID FROM tbl_entity E, football_sports_matches M WHERE E.EntityID = M.MatchID AND M.MatchIDLive = '.$Match['id'].' LIMIT 1')->row_array();
            if($MatchDetails['StatusID'] == 3 || $MatchDetails['StatusID'] == 5){
                continue;
            }

            $MatchScoreDetails = array();
            $MatchScoreDetails['LocalTeamScore']   = (int) @$Match['scores']['localteam_score'];
            $MatchScoreDetails['VisitorTeamScore'] = (int) @$Match['scores']['visitorteam_score'];
            $MatchScoreDetails['HalfTimeScore']    = (!empty($Match['scores']['ht_score'])) ? $Match['scores']['ht_score'] : '';
            $MatchScoreDetails['FullTimeScore']    = (!empty($Match['scores']['ft_score'])) ? $Match['scores']['ft_score'] : '';
            if($Match['time']['status'] == 'FT'){
                $MatchScoreDetails['WinnerTeam']    = ((!empty($Match['winner_team_id'])) ? (($Match['winner_team_id'] == $Match['localteam_id']) ? 'Local' : 'Visitor') : 'Draw');
                $MatchScoreDetails['WinnerTeamID']  = (!empty($Match['winner_team_id'])) ? $this->db->query('SELECT TeamID FROM football_sports_teams WHERE TeamIDLive = '.$Match['winner_team_id'].' AND LeagueID = '.$MatchDetails['LeagueID'].' LIMIT 1')->row()->TeamID : '';
            }

            /* Manage Match Status */
            // https://sportmonks.com/docs/football/2.0/getting-started/a/statuses-definitions/83
            switch ($Match['time']['status']) {
                case 'NS':
                case 'ABAN':
                case 'SUSP':
                case 'DELAYED':
                    $MatchStatus = 1;
                    break;
                case 'LIVE':
                case 'HT':
                case 'ET':
                case 'PEN_LIVE':
                case 'BREAK':
                case 'INT':
                    $MatchStatus = 2;
                    break;
                case 'CANCL':
                case 'POSTP':
                    $MatchStatus = 3;
                    break;
                case 'FT':
                case 'AET':
                case 'FT_PEN':
                    $MatchStatus = 5;
                    break;
            }

            /* Update Match Score Data */
            $this->db->where('MatchID', $MatchDetails['MatchID']);
            $this->db->limit(1);
            $this->db->update('football_sports_matches',array('MatchScoreDetails' => json_encode($MatchScoreDetails)));

            /* Update Match Status */
            $this->db->where('EntityID', $MatchDetails['MatchID']);
            $this->db->limit(1);
            $this->db->update('tbl_entity',array('StatusID' => $MatchStatus));


            if($Match['time']['status'] == 'FT'){

                /* Get Longest Odds Label */
                $OddsResponse = $this->callSportsAPI(SPORTMONKS_API_URL . 'odds/fixture/'.$MatchDetails['MatchIDLive'].'?api_token=');
                if(!empty($OddsResponse['data'])){
                    foreach ($OddsResponse['data'] as $key => $Odds) {
                        if($Odds['name'] == '3Way Result'){ // Home + Draw + Away
                            if(!empty($Odds['bookmaker']['data'])){
                                foreach($Odds['bookmaker']['data'] as $OddsLabel){
                                    if(!empty($OddsLabel['odds']['data'])){
                                        $LongestOddsLabel = '';
                                        $CurrentLabelValue = 0;
                                        foreach ($OddsLabel['odds']['data'] as $key => $Value) {
                                            if($Value['value'] > $CurrentLabelValue){
                                                $LongestOddsLabel  = $LongestOddsLabelArr[$Value['label']];
                                                $CurrentLabelValue = $Value['value'];
                                            }
                                        }
                                        if(!empty($LongestOddsLabel)){

                                            /* Update Match Longest Odds Label*/
                                            $this->db->where('MatchID', $MatchDetails['MatchID']);
                                            $this->db->limit(1);
                                            $this->db->update('football_sports_matches',array('LongestOddsLabel' => $LongestOddsLabel));
                                        }
                                        break;
                                    }
                                }
                            }
                            break;
                        }
                    }
                }

                /* Calculate User Points & Update Leaderboard */
                $this->Football_model->calculatePoints($MatchDetails['MatchID'],$MatchScoreDetails,@$LongestOddsLabel);
            }
        }
    }

    /*
      Description: To get football live match team lienups data
    */
    function getMatchTeamLineUpsLive_Football($CronID) {

        /* To Get Completed Matches */        
        $MatchesData = $this->Football_model->getMatches('MatchIDLive,LeagueID,MatchID,TeamIDLocal,TeamIDVisitor,TeamIDLiveLocal,TeamIDLiveVisitor',array('StatusID' => 5, 'IsTeamLineUp' => 'No', 'MatchSource' => 'API', 'OrderBy' => 'MatchStartDateTime', 'Sequence' => 'DESC'),TRUE,1,20);
        if(empty($MatchesData)){
            $this->db->where('CronID', $CronID);
            $this->db->limit(1);
            $this->db->update('log_cron', array('CronStatus' => 'Exit'));
            return true;
        }

        /* To Get All Team Ids */
        $TeamIds = array_column($MatchesData['Data']['Records'], 'TeamIDLocal','TeamIDLiveLocal') + array_column($MatchesData['Data']['Records'], 'TeamIDVisitor','TeamIDLiveVisitor');

        /* Player Positions */
        $PlayerPositions = array('G' => 'Goalkeeper', 'D' => 'Defender', 'M' => 'Midfielder', 'A' => 'Forward');

        /* To Get Completed Fixtures Data */
        foreach($MatchesData['Data']['Records'] as $Match){

            /* To Get Live Data */
            $Response = $this->callSportsAPI(SPORTMONKS_API_URL . 'fixtures/'.$Match['MatchIDLive'].'?include=lineup&api_token=');

            /* Update Team Lineup */
            if(empty($Response['data']['lineup']['data'])){
                continue;
            }

            $TeamPlayersData = array();
            foreach($Response['data']['lineup']['data'] as $Player){

                /* To check if player is already exist */
                $Query = $this->db->query('SELECT PlayerID FROM football_sports_players WHERE PlayerIDLive = "' . $Player['player_id'] . '" LIMIT 1');
                $PlayerID = ($Query->num_rows() > 0) ? $Query->row()->PlayerID : FALSE;

                /* Insert Player */
                if (!$PlayerID) {

                    /* Add players to entity table and get EntityID. */
                    $PlayerGUID = get_guid();
                    $PlayerID = $this->Entity_model->addEntity($PlayerGUID, array("EntityTypeID" => 10, "StatusID" => 2));
                    $PlayersAPIData = array(
                                        'PlayerID'     => $PlayerID,
                                        'PlayerGUID'   => $PlayerGUID,
                                        'PlayerIDLive' => $Player['player_id'],
                                        'PlayerName'   => $Player['player_name']
                                    );
                    $this->db->insert('football_sports_players', $PlayersAPIData);
                }

                /* Manage Match Team Players Data */
                $TeamPlayersData[] = array(
                                        'PlayerID' => $PlayerID,
                                        'LeagueID' => $Match['LeagueID'],
                                        'TeamID'   => $TeamIds[$Player['team_id']],
                                        'MatchID'  => $Match['MatchID'],
                                        'PlayerRole' => $PlayerPositions[$Player['position']]
                                    );
            }

            /* Insert Team Players Data */
            if (!empty($TeamPlayersData)) {

                /* To Delete Already Exist Player */
                $this->db->query('DELETE FROM `football_sports_team_players` WHERE MatchID = '.$Match['MatchID']);

                /* Insert Match Players */
                $this->db->insert_batch('football_sports_team_players', $TeamPlayersData);

                /* Update Team Lineup Flag */
                $this->db->where(array('MatchID' => $Match['MatchID'], 'IsTeamLineUp' => 'No'));
                $this->db->limit(1);
                $this->db->update('football_sports_matches',array('IsTeamLineUp' => 'Yes'));
            }
        }
    }

    /*
      Description: To distribute winning (On Every Thursday)
    */
    function distributeWinning_Football($CronID) {

        $CurrentDate = date('Y-m-d');
        if(date('l',strtotime($CurrentDate)) == 'Thursday'){
            
            /* To Get Completed Week ID */
            $Query = $this->db->query('SELECT W.WeekID FROM `football_sports_season_weeks` W, tbl_entity E WHERE W.WeekID = E.EntityID AND W.`WeekStartDate` <= "'.$CurrentDate.'" AND W.`WeekEndDate` >= "'.$CurrentDate.'" AND E.StatusID = 5 LIMIT 1');
            if($Query->num_rows() > 0){

                /* To Get Completed Matches */
                $Query = $this->db->query('SELECT M.MatchID FROM `football_sports_matches` M, tbl_entity E WHERE M.MatchID = E.EntityID AND E.StatusID = 5 AND M.WeekID = '.$Query->row()->WeekID);
                if($Query->num_rows() > 0){

                    /* To Get User Predictions  */
                    $Query = $this->db->query('SELECT MatchID,UserID,SUM(`ExactScorePoints` + `CorrectResultPoints` + `BothTeamScorePoints` + `LongestOddsScorePoints`) TotalPoints FROM `football_sports_matches_prediction` WHERE `MatchID` IN ('.implode(",", $Query->result_array()).') AND `PredictionStatus` = "Lock" AND `IsWinning` = "No" HAVING TotalPoints > 0');
                    if($Query->num_rows() > 0){
                        foreach ($Query->result_array() as $Value) {
                            $this->db->trans_start();

                            /* To Check If Winning Is Already Distributed */
                            $Query = $this->db->query('SELECT 1 FROM tbl_users_wallet WHERE UserID = '.$Value['UserID'].' AND EntityID = '.$Value['MatchID'].' AND Narration = "Football Winning" LIMIT 1');
                            if($Query->num_rows() == 0){
                                $WalletData = array(
                                    "UserID"          => $Value['UserID'],
                                    "EntityID"        => $Value['MatchID'],
                                    "Amount"          => $Value['TotalPoints'],
                                    "WinningAmount"   => $Value['TotalPoints'],
                                    "TransactionType" => 'Cr',
                                    "Narration"       => 'Football Winning',
                                    "EntryDate"       => date("Y-m-d H:i:s")
                                );
                                $this->Users_model->addToWallet($WalletData, $Value['UserID'], 5);
                            }

                            /* Update Prediction Status */
                            $this->db->where(array('UserID' => $Value['UserID'], 'MatchID' => $Value['MatchID']));
                            $this->db->limit(1);
                            $this->db->update('football_sports_matches_prediction', array('WinningAmount' => $Value['TotalPoints'], 'IsWinning' => 'Yes', 'WinningDateTime' => date('Y-m-d H:i:s')));

                            $this->db->trans_complete();
                            if ($this->db->trans_status() === false) {
                                return false;
                            }
                        }
                    }
                }
            }
        }
    }   

}
