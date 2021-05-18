<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
    }

    /*
      Description: 	Use to get Get Attributes.
      URL: 			/api/category/getAttributes
      Input (Sample JSON):
     */

    public function getAttributes_post() {
        /* Validation section */
        $this->form_validation->set_rules('CategoryGUID', 'CategoryGUID', 'trim|required|callback_validateEntityGUID[Category,CategoryID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $AttributesData = $this->Category_model->getAttributes('
			E.EntityGUID AttributeGUID,
			A.AttributeName,
			A.AttributeValues
			', array("EntityID" => $this->CategoryID), TRUE, 1, 25);
        if (!empty($AttributesData)) {
            $this->Return['Data'] = $AttributesData['Data'];
        }
    }

    /*
      Description: 	Use to get Get single category.
      URL: 			/api/category/getCategories
      Input (Sample JSON):
     */

    public function getCategories_post() {
        /* Validation section */
        $this->form_validation->set_rules('CategoryTypeName', 'CategoryTypeName', 'trim|callback_validateCategoryTypeName');
        $this->form_validation->set_rules('StoreGUID', 'StoreGUID', 'trim|callback_validateEntityGUID[Store,StoreID]');

        $this->form_validation->set_rules('ParentCategoryGUID', 'ParentCategoryGUID', 'trim|callback_validateEntityGUID[Category,ParentCategoryID]');

        $this->form_validation->set_rules('PageNo', 'PageNo', 'trim|integer');
        $this->form_validation->set_rules('PageSize', 'PageSize', 'trim|integer');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $CategoryData = $this->Category_model->getCategories('MenuOrder,SubCategoryNames', array("CategoryTypeID" => @$this->CategoryTypeID, "ParentCategoryID" => @$this->ParentCategoryID, "StoreID" => @$this->StoreID, "ShowOnlyParent" => (!empty($this->ParentCategoryID) ? false : true)), TRUE, @$this->Post['PageNo'], @$this->Post['PageSize']);
        if (!empty($CategoryData)) {
            $this->Return['Data'] = $CategoryData['Data'];
        }
    }

    /*
      Description: 	Use to get Get single category.
      URL: 			/api/category/getCategory
      Input (Sample JSON):
     */

    public function getCategory_post() {
        /* Validation section */
        $this->form_validation->set_rules('CategoryGUID', 'CategoryGUID', 'trim|required|callback_validateEntityGUID[Category,CategoryID]');
        $this->form_validation->validation($this);  /* Run validation */
        /* Validation - ends */

        $CategoryData = $this->Category_model->getCategories('', array("CategoryTypeID" => @$this->CategoryTypeID, "CategoryID" => @$this->CategoryID));
        if (!empty($CategoryData)) {
            $this->Return['Data'] = $CategoryData;
        }
    }

}
