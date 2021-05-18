<?php
class MY_Form_validation extends CI_Form_validation {

    function __construct($rules = array()) {
        parent::__construct($rules);
        
    }
    /*------------------------------*/
    /*------------------------------*/  
    public function getFirstErrorString() {
        foreach ($this->_error_array as $Key => $Value)
        {
            return $Value;
        }
    }
    

    /*------------------------------*/
    /*------------------------------*/  
    public function validation($Obj) {
        if ($Obj->form_validation->run() == FALSE)
        {
            $Obj->Return['ResponseCode'] = ($Obj->Return['ResponseCode'] != 200) ? $Obj->Return['ResponseCode'] : 500;
            $Obj->Return['Message']      = $this->getFirstErrorString(); 
            exit;
        }
    }



}
