<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Page extends Admin_Controller_Secure {
	
	/*------------------------------*/
	/*------------------------------*/	
	public function index()
	{

		$Data = array();
		// if(!empty($_GET['page'])){
		// 	$JSON = json_encode(array(
		// 		"PageGUID" 		=> $_GET['page']
		// 	));
		// 	$Response = APICall(API_URL.'admin/page/getPage', $JSON); /* call API and get response */
		// 	if($Response['ResponseCode'] == 200){ /*check for admin type user*/
		// 		$Data['Data'] = $Response['Data'];
		// 	}
		// }


		$load['css']=array(
			'asset/plugins/chosen/chosen.min.css'
		);
		$load['js']=array(
			'asset/js/'.$this->ModuleData['ModuleName'].'.js',
			'asset/plugins/chosen/chosen.jquery.min.js',
			'asset/plugins/jquery.form.js',
			'asset/js/tinymce.min.js'
		);	

		$this->load->view('includes/header',$load);
		$this->load->view('includes/menu');
		$this->load->view('page/pages', $Data);
		$this->load->view('includes/footer');
	}



}
