<?php
class Privatepanel_UserController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelEmail = new Application_Model_Email();
		$this->modelStatic = new Application_Model_Static();
		$this->view->pageIcon = "fa  fa-users";
    }
 	
	
	
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Clients";
		$this->view->pageDescription = "manage all site clients ";
		$this->view->request_type = "all_client";
		 
 	}
 	
	
	public function verifiedAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Clients";
		$this->view->pageDescription = "manage all site clients ";
		$this->view->request_type = "verified";
 		$this->render("index");
 	}
 	
	
	
	public function blockedAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Clients";
		$this->view->pageDescription = "manage all site clients ";
		$this->view->request_type = "blocked";
		$this->render("index");
		 
 	}
 	
   	public function serviceprovidersAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Service Providers";
		$this->view->pageDescription = "manage all service providers";
		$this->view->request_type = "all_sp";
		$this->render("index");
		 
 	}
	
	public function verifiedserviceprovidersAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Service Providers";
		$this->view->pageDescription = "manage all service providers";
		$this->view->request_type = "v_sp";
		$this->render("index");
		 
 	}
	public function blockedserviceprovidersAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Service Providers";
		$this->view->pageDescription = "manage all service providers";
		$this->view->request_type = "b_sp";
		$this->render("index");
		 
 	}
	
	
	
// get CITY	
	public function getusercity(){
		
		$where = "zip_id='".$user_information['user_city']."'";
		//$where = '';
		$extra=array('fields'=>'user_city');	
		$joinArr = array(
					'0'=>array("zips","zip_id=user_city","left",array("city")),
					);
		$getcity = $this->modelStatic->Super_Get("users",$where,"fetch",$extra,$joinArr);
				//prd($getcity);
		$this->view->city = $getcity;
		
	}	
// get CITY end	
	
	
	
	/*
	 * User Account Information 
	 */
	public function accountAction(){
		global $mySession; 
		$user_id =  $this->_getParam("user_id");
		$this->view->user_id = $user_id;
		
		$user_information = $this->modelUser->find($user_id);
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("privatepanel");
		}
		
		
		$user_information = $user_information->current()->toArray();
			//	prd($user_information);

		$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']." Profile ");
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		
	//	prd($user_information);
		
		$form = new Application_Form_User();
		$form->profile_front($user_information['user_id']);
		$form->populate($user_information);
		$form->user_city->setRegisterInArrayValidator(false);
		
		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
 
  			if($form->isValid($posted_data)){
				
				$data = $form->getValues();
				
				if($user_information['user_email']!=$posted_data['user_email']){
					$user_email_key = md5("ASDFUITYU"."!@#$%^$%&(*_+".time());
					$data['user_email_key'] = $user_email_key;
					$data['user_email_verified'] ='0';
				}

				
 				$is_update = $this->modelUser->add($data,$user_id);
				
				if(is_object($is_update) and $is_update->success){
					
					//prn($data);
					if($user_information['user_email']!=$posted_data['user_email']){
							$isSend = $this->modelEmail->sendEmail('email_verification',$data);
						//	prd($isSend);	
						}
					
					$mySession->successMsg = "User Information Successfully Updated";
					$this->_redirect("privatepanel/user/account/user_id/".$user_id);
				}
				$mySession->errorMsg = "Please Check Information Again";
			}
 		}
		
  		$this->view->form = $form ;	
		$this->view->user_information = $user_information ;
	}
	
	/* Send Verification Email */
	public function sendverificationAction(){
 		global $mySession;
		
		$modelEmail = new Application_Model_Email();
		
		$user_id =  $this->_getParam("user_id");
		
		$user_information = $this->modelUser->find($user_id);
		$user_information = $user_information->current()->toArray();
  		$data_form_values = $user_information;
		//prd($user_information);
   		if($user_information['user_email_verified']!="1"){
  			$user_email_key = md5("ASDFUITYU"."!@#$%^$%&(*_+".time());
			$data_to_update = array("user_email_verified"=>"0","user_email_key"=>$user_email_key);
			$this->modelUser->update($data_to_update,"user_id = '".$user_information['user_id']."'");
			$data_form_values['user_email_key'] = $user_email_key ;
			$issend = $modelEmail->sendEmail('email_verification',$data_form_values);
		//	prd($issend);
 			$mySession->successMsg = " Email Successfully Send to '".$user_information['user_first_name']."' email address.";
 		}else{
			$mySession->infoMsg = "'".$user_information['user_first_name']."' Email Address is already verified..";
		}
  		$this->_redirect("privatepanel/user/account/user_id/".$user_id);

	}
	
	public function checkemailAction(){
		
		$user_id =  $this->_getParam("user_id");
		//prd($user_id);
		$user_information = $this->modelUser->find($user_id);
		$user_information = $user_information->current()->toArray();
		
 		$email_address = strtolower($this->_getParam('user_email'));
		
		$exclude = strtolower($this->_getParam('exclude'));
		
		if(!empty($exclude)){
			 $user = $user_information;
			 $user_id = $user_id ;
		}
		$email = $this->modelUser->checkEmail($email_address,$user_id);
		
		//prd($email);
		
		$rev = $this->_getParam("rev");
		//prd($rev);
		if(!empty($rev)){
 			if($email)
				echo json_encode("true");
 			else
				echo json_encode("`$email_address` is not registered , please enter valid email address ");
 			exit();
 		}
		
 
		if($email)
			echo json_encode("`$email_address` already exists , please enter any other email address ");
		else
			echo json_encode("true");
		exit();
	}

	
	/*
	 * User Account Information 
	 */
	 
	 public function edituserinfoAction(){
		 		global $objSession ; 
			$this->_helper->layout->disableLayout();

		 	$user_id =  $this->_getParam("user_id");
			$where = "user_id = '".$user_id."'";
			$user_information = $this->modelStatic->Super_Get("users",$where,"fetch");
		//	prd($user_information);
			$form = new Application_Form_User();
			$form->registerpro($user_id );
			$form->populate($user_information);
			$this->view->form = $form ;	
		 }
	 
	public function imageAction(){
		
		global $mySession; 
		
		$user_id =  $this->_getParam("user_id");
		
		$user_information = $this->modelUser->find($user_id);
		
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("privatepanel");
		}
		
		$user_information = $user_information->current()->toArray();
		
		if($user_information['user_type']=="admin"){
 			$mySession->errorMsg = " Invalid Operation .";
			$this->_redirect("privatepanel/user");
 		}
		
		
		$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']."'s Profile ");
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		
		$form = new Application_Form_User();
		$form->image();
 		
		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
		
			if($form->isValid($data_post)){
				
 				$is_uploaded = $this->_handle_profile_image();
				
				if(is_object($is_uploaded) and $is_uploaded->success){

					if(empty($is_uploaded->media_path)){
						/* Not Image is Uploaded  */
						$objSession->defaultMsg = "No Images Selected ...";
						$this->_redirect("admin/privatepanel/image/user_id/".$user_id);
					}
					
 					$is_updated = $this->modelUser->add(array("user_image"=>$is_uploaded->media_path),$user_id);
					
					if(is_object($is_updated) and $is_updated->success){
						
						/* Remove Old User Images*/
						$this->_unlink_user_image($user_information['user_image']); 
						$objSession->successMsg = " Image Successfully Updated";
						$this->_redirect("privatepanel/user/image/user_id/".$user_id);
						
 					}
										
				}
 			}
		}
		
  		$this->view->form = $form ;	
		$this->view->user_information = $user_information ;
   	 
	}
	
	public function detailAction(){
		$user_id =  $this->_getParam("user_id");
		
		$user_information = $this->modelUser->find($user_id);
		
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("privatepanel");
		}
		
		$HireTime =$this->modelStatic->Super_Get("job","hired_provider_id='".$user_id."'","fetch",array('fields'=>'count("hired_provider_id") as totalHire'));
		$this->view->HireTime = $HireTime['totalHire'];
		
		
		$extraReview = array();
		$ReviewData=$this->modelStatic->Super_Get("reviews","review_sp='".$user_id."'","fetchAll",$extraReview);
		$this->view->ReviewData = $ReviewData;
		
		$TotalRate = '';
		foreach($ReviewData as $eachReview){
			$TotalRate += $eachReview['review_ratings'];
		}
		
		if(count($ReviewData)!=0){
			$AvegargeRate = $TotalRate/count($ReviewData);
		}else{
			$AvegargeRate = 0;
			}
		$this->view->AvegargeRate = $AvegargeRate;
		
		$extra= array();
		$join =  array(
					'0'=>array("services","service_id=us_service_sub_parent_id","left",array('service_name')),
					);
		$user_services=$this->modelStatic->Super_Get("user_services","us_user_id='".$user_id."'","fetchAll",$extra,$join);
		$this->view->user_services = $user_services;
		
		$user_information = $user_information->current()->toArray();
		$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']." Profile ");
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		$this->view->user_information=$user_information ;
		
				//echo "user_information";
		//prn($user_information);
// get user city
		$where = "zip_id='".$user_information['user_city']."'";
		//$where = '';
		$extra=array('fields'=>'user_city');	
		$joinArr = array(
					'0'=>array("zips","zip_id=user_city","left",array("city")),
					);
		$getcity = $this->modelStatic->Super_Get("users",$where,"fetch",$extra,$joinArr);
				//prd($getcity);
		$this->view->city = $getcity;

		
	}
	
	
	/*
	 * User Account Information 
	 */
	public function passwordAction(){
		
		global $mySession; 
		
		$user_id =  $this->_getParam("user_id");
		
		$user_information = $this->modelUser->find($user_id);
		
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("privatepanel");
		}
		
		$user_information = $user_information->current()->toArray();
		$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']."'s Profile ");
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		
		$form = new Application_Form_User();
		$form->resetPassword();
		$form->populate($user_information);
 		
		
		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
 
  			if($form->isValid($posted_data)){
				
				$data = $form->getValues();
				
				$data['user_password'] = md5($data['user_password']);
 				
 				$is_update = $this->modelUser->add($data,$user_id);
				
				if(is_object($is_update) and $is_update->success){
					
					$mySession->successMsg = "User Information Successfully Updated";
					$this->_redirect("privatepanel/user/account/user_id/".$user_id);
				}
				$mySession->errorMsg = "Please Check Information Again";
			}
 		}
		
  		$this->view->form = $form ;	
		$this->view->user_information = $user_information ;
		$this->render("account");
   	 
	}
 
 
 	/* Ajax Call For Get Users */
  	public function getusersAction(){
		
		$this->dbObj = Zend_Registry::get('db');
		
		$request_type = $this->_getParam('type');
 
 		$aColumns = array(
			'user_id',
			'user_type',
			'user_image',
			'user_first_name',
			'user_last_name',
			'user_email',
			 'user_email_verified',
			'user_status',
 			'user_salutation',
 			'user_last_name' , 
  		);

		$sIndexColumn = 'user_id';
		$sTable = 'users';
 		
		/* 
		 * Paging
		 */
		 
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) and $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR ";
				//$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) and $_GET['bSearchable_'.$i] == "true" and $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
		
		
		if($sWhere){
			$sWhere.=" and user_type!='admin'  ";
		}else{
			$sWhere.=" where user_type!='admin' ";
		}
		
		if($request_type!=""){
			
			switch($request_type){
				case 'all_client': $sWhere.=" and   user_type = 'client'"; break;	
				case 'all_sp': $sWhere.=" and   user_type = 'service_provider'"; break;	
				case 'verified': $sWhere.=" and   user_email_verified = '1' and user_type = 'client' "; break;	
				case 'blocked': $sWhere.=" and  user_status = '0' and user_type = 'client' "; break;
				case 'v_sp': $sWhere.=" and   user_email_verified = '1' and user_type = 'service_provider' "; break;	
				case 'b_sp': $sWhere.=" and  user_status = '0' and user_type = 'service_provider' "; break;
				default : break ;	
			}
		}
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)).",
		CONCAT(user_first_name,' ',user_last_name) as user_name
		FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal-1,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		foreach($qry as $row1){
			
 			$row=array();
			
			$row[] = $i;
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			$row[]='<img src="'.getUserImage($row1['user_image'],60).'" />';
			
			switch($row1['user_email_verified']){
				case '0':$verification_status ="<span class='badge badge-danger badge-roundless'>Unverified</span>";break;
				default :$verification_status ="<span class='badge badge-success badge-roundless'>Verified</span>";break;
			}
			
			
			$row[]=$row1['user_name']."<br />$verification_status";
   			$row[]=$row1['user_email'];

 			 
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			
			
			
			
			
			$row[] =  '<a href="'.APPLICATION_URL.'/privatepanel/user/account/user_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> Edit <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
			$row[] = '<a href="'.APPLICATION_URL.'/privatepanel/user/detail/user_id/'.$row1[$sIndexColumn].'" class="btn btn-xs green ">View <i class="fa fa-search"></i></a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	
	/* 
	 *	Remove Graphic Media 
	 */
 	public function removeAction(){
		
		global $mySession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData['users']) and count($formData['users'])){
				
				 foreach($formData['users'] as $key=>$values){
 
   					 $user_info = $this->modelUser->get($values);
					 
					 if(empty($user_info))
						continue ;
						
 					$this->_unlink_user_image($user_info['user_image']);
					
					$removed = $this->modelUser->delete("user_id IN ($values)");
					 
				 }
 
 				 
 				$mySession->successMsg = " User(s) Deleted Successfully ";
				
 			}else{
				$mySession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect($_SERVER['HTTP_REFERER']);	 
   	 
		} 
		
 			
 	}
	
	
	/* 
	 *	Delete graphic Media Images 
	 */
	private function _unlink_user_image($image){
		
		if(empty($image))
			return true; 
		
	 
  		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/".$image)){
			unlink(PROFILE_IMAGES_PATH."/".$image);
 		}
		
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/thumb/".$image)){
			unlink(PROFILE_IMAGES_PATH."/thumb/".$image);
 		}
		
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/60/".$image)){
			unlink(PROFILE_IMAGES_PATH."/60/".$image);
 		}
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/160/".$image)){
			unlink(PROFILE_IMAGES_PATH."/160/".$image);
 		}
		
		
		
	}
	

	
	/* Handle The Uploaded Images For Graphic Media  */
	private function _handle_profile_image(){
		
 		global $objSession; 
		
		$uploaded_image_names = array();
	 
		$adapter = new Zend_File_Transfer_Adapter_Http();
	
		$files = $adapter->getFileInfo();
  		 
		$uploaded_image_names = array();
		
		$new_name = false; 
		 
  		/*prd($adapter);*/
		foreach ($files as $file => $info) { /* Begin Foreach for handle multiple images */
		
  			$name_old = $adapter->getFileName($file);
			
			if(empty($name_old)){
				continue ;			
			}
			
			$file_title  = $adapter->getFileInfo($file);
			
			$file_title = $file_title[$file]['name']; 
				
  			$uploaded_image_extension = getFileExtension($name_old);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$file_title);
			
			$file_title = formatImageName($file_title);
  
 			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
  			$adapter->addFilter('Rename',array('target' => PROFILE_IMAGES_PATH."/".$new_name));
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
				$thumb_config = array("source_path"=>PROFILE_IMAGES_PATH,"name"=> $new_name);
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
			
  			//$uploaded_image_names[]=array('media_path'=>$new_name); => For Multiple Images
   		
		}/* End Foreach Loop for all images */
		
		
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 		
   	 
 	}
	
// get subscriber users details
		public function subscriberAction(){
			global $mySession; 
			$this->view->pageHeading = "Manage All Subscriber";
			$this->view->pageDescription = "manage all site Subscribers ";
			$this->view->request_type = "all_client";
			 
		}

		public function getsubscriberAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('coming_user_id','newsletter_email');
		$sIndexColumn = 'coming_user_id';
		$sTable = 'comingsoon';
 		
		/* 
		 * Paging
		 */
		 
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) and $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR ";
				//$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) and $_GET['bSearchable_'.$i] == "true" and $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = " WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
		
		
		if($sWhere== ""){
			$sWhere ="where 1";
		}else{
			$sWhere.=" AND  ";
		}
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal-1,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=1;
	
		foreach($qry as $row1){
			
 			$row=array();
			
			$row[] = $j;
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			$row[]=$row1['newsletter_email'];
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}	
	
// remove subscriber	
		public function  removesubscriberAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['comingsoon']) && count($formData['comingsoon'])) {
			$comingsoon = implode(",",$formData['comingsoon']) ;
  			$removed = $this->modelUser->getAdapter()->delete('comingsoon',"coming_user_id IN (".$comingsoon.")");
 			$mySession->successMsg="Subscriber User Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/user/subscriber");
 	} 
	 
	
	 
}