<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Football extends API_Controller_Secure {

    function __construct() {
        parent::__construct();
        $this->load->model('Football_model');
    }

    /*
      Name: 		getSeasons
      Description: 	Use to get Season list.
      URL: 			/admin/football/getSeasons
    */
    public function getSeasons_post() {
        /* Validation section */
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $SeasonsData = $this->Football_model->getSeasons((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), $this->Post, (!empty($this->Post['SeasonID']) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($SeasonsData) {
            $this->Return['Data'] = (!empty($this->SeasonID)) ? $SeasonsData :  $SeasonsData['Data'];
        }
    }

    /*
    Name:           addLeague
    Description:    Use to add League info.
    URL:            /Football/addLeague/
    */
    public function addLeague_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('LeagueName', 'LeagueName', 'trim|required|is_unique[football_sports_leagues.LeagueName]');
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|required|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->set_rules('SeasonID', 'SeasonID', 'trim|required|numeric');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $LeagueID = $this->Football_model->addLeague($this->Post,$this->CompetitionID);
        if ($LeagueID) {

            /* Add League Image */
            if (!empty($this->Post['MediaGUIDs'])) {
                $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
                foreach ($MediaGUIDsArray as $MediaGUID) {
                    $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                    if($MediaID){
                        $this->Football_model->updateLeagueData($LeagueID,array('LeagueFlag' => @$MediaID['MediaName']));
                    }
                }
            }
            $this->Return['Message'] = "League Details added successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }

    /*
      Name: 		getLeagues
      Description: 	Use to get Leagues list.
      URL: 			/admin/football/getLeagues
    */
    public function getLeagues_post() {
        /* Validation section */
        $this->form_validation->set_rules('SeasonID', 'SeasonID', 'trim|integer');
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $LeaguesData = $this->Football_model->getLeagues((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'LeagueID' => @$this->LeagueID,'CompetitionID' => @$this->CompetitionID,'SeasonID' => $this->Post['SeasonID'])), (!empty($this->LeagueID)) ? FALSE : TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($LeaguesData) {
            $this->Return['Data'] = (!empty($this->LeagueID)) ? $LeaguesData :  $LeaguesData['Data'];
        }
    }

    /*
        Description:    Use to update League.
        URL:            /admin/football/updateLeague/ 
    */
    public function updateLeague_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|required|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->set_rules('LeagueName', 'LeagueName', 'trim|required');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!empty($this->Post['MediaGUIDs'])) {
            $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
            foreach ($MediaGUIDsArray as $MediaGUID) {
                $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                if ($MediaID) {
                    $this->Media_model->addMediaToEntity($MediaID['MediaID'], $this->SessionUserID, $this->LeagueID);
                }
            }
        }

        /* Update League Data */
        if($this->Football_model->updateLeagueData($this->LeagueID,$this->CompetitionID, array_merge($this->Post,array('LeagueFlag'=> @$MediaID['MediaName'])))){
            $this->Return['Data']    = $this->Football_model->getLeagues('LeagueFlag,Status',array('LeagueID' => $this->LeagueID));
            $this->Return['Message'] = "League info updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] =  "Something went wrong, please try again later";
        }
    }

    /*
     Name:           deleteLeague
     Description:    Use to delete League
     URL:            /Football/deleteLeague/
    */
    public function deleteLeague_post() {
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete League Data */
        $this->Football_model->deleteLeague($this->LeagueID);
        $this->Return['Message'] = "League deleted successfully.";
    }


     /*
    Name:           addCompetition
    Description:    Use to add Competition info.
    URL:            /Football/addCompetition/
    */
    public function addCompetition_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('CompetitionName', 'CompetitionName', 'trim|required|is_unique[football_sports_competitions.CompetitionName]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $CompetitionID = $this->Football_model->addCompetition($this->Post);
        if ($CompetitionID) {

            /* Add League Image */
            if (!empty($this->Post['MediaGUIDs'])) {
                $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
                foreach ($MediaGUIDsArray as $MediaGUID) {
                    $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                    if($MediaID){
                        $this->Football_model->updateCompetitionData($CompetitionID,array('CompetitionFlag' => @$MediaID['MediaName']));
                    }
                }
            }
            $this->Return['Message'] = "Competition Details added successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }


     /*
      Name: 		getCompetition
      Description: 	Use to get Competitions list.
      URL: 			/admin/football/getCompetitions
    */
    public function getCompetitions_post() {
        /* Validation section */
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $CompetitionsData = $this->Football_model->getCompetitions((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'CompetitionID' => @$this->CompetitionID)), (!empty($this->CompetitionID)) ? FALSE : TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($CompetitionsData) {
            $this->Return['Data'] = (!empty($this->CompetitionID)) ? $CompetitionsData :  $CompetitionsData['Data'];
        }
    }

     /*
        Description:    Use to update Competition.
        URL:            /admin/football/updateCompetition/ 
    */
    public function updateCompetition_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|required|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->set_rules('CompetitionName', 'CompetitionName', 'trim|required');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!empty($this->Post['MediaGUIDs'])) {
            $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
            foreach ($MediaGUIDsArray as $MediaGUID) {
                $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                if ($MediaID) {
                    $this->Media_model->addMediaToEntity($MediaID['MediaID'], $this->SessionUserID, $this->CompetitionID);
                }
            }
        }

        /* Update Competition Data */
        if($this->Football_model->updateCompetitionData($this->CompetitionID, array_merge($this->Post,array('CompetitionFlag'=> @$MediaID['MediaName'])))){
            $this->Return['Data']    = $this->Football_model->getCompetitions('CompetitionFlag,Status',array('CompetitionID' => $this->CompetitionID));
            $this->Return['Message'] = "Competition info updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] =  "Something went wrong, please try again later";
        }
    }

    /*
     Name:           deleteCompetition
     Description:    Use to delete Competition
     URL:            /Football/deleteCompetition/
    */
    public function deleteCompetition_post() {
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|required|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete Competition Data */
        $this->Football_model->deleteCompetition($this->CompetitionID);
        $this->Return['Message'] = "Competition deleted successfully.";
    }

    /*
      Name: 		getRounds
      Description: 	Use to get Rounds list.
      URL: 			/admin/football/getRounds
    */
    

    public function getRounds_post() {
        /* Validation section */
        $this->form_validation->set_rules('RoundGUID', 'RoundGUID', 'trim|callback_validateEntityGUID[Rounds,RoundID]');
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $RoundsData = $this->Football_model->getRounds((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'RoundID' => @$this->RoundID, 'LeagueID' => @$this->LeagueID)), (!empty($this->RoundID) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($RoundsData) {
            $this->Return['Data'] = (!empty($this->RoundID) ? $RoundsData : $RoundsData['Data']);
        }
    }

    /*
      Name:         getWeeks
      Description:  Use to get Weeks list.
      URL:          /football/getWeeks
    */

    public function getWeeks_post() {
        /* Validation section */
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $WeeksData = $this->Football_model->getWeeks((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'WeekID' => @$this->WeekID)), (!empty($this->WeekID) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($WeeksData) {
            $this->Return['Data'] = (!empty($this->WeekID)) ? $WeeksData :  $WeeksData['Data'];
        }
    }

    /*
    Name:           addTeam
    Description:    Use to add Team info.
    URL:            /Football/addTeam/
    */
    public function addTeam_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('TeamName', 'TeamName', 'trim|required');
        $this->form_validation->set_rules('TeamNameShort', 'TeamShortName', 'trim|required|is_unique[football_sports_teams.TeamNameShort]');
        $this->form_validation->set_rules('TeamColor', 'TeamColor', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $TeamID = $this->Football_model->addTeam(array_merge($this->Post,array('LeagueID' => $this->LeagueID)));
        if ($TeamID) {

            /* Add Team Image */
            if (!empty($this->Post['MediaGUIDs'])) {
                $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
                foreach ($MediaGUIDsArray as $MediaGUID) {
                    $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                    if($MediaID){
                        $this->Football_model->updateTeamDetails($TeamID,array('TeamFlag' => @$MediaID['MediaName']));
                    }
                }
            }
            $this->Return['Message'] = "Team Details added successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }

    /*
      Name: 		getTeams
      Description: 	Use to get Team list.
      URL: 			/admin/football/getTeams
    */

    public function getTeams_post() {
        /* Validation section */
        $this->form_validation->set_rules('TeamGUID', 'TeamGUID', 'trim|callback_validateEntityGUID[Teams,TeamID]');
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $TeamData = $this->Football_model->getTeams((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'TeamID' => @$this->TeamID,'LeagueID' => @$this->LeagueID)), (!empty($this->TeamID) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($TeamData) {
            $this->Return['Data'] = (!empty($this->TeamID) ? $TeamData : $TeamData['Data']);
        }
    }

    /*
	Name: 			updateTeam
	Description: 	Use to update Team info.
	URL: 			/Football/updateTeam/
	*/
	public function updateTeam_post()
	{
		/* Validation section */
		$this->form_validation->set_rules('TeamGUID', 'TeamGUID', 'trim|required|callback_validateEntityGUID[Teams,TeamID]');
		$this->form_validation->set_rules('TeamName', 'TeamName', 'trim|required');
		$this->form_validation->set_rules('TeamNameShort', 'TeamShortName', 'trim|required');
        $this->form_validation->set_rules('TeamColor', 'TeamColor', 'trim|required');
		$this->form_validation->validation($this);  /* Run validation */
		/* Validation - ends */

        if (!empty($this->Post['MediaGUIDs'])) {
            $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
            foreach ($MediaGUIDsArray as $MediaGUID) {
                $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                if ($MediaID) {
                    $this->Media_model->addMediaToEntity($MediaID['MediaID'], $this->SessionUserID, $this->TeamID);
                }
            }
        }

		if ($this->Football_model->updateTeamDetails($this->TeamID,array_merge($this->Post,array('TeamFlag' => @$MediaID['MediaName'])))) {
            $this->Return['Data']    = $this->Football_model->getTeams('TeamFlag,TeamName,TeamNameShort,TeamColor',array('TeamID' => $this->TeamID));
            $this->Return['Message'] = "Team Details updated successfully";
		}else{
			$this->Return['ResponseCode'] = 500;
			$this->Return['Message']      	= "Something went wrong, please try again later!";
		}
	}

    /*
    Name:           updateTeamStandings
    Description:    Use to update Team standings.
    URL:            /Football/updateTeamStandings/
    */
    public function updateTeamStandings_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('TeamGUID', 'TeamGUID', 'trim|required|callback_validateEntityGUID[Teams,TeamID]');
        $this->form_validation->set_rules('Position', 'Position', 'trim|required|numeric');
        $this->form_validation->set_rules('OverallGamePlayed', 'OverallGamePlayed', 'trim|required|numeric');
        $this->form_validation->set_rules('OverallWon', 'OverallWon', 'trim|required|numeric');
        $this->form_validation->set_rules('OverallDraw', 'OverallDraw', 'trim|required|numeric');
        $this->form_validation->set_rules('OverallLost', 'OverallLost', 'trim|required|numeric');
        $this->form_validation->set_rules('OverallGoalFor', 'OverallGoalFor', 'trim|required|numeric');
        $this->form_validation->set_rules('OverallGoalAgainst', 'OverallGoalAgainst', 'trim|required|numeric');
        $this->form_validation->set_rules('HomeGamePlayed', 'HomeGamePlayed', 'trim|required|numeric');
        $this->form_validation->set_rules('HomeWon', 'HomeWon', 'trim|required|numeric');
        $this->form_validation->set_rules('HomeDraw', 'HomeDraw', 'trim|required|numeric');
        $this->form_validation->set_rules('HomeLost', 'HomeLost', 'trim|required|numeric');
        $this->form_validation->set_rules('AwayGamePlayed', 'AwayGamePlayed', 'trim|required|numeric');
        $this->form_validation->set_rules('AwayWon', 'AwayWon', 'trim|required|numeric');
        $this->form_validation->set_rules('AwayDraw', 'AwayDraw', 'trim|required|numeric');
        $this->form_validation->set_rules('AwayLost', 'AwayLost', 'trim|required|numeric');
        $this->form_validation->set_rules('Points', 'Points', 'trim|required|numeric');
        $this->form_validation->set_rules('GoalDifference', 'GoalDifference', 'trim|required|numeric');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Football_model->updateTeamStandings($this->TeamID,$this->Post);
        $this->Return['Message'] = "Team Standings updated successfully";
    }

    /*
     Name:           deleteTeam
     Description:    Use to delete Team
     URL:            /Football/deleteTeam/
    */
    public function deleteTeam_post() {
        $this->form_validation->set_rules('TeamGUID', 'TeamGUID', 'trim|required|callback_validateEntityGUID[Teams,TeamID]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete Team Data */
        $this->Football_model->deleteTeam($this->TeamID);
        $this->Return['Message'] = "Team deleted successfully.";
    }

    /*
    Name:           addVenue
    Description:    Use to add Venue info.
    URL:            /Football/addVenue/
    */
    public function addVenue_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('VenueName', 'VenueName', 'trim|required');
        $this->form_validation->set_rules('VenueAddress', 'VenueAddress', 'trim|required');
        $this->form_validation->set_rules('VenueCity', 'VenueCity', 'trim|required');
        $this->form_validation->set_rules('VenueCapicity', 'VenueCapicity', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $VenueID = $this->Football_model->addVenue($this->Post);
        if ($VenueID) {

            /* Add Venue Image */
            if (!empty($this->Post['MediaGUIDs'])) {
                $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
                foreach ($MediaGUIDsArray as $MediaGUID) {
                    $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                    if($MediaID){
                        $this->Football_model->updateVenueDetails($VenueID,array('VenueImage' => @$MediaID['MediaName']));
                    }
                }
            }
            $this->Return['Message'] = "Venue Details added successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }

	/*
      Name: 		getVenues
      Description: 	Use to get Venue list.
      URL: 			/admin/football/getVenues
    */

    public function getVenues_post() {
        /* Validation section */
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $VenueData = $this->Football_model->getVenues((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), $this->Post, (!empty($this->Post['VenueID']) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($VenueData) {
            $this->Return['Data'] = (!empty($this->Post['VenueID'])) ? $VenueData :  $VenueData['Data'];
        }
    }

    /*
	Name: 			updateVenue
	Description: 	Use to update Venue info.
	URL: 			/Football/updateVenue/
	*/
	public function updateVenue_post()
	{
        /* Validation section */
        $this->form_validation->set_rules('VenueID', 'VenueID', 'trim|required');
        $this->form_validation->set_rules('VenueName', 'VenueName', 'trim|required');
        $this->form_validation->set_rules('VenueAddress', 'VenueAddress', 'trim|required');
        $this->form_validation->set_rules('VenueCity', 'VenueCity', 'trim|required');
        $this->form_validation->set_rules('VenueCapicity', 'VenueCapicity', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!empty($this->Post['MediaGUIDs'])) {
            $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
            foreach ($MediaGUIDsArray as $MediaGUID) {
                $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                // if ($MediaID) {
                //     $this->Media_model->addMediaToEntity($MediaID['MediaID'], $this->SessionUserID, $this->Post['VenueID']);
                // }
            }
        }

        if ($this->Football_model->updateVenueDetails($this->Post['VenueID'],array_merge($this->Post,array('VenueImage' => @$MediaID['MediaName'])))) {
            $this->Return['Data']    = $this->Football_model->getVenues('VenueName,VenueAddress,VenueCity,VenueCapicity,VenueImage',array('VenueID' => $this->Post['VenueID']));
            $this->Return['Message'] = "Venue Details updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }

    /*
     Name:           deleteVenue
     Description:    Use to delete Venue
     URL:            /Football/deleteVenue/
    */
    public function deleteVenue_post() {
        $this->form_validation->set_rules('VenueID', 'VenueID', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete Venue Data */
        $this->Football_model->deleteVenue($this->Post['VenueID']);
        $this->Return['Message'] = "Venue deleted successfully.";
    }

    /*
    Name:           addMatch
    Description:    Use to add Match info.
    URL:            /Football/addMatch/
    */
    public function addMatch_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|required|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('VenueID', 'VenueID', 'trim|required');
        $this->form_validation->set_rules('TeamGUIDLocal', 'TeamGUIDLocal', 'trim|required|callback_validateEntityGUID[Teams,TeamIDLocal]');
        $this->form_validation->set_rules('TeamGUIDVisitor', 'TeamGUIDVisitor', 'trim|required|callback_validateEntityGUID[Teams,TeamIDVisitor]');
        $this->form_validation->set_rules('MatchStartDateTime', 'MatchStartDateTime', 'trim|required');
        $this->form_validation->set_rules('TimeZoneIdentifire', 'TimeZoneIdentifire', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if ($this->Football_model->addMatch(array_merge($this->Post,array('LeagueID' => $this->LeagueID,'WeekID' => $this->WeekID,'TeamIDLocal' => $this->TeamIDLocal,'TeamIDVisitor' => $this->TeamIDVisitor)))) {
            $this->Return['Message'] = "Match Details added successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }

    /*
      Name:         getMatches
      Description:  Use to get Match list.
      URL:          /football/getMatches
    */
    public function getMatches_post() {

        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('TeamGUID', 'TeamGUID', 'trim|callback_validateEntityGUID[Teams,TeamID]');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $MatchesData = $this->Football_model->getMatches((!empty($this->Post['Params']) ? $this->Post['Params'] : ''),array_merge($this->Post,array('LeagueID' => @$this->LeagueID,'MatchID' => @$this->MatchID,'StatusID'=> @$this->StatusID,'WeekID' => @$this->WeekID,'TeamID' => @$this->TeamID)), (!empty($this->MatchID) ? FALSE : TRUE),@$this->Post['PageNo'],@$this->Post['PageSize']);
        if ($MatchesData) {
            $this->Return['Data'] = (!empty($this->MatchID) ? $MatchesData : $MatchesData['Data']);
        }
    }

    /*
     Name:           manageLiveScoring
     Description:    Use to Manage Match Live scoring
     URL:            /Football/manageLiveScoring/
    */
    public function manageLiveScoring_post() {
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('HalfTimeLocalTeamScore', 'HalfTimeLocalTeamScore', 'trim|required|numeric');
        $this->form_validation->set_rules('HalfTimeVisitorTeamScore', 'HalfTimeVisitorTeamScore', 'trim|required|numeric');
        $this->form_validation->set_rules('FullTimeLocalTeamScore', 'FullTimeLocalTeamScore', 'trim|required|numeric');
        $this->form_validation->set_rules('FullTimeVisitorTeamScore', 'FullTimeVisitorTeamScore', 'trim|required|numeric');
        $this->form_validation->set_rules('Status', 'Status', 'trim|required|callback_validateStatus');
        if($this->Post['Status'] && $this->Post['Status'] == 'Completed'){
            $this->form_validation->set_rules('WinnerTeam', 'WinnerTeam', 'trim|required|in_list[Local,Visitor,Draw]');
            if($this->Post['WinnerTeam'] != 'Draw'){
              $this->form_validation->set_rules('WinnerTeamGUID', 'WinnerTeamGUID', 'trim|required|callback_validateEntityGUID[Teams,WinnerTeamID]');
            }
            $this->form_validation->set_rules('LongestOdds', 'LongestOdds', 'trim|required|in_list[Home,Away,Draw]');
        }
        $this->form_validation->validation($this);  /* Run validation */

        /* Update Live Match Data */
        $this->Football_model->manageLiveScoring($this->MatchID,array_merge($this->Post,array('WinnerTeamID' => @$this->WinnerTeamID, 'StatusID' => $this->StatusID)));
        $this->Return['Message'] = "Success.";
    }

    /*
     Name:           deleteMatch
     Description:    Use to delete Match
     URL:            /Football/deleteMatch/
    */
    public function deleteMatch_post() {
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete Match Data */
        $this->Football_model->deleteMatch($this->MatchID);
        $this->Return['Message'] = "Match deleted successfully.";
    }

    /*
     Name:           assignPlayers
     Description:    Use to Assign Match Players
     URL:            /Football/assignPlayers/
    */
    public function assignPlayers_post() {
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('MatchPlayers', 'MatchPlayers', 'trim');
        if (!empty($this->Post['MatchPlayers']) && is_array($this->Post['MatchPlayers'])) {
            foreach ($this->Post['MatchPlayers'] as $Key => $Value) {
                $this->form_validation->set_rules('MatchPlayers[' . $Key . '][TeamGUIDLocal]', 'TeamGUIDLocal', 'trim|required|callback_validateEntityGUID[Teams,TeamIDLocal]');
                $this->form_validation->set_rules('MatchPlayers[' . $Key . '][LocalPlayerGUID]', 'LocalPlayerGUID', 'trim|required|callback_validateEntityGUID[Players,LocalPlayerID]');
                $this->form_validation->set_rules('MatchPlayers[' . $Key . '][LocalPlayerPosition]', 'LocalPlayerPosition', 'trim|required|in_list[Goalkeeper,Defender,Midfielder,Forward]');
                $this->form_validation->set_rules('MatchPlayers[' . $Key . '][TeamGUIDVisitor]', 'TeamGUIDVisitor', 'trim|required|callback_validateEntityGUID[Teams,TeamIDVisitor]');
                $this->form_validation->set_rules('MatchPlayers[' . $Key . '][VisitorPlayerGUID]', 'VisitorPlayerGUID', 'trim|required|callback_validateEntityGUID[Players,VisitorPlayerID]');
                $this->form_validation->set_rules('MatchPlayers[' . $Key . '][VisitorPlayerPosition]', 'VisitorPlayerPosition', 'trim|required|in_list[Goalkeeper,Defender,Midfielder,Forward]');
            }
        } else {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Match Players Required.";
            exit;
        }
        $this->form_validation->validation($this);  /* Run validation */

        $TeamPlayers = array();
        foreach ($this->Post['MatchPlayers'] as $Key => $Value) {
            $Row = array(
                        'TeamID'     => $this->Entity_model->getEntity('E.EntityID', array('EntityGUID' => $this->Post('MatchPlayers')[$Key]['TeamGUIDLocal'], 'EntityTypeName' => "Teams"))['EntityID'],
                        'PlayerID'   => $this->Entity_model->getEntity('E.EntityID', array('EntityGUID' => $this->Post('MatchPlayers')[$Key]['LocalPlayerGUID'], 'EntityTypeName' => "Players"))['EntityID'],
                        'PlayerRole' => $this->Post('MatchPlayers')[$Key]['LocalPlayerPosition'] 
                    );
            array_push($TeamPlayers, $Row);
            $Row = array(
                        'TeamID'     => $this->Entity_model->getEntity('E.EntityID', array('EntityGUID' => $this->Post('MatchPlayers')[$Key]['TeamGUIDVisitor'], 'EntityTypeName' => "Teams"))['EntityID'],
                        'PlayerID'   => $this->Entity_model->getEntity('E.EntityID', array('EntityGUID' => $this->Post('MatchPlayers')[$Key]['VisitorPlayerGUID'], 'EntityTypeName' => "Players"))['EntityID'],
                        'PlayerRole' => $this->Post('MatchPlayers')[$Key]['VisitorPlayerPosition'] 
                    );
            array_push($TeamPlayers, $Row);
        }
        if(empty($TeamPlayers)){
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "Match Players Required.";
            exit;
        }

        /* Set Match Players Data */
        $this->Football_model->assignPlayers(array('TeamPlayers' => $TeamPlayers, 'MatchID' => $this->MatchID, 'LeagueID' => $this->LeagueID));
        $this->Return['Message'] = "Match players assigned successfully.";
    }

    /*
    Name:           addPlayer
    Description:    Use to add Player info.
    URL:            /Football/addPlayer/
    */
    public function addPlayer_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('PlayerName', 'PlayerName', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $PlayerID = $this->Football_model->addPlayer($this->Post);
        if ($PlayerID) {

            /* Add Player Image */
            if (!empty($this->Post['MediaGUIDs'])) {
                $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
                foreach ($MediaGUIDsArray as $MediaGUID) {
                    $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                    if($MediaID){
                        $this->Football_model->updatePlayerDetails($PlayerID,array('PlayerPic' => @$MediaID['MediaName']));
                    }
                }
            }
            $this->Return['Message'] = "Player Details added successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']      = "Something went wrong, please try again later!";
        }
    }

    /*
      Name:         getPlayers
      Description:  Use to get Players list.
      URL:          /admin/football/getPlayers
    */

    public function getPlayers_post() {
        /* Validation section */
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('PlayerGUID', 'PlayerGUID', 'trim|callback_validateEntityGUID[Players,PlayerID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $PlayersData = $this->Football_model->getPlayers((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'TeamID' => @$this->TeamID,'LeagueID' => @$this->LeagueID, 'PlayerID' => @$this->PlayerID, 'MatchID' => @$this->MatchID)), (!empty($this->PlayerID) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($PlayersData) {
            $this->Return['Data'] = (!empty($this->PlayerID) ? $PlayersData : $PlayersData['Data']);
        }
    }

    /*
    Name:           updatePlayer
    Description:    Use to update Player info.
    URL:            /Football/updatePlayer/
    */
    public function updatePlayer_post()
    {
        /* Validation section */
        $this->form_validation->set_rules('PlayerGUID', 'PlayerGUID', 'trim|required|callback_validateEntityGUID[Players,PlayerID]');
        $this->form_validation->set_rules('PlayerName', 'PlayerName', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if (!empty($this->Post['MediaGUIDs'])) {
            $MediaGUIDsArray = explode(",", $this->Post['MediaGUIDs']);
            foreach ($MediaGUIDsArray as $MediaGUID) {
                $MediaID = $this->Media_model->getMedia('MediaID,MediaName', array('MediaGUID' => $MediaGUID));
                if ($MediaID) {
                    $this->Media_model->addMediaToEntity($MediaID['MediaID'], $this->SessionUserID, $this->PlayerID);
                }
            }
        }

        if ($this->Football_model->updatePlayerDetails($this->PlayerID,array_merge($this->Post,array('PlayerPic' => @$MediaID['MediaName'])))) {
            $this->Return['Data']    = $this->Football_model->getPlayers('PlayerPic,PlayerName',array('PlayerID' => $this->PlayerID));
            $this->Return['Message'] = "Player Details updated successfully";
        }else{
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message']        = "Something went wrong, please try again later!";
        }
    }

    /*
     Name:           deletePlayer
     Description:    Use to delete Player
     URL:            /Football/deletePlayer/
    */
    public function deletePlayer_post() {
        $this->form_validation->set_rules('PlayerGUID', 'PlayerGUID', 'trim|required|callback_validateEntityGUID[Players,PlayerID]');
        $this->form_validation->validation($this);  /* Run validation */

        /* Delete Player Data */
        $this->Football_model->deletePlayer($this->PlayerID);
        $this->Return['Message'] = "Player deleted successfully.";
    }



}