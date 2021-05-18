<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller_Secure {
    /* ------------------------------ */
    /* ------------------------------ */

    public function index() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/js/football/reports.js',
            'asset/plugins/select2/js/select2.js',
        );

        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/match_reports');
        $this->load->view('includes/footer');
    }

    public function account() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/account_reports');
        $this->load->view('includes/footer');
    }

    public function userAnalysis() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/user_analysis_reports');
        $this->load->view('includes/footer');
    }

    public function contestAnalysis() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/contest_analysis_reports');
        $this->load->view('includes/footer');
    }

     public function privateContestAnalysis() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/private_contest_analysis_reports');
        $this->load->view('includes/footer');
    }

    public function userRegistrationReports() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css',
            'asset/plugins/charts/Chart.min.css',
        );
        $load['js'] = array(
            //'asset/plugins/charts/loader-charts.js',
            'asset/plugins/charts/Chart.min.js',
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/user_registration_reports');
        $this->load->view('includes/footer');
    }

    public function userJoinedReports() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css',
            'asset/plugins/charts/Chart.min.css',
        );
        $load['js'] = array(
            //'asset/plugins/charts/loader-charts.js',
            'asset/plugins/charts/Chart.min.js',
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/user_joined_fee_reports');
        $this->load->view('includes/footer');
    }

    public function userPlanningLifetime() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css',
            'asset/plugins/charts/Chart.min.css',
        );
        $load['js'] = array(
            //'asset/plugins/charts/loader-charts.js',
            'asset/plugins/charts/Chart.min.js',
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/user_planning_lifetime_reports');
        $this->load->view('includes/footer');
    }

    public function football_match() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football_match.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/js/' . $this->ModuleData['ModuleName'] . '.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/football_match_reports');
        $this->load->view('includes/footer');
    }

    public function matchContestAnalysis() {
        $load['css'] = array(
            'asset/plugins/chosen/chosen.min.css',
            'asset/plugins/select2/css/select2.css'
        );
        $load['js'] = array(
            'asset/js/football/reports.js',
            'asset/plugins/chosen/chosen.jquery.min.js',
            'asset/plugins/jquery.form.js',
            'asset/plugins/select2/js/select2.js',
        );
        $this->load->view('includes/header', $load);
        $this->load->view('includes/menu');
        $this->load->view('football/reports/match_contest_analysis');
        $this->load->view('includes/footer');
    }

}
