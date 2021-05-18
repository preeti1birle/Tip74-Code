<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Football extends API_Controller_Secure
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Football_model');
        $this->load->model('Entries_model');
	}

    /*
      Name:         getSeasons
      Description:  Use to get Season list.
      URL:          /football/getSeasons
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
            $this->Return['Data'] = (!empty($this->Post['SeasonID'])) ? $SeasonsData :  $SeasonsData['Data'];
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
      Name: 		getLeagues
      Description: 	Use to get Leagues list.
      URL: 			/football/getLeagues
    */
    public function getLeagues_post() {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('CompetitionGUID', 'CompetitionGUID', 'trim|callback_validateEntityGUID[Competitions,CompetitionID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $LeaguesData = $this->Football_model->getLeagues((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'LeagueID' => @$this->LeagueID,'CompetitionID' => @$this->CompetitionID)), (!empty($this->LeagueID)) ? FALSE : TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($LeaguesData) {
            $this->Return['Data'] = (!empty($this->LeagueID)) ? $LeaguesData :  $LeaguesData['Data'];
        }
    }

    /*
      Name:         getLeagueRounds
      Description:  Use to get league rounds list.
      URL:          /football/getLeagueRounds
    */

    public function getLeagueRounds_post() {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('RoundGUID', 'RoundGUID', 'trim|callback_validateEntityGUID[Rounds,RoundID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $RoundsData = $this->Football_model->getRounds((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'LeagueID' => @$this->LeagueID, 'RoundID' => @$this->RoundID)), (!empty($this->RoundID) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($RoundsData) {
            $this->Return['Data'] = (!empty($this->RoundID)) ? $RoundsData :  $RoundsData['Data'];
        }
    }

    /*
      Name:         getTeams
      Description:  Use to get teams list.
      URL:          /football/getTeams
    */
    public function getTeams_post() {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('TeamGUID', 'TeamGUID', 'trim|callback_validateEntityGUID[Teams,TeamID]');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $TeamsData = $this->Football_model->getTeams((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("StatusID" => @$this->StatusID, 'LeagueID' => @$this->LeagueID, 'TeamID' => @$this->TeamID)), (!empty($this->TeamID) ? FALSE : TRUE), @$this->Post['PageNo'], @$this->Post['PageSize']);
        if ($TeamsData) {
            $this->Return['Data'] = (!empty($this->TeamID)) ? $TeamsData :  $TeamsData['Data'];
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
        $this->form_validation->set_rules('TimeZone', 'TimeZone', 'trim'); // +09:00
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $MatchesData = $this->Football_model->getMatches((!empty($this->Post['Params']) ? $this->Post['Params'] : ''),array_merge($this->Post,array('LeagueID' => @$this->LeagueID,'MatchID' => @$this->MatchID,'StatusID'=> @$this->StatusID,'WeekID' => @$this->WeekID,'TeamID' => @$this->TeamID,'SessionUserID' => @$this->SessionUserID)), (!empty($this->MatchID) ? FALSE : TRUE),@$this->Post['PageNo'],@$this->Post['PageSize']);
        if ($MatchesData) {
            $this->Return['Data'] = (!empty($this->MatchID) ? $MatchesData : $MatchesData['Data']);
        }
    }

    /*
      Name:         getTeamsHistoricResults
      Description:  Use to get teams historic results.
      URL:          /football/getTeamsHistoricResults
    */
    public function getTeamsHistoricResults_post() {
        /* Validation section */
        $this->form_validation->set_rules('TeamGUIDLocal', 'TeamGUIDLocal', 'trim|required|callback_validateEntityGUID[Teams,TeamIDLocal]');
        $this->form_validation->set_rules('TeamGUIDVisitor', 'TeamGUIDVisitor', 'trim|required|callback_validateEntityGUID[Teams,TeamIDVisitor]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Return['Data']['TeamDetailsLocal']   = $this->Football_model->getTeams('TeamColor', array_merge($this->Post, array('TeamID' => $this->TeamIDLocal)), FALSE);
        $this->Return['Data']['TeamDetailsVisitor'] = $this->Football_model->getTeams('TeamColor', array_merge($this->Post, array('TeamID' => $this->TeamIDVisitor)), FALSE);
        $this->Return['Data']['Matches'] = array();
        $this->Return['Data']['DrawMatches'] = $this->Return['Data']['TeamDetailsLocal']['WonMatches'] = $this->Return['Data']['TeamDetailsVisitor']['WonMatches'] = 0;
        $this->Return['Data']['TeamDetailsLocal']['HomeMatches'] = $this->Return['Data']['TeamDetailsLocal']['AwayMatches'] = 0;
        $this->Return['Data']['TeamDetailsVisitor']['HomeMatches'] = $this->Return['Data']['TeamDetailsVisitor']['AwayMatches'] = 0;
        $MatchesData = $this->Football_model->getMatches('TeamNameLocal,TeamNameVisitor,TeamColorLocal,TeamColorVisitor,MatchDate,MatchTime,MatchScoreDetails', array_merge($this->Post, array('Filters' => array('BothTeamMatches'), 'TeamIDL' => $this->TeamIDLocal,'TeamIDV' => $this->TeamIDVisitor, 'StatusID' => 5, 'OrderBy' => 'MatchStartDateTime', 'Sequence' => 'DESC')), TRUE, 1, 10);
        if($MatchesData){
            $this->Return['Data']['Matches'] = $MatchesData['Data'];
            foreach($MatchesData['Data']['Records'] as $Value){
                if($Value['MatchScoreDetails']->WinnerTeam == 'Local'){
                    $this->Return['Data']['TeamDetailsLocal']['WonMatches']++;
                }
                if($Value['MatchScoreDetails']->WinnerTeam == 'Visitor'){
                    $this->Return['Data']['TeamDetailsVisitor']['WonMatches']++;
                }
                if($Value['MatchScoreDetails']->WinnerTeam == 'Draw'){
                    $this->Return['Data']['DrawMatches']++;
                }
                if($Value['TeamNameLocal'] == $this->Return['Data']['TeamDetailsVisitor']['TeamName']){
                    $this->Return['Data']['TeamDetailsVisitor']['HomeMatches']++;
                    $this->Return['Data']['TeamDetailsLocal']['AwayMatches']++;
                }

                if($Value['TeamNameLocal'] == $this->Return['Data']['TeamDetailsLocal']['TeamName']){
                    $this->Return['Data']['TeamDetailsLocal']['HomeMatches']++;
                    $this->Return['Data']['TeamDetailsVisitor']['AwayMatches']++;
                }
            }
        }
    }

    /*
      Name:         getTeamLineup
      Description:  Use to get match teams lineup players.
      URL:          /football/getTeamLineup
    */
    public function getTeamLineup_post() {
        /* Validation section */
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $this->Return['Data']['Goalkeepers'] = $this->Return['Data']['Defenders'] = $this->Return['Data']['Midfielders'] = $this->Return['Data']['Forwards'] = array();

        /* Get Goal Keepers */
        $Goalkeepers = $this->Football_model->getPlayers('PlayerName,PlayerRole,TeamGUID', array_merge($this->Post, array('MatchID' => $this->MatchID,'PlayerRole' => 'Goalkeeper')), TRUE);
        if($Goalkeepers){
            $this->Return['Data']['Goalkeepers'] = $Goalkeepers['Data']['Records'];
        }

        /* Get Defenders */
        $Defenders = $this->Football_model->getPlayers('PlayerName,PlayerRole,TeamGUID', array_merge($this->Post, array('MatchID' => $this->MatchID,'PlayerRole' => 'Defender')), TRUE);
        if($Defenders){
            $this->Return['Data']['Defenders'] = $Defenders['Data']['Records'];
        }

        /* Get Midfielders */
        $Midfielders = $this->Football_model->getPlayers('PlayerName,PlayerRole,TeamGUID', array_merge($this->Post, array('MatchID' => $this->MatchID,'PlayerRole' => 'Midfielder')), TRUE);
        if($Midfielders){
            $this->Return['Data']['Midfielders'] = $Midfielders['Data']['Records'];
        }

        /* Get Forwards */
        $Forwards = $this->Football_model->getPlayers('PlayerName,PlayerRole,TeamGUID', array_merge($this->Post, array('MatchID' => $this->MatchID,'PlayerRole' => 'Forward')), TRUE);
        if($Forwards){
            $this->Return['Data']['Forwards'] = $Forwards['Data']['Records'];
        }
    }



    /*
      Name:         matchPrediction (Save & Lock)
      Description:  Use to do match prediction.
      URL:          /football/matchPrediction
    */

    public function matchPrediction_post() {

        /* Validation section */
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]|callback_validateMatchPrediction');
        // $this->form_validation->set_rules('PredictionStatus', 'PredictionStatus', 'trim|required|in_list[Save,Lock]');
        $this->form_validation->set_rules('TeamScoreLocalFT', 'Home Team Score Full Time', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[12]');
        $this->form_validation->set_rules('TeamScoreVisitorFT', 'Away Team Score Full Time', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[12]');
        $this->form_validation->set_rules('TeamScoreLocalHT', 'Home Team Score Half Time', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[12]');
        $this->form_validation->set_rules('TeamScoreVisitorHT', 'Away Team Score Half Time', 'trim|required|greater_than_equal_to[0]|less_than_equal_to[12]');
        $this->form_validation->set_rules('IsDoubleUps', 'IsDoubleUps', 'trim|required|in_list[Yes,No]');
        //$this->form_validation->set_rules('MatchPredictionID', 'Match Prediction ID', 'trim'.(($this->Post['IsDoubleUps'] == 'Yes' || $this->Post['PredictionStatus'] == 'Lock') ? '|required' : ''));

        $this->form_validation->set_message('greater_than_equal_to', 'The {field} field value should be greater than or equals to {param}.');
        $this->form_validation->set_message('less_than_equal_to', 'The {field} field value should be less than or equals to {param}.');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        if($this->Post['IsAutoPurchaseDoubleUps'] == 'Yes'){
            $PurchaseData = $this->Entries_model->purchaseDoubleUps(array("WeekID" => $this->WeekID, "NoOfDoubleUps" => 1, "TotalRequiredAmount" => $this->Post['TotalRequiredAmount'], 'GameEntryID' => $this->Post['GameEntryID']), $this->SessionUserID);
            if(!$PurchaseData) {
                $this->Return['ResponseCode'] = 500;
                $this->Return['Message'] = "An error occurred, please try again later.";
                exit;
            }
        }

        $Prediction = $this->Football_model->matchPrediction(array_merge($this->Post,array('MatchID' => $this->MatchID,'WeekID' => $this->WeekID)),$this->SessionUserID);
        if (!$Prediction) {
            $this->Return['ResponseCode'] = 500;
            $this->Return['Message'] = "An error occurred, please try again later.";
        } else {
            $this->Return['Data'] = $this->Football_model->getMatches('PredictionDetails,FullTimePredictionStatics,,HalfTimePredictionStatics',array('MatchID' => $this->MatchID,'SessionUserID' => $this->SessionUserID));
            $this->Return['Message'] = ($this->Post['PredictionStatus'] == 'Lock') ? $Prediction : 'Success';
            if($this->Post['PredictionStatus'] == 'Lock'){
                $this->Return['Balance'] = $this->Entries_model->getUserEntriesBalance($this->SessionUserID, $this->WeekID);
            }
        }
    }

    /*
      Name:         getPredictions
      Description:  Use to get match prediction.
      URL:          /football/getPredictions
     */

    public function getPredictions_post() {
        /* Validation section */
        $this->form_validation->set_rules('UserGUID', 'UserGUID', 'trim|callback_validateEntityGUID[User,UserID]');
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('Status', 'Status', 'trim|callback_validateStatus');
        $this->form_validation->set_rules('PredictionStatus', 'PredictionStatus', 'trim|in_list[Lock,Save]');
        $this->form_validation->set_rules('GameEntryID', 'GameEntryID', 'trim');
        $this->form_validation->set_rules('Keyword', 'Search Keyword', 'trim');
        $this->form_validation->set_rules('OrderBy', 'OrderBy', 'trim');
        $this->form_validation->set_rules('Sequence', 'Sequence', 'trim|in_list[ASC,DESC]');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer|required');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $PredictionsData = $this->Football_model->getPredictions((!empty($this->Post['Params']) ? $this->Post['Params'] : ''), array_merge($this->Post, array("LeagueID" => @$this->LeagueID, "MatchID" => @$this->MatchID, "WeekID" => @$this->WeekID, 'UserID' => @$this->UserID, 'SessionUserID' => $this->SessionUserID, 'StatusID' => @$this->StatusID)), TRUE ,$this->Post['PageNo'], $this->Post['PageSize']);
        if ($PredictionsData) {
            $this->Return['Data'] = $PredictionsData['Data'];
        }
    }

    /*
      Name:         getLeaderBoardMatchWise
      Description:  Use to get match wise leaderboard list.
      URL:          /football/getLeaderBoardMatchWise
     */

    public function getLeaderBoardMatchWise_post() {
        /* Validation section */
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer|required');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $LeaderBoardData = $this->Football_model->getLeaderBoardMatchWise(array_merge($this->Post, array("MatchID" => $this->MatchID)), (int) $this->Post['PageNo'], (int) $this->Post['PageSize']);
        if ($LeaderBoardData) {
            $this->Return['Data'] = $LeaderBoardData['Data'];
        }
    }

    /*
      Name:         getLeaderBoardLeagueWeekWise
      Description:  Use to get league week wise leaderboard list.
      URL:          /football/getLeaderBoardLeagueWeekWise
     */

    public function getLeaderBoardLeagueWeekWise_post() {
        /* Validation section */
        // $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('WeekGUID', 'WeekGUID', 'trim|required|callback_validateEntityGUID[Weeks,WeekID]');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer|required');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $LeaderBoardData = $this->Football_model->getLeaderBoardLeagueWeekWise(array_merge($this->Post, array('WeekID' => $this->WeekID)), (int) $this->Post['PageNo'], (int) $this->Post['PageSize']);
        if ($LeaderBoardData) {
            $this->Return['Data'] = $LeaderBoardData['Data'];
        }
    }

    /*
      Name:         getLeaderBoardLeagueSeasonWise
      Description:  Use to get league season wise leaderboard list.
      URL:          /football/getLeaderBoardLeagueSeasonWise
     */

    public function getLeaderBoardLeagueSeasonWise_post() {
        /* Validation section */
        $this->form_validation->set_rules('LeagueGUID', 'LeagueGUID', 'trim|required|callback_validateEntityGUID[Leagues,LeagueID]');
        $this->form_validation->set_rules('SeasonID', 'SeasonID', 'trim|integer|required');
        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer|required');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer|required');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $LeaderBoardData = $this->Football_model->getLeaderBoardLeagueSeasonWise(array_merge($this->Post, array("LeagueID" => $this->LeagueID)), (int) $this->Post['PageNo'], (int) $this->Post['PageSize']);
        if ($LeaderBoardData) {
            $this->Return['Data'] = $LeaderBoardData['Data'];
        }
    }

    /**
     * Function Name: validateMatchPrediction
     * Description:   To validate entries ID
     */
    public function validateMatchPrediction($MatchGUID) {
        /* To Check Match Status */
        $MatchDetails = $this->Football_model->getMatches('Status,MatchStartDateTime,TeamNameLocal,TeamNameVisitor',array('MatchID' => $this->MatchID));
        if($MatchDetails['Status'] != 'Pending'){
            $this->form_validation->set_message('validateMatchPrediction', 'You can predict only upcoming fixtures.');
            return FALSE;
        }

        $this->Post['TeamNameLocal']   = $MatchDetails['TeamNameLocal'];
        $this->Post['TeamNameVisitor'] = $MatchDetails['TeamNameVisitor'];
        $this->Post['IsAutoPurchaseDoubleUps'] = 'No';

         /* To Check Week Status */
         $WeekDetails = $this->Football_model->getWeeks('Status, WeekCount',array('WeekID' => $this->WeekID));
         $this->Post['WeekCount'] = $WeekDetails['WeekCount'];
         // if($WeekDetails['Status'] != 'Running'){
         //     $this->form_validation->set_message('validateMatchPrediction', 'You can predict only for current week.');
         //     return FALSE;
         // }

        /* To Check Team Local Score */
        if(isset($this->Post['TeamScoreLocalFT']) && isset($this->Post['TeamScoreLocalHT']) && ($this->Post['TeamScoreLocalHT'] > $this->Post['TeamScoreLocalFT'])){
            $this->form_validation->set_message('validateMatchPrediction', 'Home Team full time prediction should be greater than or equals to half time prediction.');
            return FALSE;
        }

        /* To Check Team Visitor Score */
        if(isset($this->Post['TeamScoreVisitorFT']) && isset($this->Post['TeamScoreVisitorHT']) && ($this->Post['TeamScoreVisitorHT'] > $this->Post['TeamScoreVisitorFT'])){
            $this->form_validation->set_message('validateMatchPrediction', 'Away Team full time prediction should be greater than or equals to half time prediction.');
            return FALSE;
        }

        /* To Check Match Prediction Close Limit */
        $PredictionCloseLimit = $this->db->query('SELECT `ConfigTypeValue` FROM `set_site_config` WHERE `ConfigTypeGUID` = "FixturePredictionCloseLimit" LIMIT 1')->row()->ConfigTypeValue;
        if($PredictionCloseLimit > 0){
            if ((strtotime($MatchDetails['MatchStartDateTime']) - ($PredictionCloseLimit * 60)) <= strtotime(date('Y-m-d H:i:s'))) {
                $this->form_validation->set_message('validateMatchPrediction', 'Fixture prediction already closed at '.$PredictionCloseLimit.' minutes ago, before starting match.');
                return FALSE;
            }
        }

        /* To Check Match Prediction Status */
        $this->Post['IsExist'] = $this->Post['IsDoubleUsed'] = 'No';
        $MatchPrediction = $this->db->query('SELECT PredictionStatus,IsDoubleUps FROM football_sports_matches_prediction WHERE MatchID = '.$this->MatchID.' AND UserID = '.$this->SessionUserID.(!empty($this->Post['MatchPredictionID']) ? ' AND MatchPredictionID ='.$this->Post['MatchPredictionID'] : '').' LIMIT 1');
        if($MatchPrediction->num_rows() > 0){
            // checing no of prediction user have predicted
            if(!empty($this->Post['MatchPredictionID'])){
                $this->Post['IsExist'] = 'Yes';
                // changes are here 
                if($MatchPrediction->row()->PredictionStatus == 'Lock'){
                    $this->form_validation->set_message('validateMatchPrediction', 'Fixture prediction is already locked.');
                    return FALSE;
                }
            }
            // if($MatchPrediction->row()->PredictionCount >= 5){
            //     $this->form_validation->set_message('validateMatchPrediction', 'Already Predicted for this match.');
            //     return FALSE;
            // }
        }

        /* To Check Entries Prediction & Double Ups Balance */
        $EntreisData = $this->db->query('SELECT GameEntryID,(AllowedPredictions - ConsumedPredictions) RemainingPredictions, (AllowedPurchaseDoubleUps - ConsumeDoubleUps) RemainingDoubleUps, (AllowedPurchaseDoubleUps-TotalPurchasedDoubleUps) RemainingPurchasedDoubleUps FROM tbl_users_game_entries WHERE UserID = '.$this->SessionUserID.' AND WeekID = '.$this->WeekID.' ORDER BY EntryNo DESC LIMIT 1');
        /*echo "yes";
        echo $this->db->last_query();
        print_r($EntreisData->row());*/
        if($EntreisData->num_rows() <= 0){
            $this->Return['ResponseCode'] = 402;
            $this->Return['Data'] = array('BalanceType' => 'Prediction');
            $this->form_validation->set_message('validateMatchPrediction', 'Insufficient prediction balance.');
            return FALSE;
        }
        
        if($this->Post['IsExist'] == 'No'){
            if($EntreisData->row()->RemainingPredictions <= 0){
                $this->Return['ResponseCode'] = 402;
                $this->Return['Data'] = array('BalanceType' => 'Prediction');
                $this->form_validation->set_message('validateMatchPrediction', 'Insufficient prediction balance.');
                return FALSE;
            }
            $this->Post['GameEntryID'] = $EntreisData->row()->GameEntryID;
            if($this->Post['IsDoubleUps'] == 'Yes') {
                if($EntreisData->row()->RemainingDoubleUps > 0){
                    $UserWalletAmount = $this->db->query('SELECT WalletAmount FROM tbl_users WHERE UserID = '.$this->SessionUserID.' LIMIT 1')->row()->WalletAmount;
                    if($UserWalletAmount > 0 && $EntreisData->row()->RemainingPurchasedDoubleUps > 0){
                        $this->Post['IsAutoPurchaseDoubleUps'] = 'Yes';

                        /* To Get Per Double Price */
                        $PerDoubleUpPrice = $this->db->query('SELECT `ConfigTypeValue` FROM `set_site_config` WHERE `ConfigTypeGUID` = "PerDoubleUpPrice" LIMIT 1')->row()->ConfigTypeValue;
                        $this->Post['TotalRequiredAmount'] = 1 * $PerDoubleUpPrice;
                    }else{
                        $this->Return['ResponseCode'] = 402;
                        $this->Return['Data'] = array('BalanceType' => 'DoubleUps');
                        $this->form_validation->set_message('validateMatchPrediction', 'Please add money in your wallet to purchase Double Ups.');
                        return FALSE;
                    }
                } else {
                    $this->Return['ResponseCode'] = 402;
                    $this->Return['Data'] = array('BalanceType' => 'Prediction');
                    $this->form_validation->set_message('validateMatchPrediction', 'You have excceded double up limit!');
                    return FALSE;
                }
            }
        }

        /* To Check Double Ups for Existing Prediction */
        if($this->Post['IsExist'] == 'Yes'){
            // if($MatchPrediction->row()->IsDoubleUps == 'Yes' && $this->Post['IsDoubleUps'] == 'No'){
            //     $this->form_validation->set_message('validateMatchPrediction', 'Double ups already applied, You can not change now.');
            //     return FALSE;
            // }
            $this->Post['GameEntryID'] = $EntreisData->row()->GameEntryID;
            if($MatchPrediction->row()->IsDoubleUps == 'No' && $this->Post['IsDoubleUps'] == 'Yes'){
                if($EntreisData->row()->RemainingDoubleUps > 0){
                    $UserWalletAmount = $this->db->query('SELECT WalletAmount FROM tbl_users WHERE UserID = '.$this->SessionUserID.' LIMIT 1')->row()->WalletAmount;
                    if($UserWalletAmount > 0 && $EntreisData->row()->RemainingPurchasedDoubleUps > 0){
                        $this->Post['IsAutoPurchaseDoubleUps'] = 'Yes';

                        /* To Get Per Double Price */
                        $PerDoubleUpPrice = $this->db->query('SELECT `ConfigTypeValue` FROM `set_site_config` WHERE `ConfigTypeGUID` = "PerDoubleUpPrice" LIMIT 1')->row()->ConfigTypeValue;
                        $this->Post['TotalRequiredAmount'] = 1 * $PerDoubleUpPrice;
                    }else{
                        $this->Return['ResponseCode'] = 402;
                        $this->Return['Data'] = array('BalanceType' => 'DoubleUps');
                        $this->form_validation->set_message('validateMatchPrediction', 'Please add money in your wallet to purchase Double Ups.');
                        return FALSE;
                    }
                    
                } else {
                    $this->Return['ResponseCode'] = 402;
                    $this->Return['Data'] = array('BalanceType' => 'DoubleUps');
                    $this->form_validation->set_message('validateMatchPrediction', 'You have excceded double up limit!');
                    return FALSE;
                }
                $this->Post['IsDoubleUsed'] = 'Yes';
            }
        }

        return TRUE;
    }

}