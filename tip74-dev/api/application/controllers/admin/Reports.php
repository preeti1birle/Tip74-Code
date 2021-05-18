<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends API_Controller_Secure {

    function __construct() {
        parent::__construct();
        $this->load->model('Contest_model');
        $this->load->model('Sports_model');
    }

    /*
      Description: To get contest winning users
     */

    public function getMatchWiseReports_post() {
        $this->form_validation->set_rules('SeriesGUID', 'SeriesGUID', 'required|trim|callback_validateEntityGUID[Series,SeriesID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'required|trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getMatchWiseReports(@$this->SeriesID, @$this->MatchID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getAccountReport_post() {
        $this->form_validation->set_rules('SeriesGUID', 'SeriesGUID', 'trim|callback_validateEntityGUID[Series,SeriesID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getAccountReport($this->Post, @$this->SeriesID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getMatchContestAnalysis_post() {
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getMatchContestAnalysis($this->MatchID);
        $this->Return['Data']['MatchDetails'] = array();
        if (!empty($this->MatchID)) {
            $this->db->select('M.MatchID,S.SeriesName,CONCAT(TL.TeamName," Vs ",TV.TeamName) as MatchName,DATE_FORMAT(CONVERT_TZ(M.MatchStartDateTime,"+00:00","' . DEFAULT_TIMEZONE . '"), "' . DATE_FORMAT . '") MatchStartDateTime');
            $this->db->from('sports_matches M,tbl_entity E,sports_series S,sports_teams TL, sports_teams TV');
            $this->db->where("E.EntityID", "M.MatchID", FALSE);
            $this->db->where("S.SeriesID", "M.SeriesID", FALSE);
            $this->db->where("M.TeamIDLocal", "TL.TeamID", FALSE);
            $this->db->where("M.TeamIDVisitor", "TV.TeamID", FALSE);
            $this->db->where("M.MatchID", $this->MatchID);
            $this->db->where("E.StatusID", 5);
            $this->db->limit(1);
            $Query = $this->db->get();
            if ($Query->num_rows() > 0) {
                $WinningUsersData['MatchDetailsAll'] = $Query->row_array();
            }
        }
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

        /*
      Description: To get Account CSV Report
     */

    public function getMatchContestAnalysisExports_post() {
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|required|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getMatchContestAnalysis($this->MatchID);
        //dump($WinningUsersData);
        if (!empty($WinningUsersData)) {
            $n=1;
            foreach ($WinningUsersData as $Value) {
                if($this->Post['FilterType'] == $Value['visible']){
                    $DataArr[] = array(
                            's_no'      => $n++,
                            'Contest'   => $Value['ContestDetails']['ContestType'].' - '.$Value['ContestDetails']['ContestName'],
                            'TotalDepositCollection' => $Value['TotalDepositCollection'],
                            'TotalCashBonusCollection'   => $Value['TotalCashBonusCollection'],
                            'TotalRealUserWinningCollection'  => $Value['TotalRealUserWinningCollection'],
                            'TotalRealUserJoined'     => $Value['TotalRealUserJoined'],
                            'TotalVirtualUserJoined' => $Value['TotalVirtualUserJoined'],
                            'NetProfit'    => $Value['NetProfit'],
                            'Profit'      => $Value['Profit'],
                            'Loss'      => $Value['loss']
                        );
                }elseif($this->Post['FilterType'] == 'All'){
                    $DataArr[] = array(
                            's_no'      => $n++,
                            'Contest'   => $Value['ContestDetails']['ContestType'].' - '.$Value['ContestDetails']['ContestName'],
                            'TotalDepositCollection' => $Value['TotalDepositCollection'],
                            'TotalCashBonusCollection'   => $Value['TotalCashBonusCollection'],
                            'TotalRealUserWinningCollection'  => $Value['TotalRealUserWinningCollection'],
                            'TotalRealUserJoined'     => $Value['TotalRealUserJoined'],
                            'TotalVirtualUserJoined' => $Value['TotalVirtualUserJoined'],
                            'NetProfit'    => $Value['NetProfit'],
                            'Profit'      => $Value['Profit'],
                            'Loss'      => $Value['loss']
                    );
                }   
            }
            $filename = 'ContestAccountReport-'.date('Y-m-d');
            $fp = fopen('uploads/reports/'.$filename.'.csv', 'w');

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename.'.csv');
            fputcsv($fp, array('S.no', 'Contest', 'Total Deposit Collection', 'TotalCash Bonus Collection', 'Winning', 'Total Real User Joined','Total Virtual User Joined', 'Net Profit', 'Profit','loss'));

            foreach ($DataArr as $row) {
                fputcsv($fp, $row);
            }

            $this->Return['ResponseCode'] = 200;
            $this->Return['Message'] = "Successfully Exported";
            $this->Return['Data'] = BASE_URL . 'uploads/reports/'.$filename.'.csv';            
        }
    }

    /*
      Description: To get Account CSV Report
     */

    public function getAccountReportExport_post() {
        $this->form_validation->set_rules('SeriesGUID', 'SeriesGUID', 'trim|callback_validateEntityGUID[Series,SeriesID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $SeriesData = $this->db->query("SELECT SeriesName FROM sports_series WHERE SeriesID = ".$this->SeriesID);

        $WinningUsersData = $this->Contest_model->getAccountReport($this->Post, @$this->SeriesID);
        if (!empty($WinningUsersData['Matches'])) {
            $n=1;
            foreach ($WinningUsersData['Matches'] as $Value) {
                $DataArr[] = array(
                            's_no'      => $n++,
                            'MatchDate' => date('d-m-Y H:i:s',strtotime($Value['MatchDetails']['MatchStartDateTime'])),
                            'MatchName' => $Value['MatchDetails']['MatchName'],
                            'Deposit'   => $Value['TotalDepositCollection'],
                            'Winnings'  => $Value['TotalRealUserWinningCollection'],
                            'Bonus'     => $Value['TotalCashBonusCollection'],
                            'NetProfit' => $Value['NetProfit'],
                            'Profit'    => $Value['Profit'],
                            'Loss'      => $Value['loss']
                        );
            }
            $NameDate=date('d-m-Y');
            if(!empty($this->Post['FromDate']) && !empty($this->Post['ToDate'])){
                $NameDate=date('d-m-Y',strtotime($this->Post['FromDate'])). '-' .date('d-m-Y',strtotime($this->Post['ToDate']));
            }

            $filename = 'AccountReport-'.$SeriesData->row()->SeriesName;
            $fp = fopen('uploads/reports/'.$filename.'.csv', 'w');

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename='.$filename.'.csv');
            fputcsv($fp, array('S.no', 'Match Date', 'Match Name', 'Deposit', 'Winnings', 'Bonus','Net Profit', 'Profit', 'Loss'));

            foreach ($DataArr as $row) {
                fputcsv($fp, $row);
            }

            $this->Return['ResponseCode'] = 200;
            $this->Return['Message'] = "Successfully Exported";
            $this->Return['Data'] = BASE_URL . 'uploads/reports/'.$filename.'.csv';            
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getUserAnalysisReport_post() {
        $this->form_validation->set_rules('UserType', 'User Type', 'trim|required');
        $this->form_validation->set_rules('DataFilter', 'Date Filter', 'trim|required');
        $this->form_validation->set_rules('FromDate', 'FromDate', 'trim');
        $this->form_validation->set_rules('ToDate', 'ToDate', 'trim');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getUserAnalysisReport($this->Post);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getContestName_post() {
        $this->form_validation->set_rules('ContestType', 'ContestType', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        // print_r($this->Post);exit;
        $this->Return['Data'] = $this->Contest_model->getContestName($this->Post['ContestType']);
    }

    public function getContestPrivateName_post() {
        $this->form_validation->set_rules('ContestType', 'ContestType', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        // print_r($this->Post);exit;
        $this->Return['Data'] = $this->Contest_model->getContestPrivateName($this->Post['ContestType']);
    }
    /*
      Description: To get contest winning users
     */

    public function getContestAnalysisReport_post() {
        $this->form_validation->set_rules('ContestType', 'ContestType', 'trim|required');
        $this->form_validation->set_rules('ContestName', 'ContestName', 'trim|required');
        $this->form_validation->set_rules('SeriesGUID', 'SeriesGUID', 'trim|callback_validateEntityGUID[Series,SeriesID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('FromDate', 'FromDate', 'trim');
        $this->form_validation->set_rules('ToDate', 'ToDate', 'trim');

        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getContestAnalysisReport($this->Post, @$this->SeriesID, @$this->MatchID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }


    /*
      Description: To get contest winning users
     */

    public function getPrivateContestAnalysisReport_post() {
        $this->form_validation->set_rules('ContestType', 'ContestType', 'trim|required');
        $this->form_validation->set_rules('ContestName', 'ContestName', 'trim|required');
        $this->form_validation->set_rules('SeriesGUID', 'SeriesGUID', 'trim|callback_validateEntityGUID[Series,SeriesID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->set_rules('FromDate', 'FromDate', 'trim');
        $this->form_validation->set_rules('ToDate', 'ToDate', 'trim');

        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getPrivateContestAnalysisReport($this->Post, @$this->SeriesID, @$this->MatchID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getUserRegisterReport_post() {
        $this->form_validation->set_rules('DataFilter', 'Date Filter', 'trim|required');
        $this->form_validation->set_rules('FromDate', 'FromDate', 'trim');
        $this->form_validation->set_rules('ToDate', 'ToDate', 'trim');

        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getUserRegisterReport($this->Post, @$this->SeriesID, @$this->MatchID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getUserJoinedFeeReport_post() {
        $this->form_validation->set_rules('DataFilter', 'Date Filter', 'trim|required');
        $this->form_validation->set_rules('EntryFeeRange', 'Entry Fee Range', 'trim|required');
        $this->form_validation->set_rules('FromDate', 'FromDate', 'trim');
        $this->form_validation->set_rules('ToDate', 'ToDate', 'trim');

        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getUserJoinedFeeReport($this->Post, @$this->SeriesID, @$this->MatchID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getUserPlanningLifetimeReport_post() {
        $this->form_validation->set_rules('DataFilter', 'Date Filter', 'trim|required');
        $this->form_validation->set_rules('FromDate', 'FromDate', 'trim');
        $this->form_validation->set_rules('ToDate', 'ToDate', 'trim');

        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $WinningUsersData = $this->Contest_model->getUserPlanningLifetimeReport($this->Post, @$this->SeriesID, @$this->MatchID);
        if (!empty($WinningUsersData)) {
            $this->Return['Data'] = $WinningUsersData;
        }
    }

    /*
      Description: To get contest winning users
     */

    public function getPrivateContestDataReport_post() {
        $this->form_validation->set_rules('SeriesGUID', 'SeriesGUID', 'trim|callback_validateEntityGUID[Series,SeriesID]');
        $this->form_validation->set_rules('MatchGUID', 'MatchGUID', 'trim|callback_validateEntityGUID[Matches,MatchID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Get Contests Winning Users Data */
        $privateContestData = $this->Contest_model->getPrivateContestDataReport($this->Post, @$this->SeriesID, @$this->MatchID);
        if (!empty($privateContestData)) {
            $this->Return['Data'] = $privateContestData;
        }
    }

}

?>