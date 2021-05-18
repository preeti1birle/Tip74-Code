<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class State extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('State_model');
    }

    /*
      Description: 	Use to get Get Attributes.
      URL: 			/api/state/getState
      Input (Sample JSON):
     */

    public function getState_post() {
        $StateData = $this->State_model->getState(TRUE, 1, 50);
        if (!empty($StateData)) {
            $this->Return['Data'] = $StateData['Data'];
        }
    }

    public function getStateByName_post() {

        $this->form_validation->set_rules('StateName', 'StateName', 'trim|required');
        $this->form_validation->validation($this);  /* Run validation */
        $StateData = $this->State_model->getStateByName($this->Post['StateName']);
        if (!empty($StateData)) {
            $this->Return['Data'] = $StateData;
        }
    }

    public function editStateByName_post() {

        $this->form_validation->set_rules('StateName', 'StateName', 'trim|required');
        $this->form_validation->set_rules('Status', 'Status', 'trim|required');

        $this->form_validation->validation($this);  /* Run validation */
        $StateData = $this->State_model->editState($this->Post['StateName'],$this->Post['Status']);
        if (!empty($StateData)) {
            $this->Return['Data'] = $StateData;
        }
    }
}