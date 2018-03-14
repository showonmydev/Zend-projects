<?php
class ProfileController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
		$this->pluginImage = new Application_Plugin_Image();
		$this->modelEmail = new Application_Model_Email();
		
   	}
	
	public function homeAction(){	
	/*$distanceQuery = "(((ACOS(SIN(".$filter_lat." * PI() / 180) * SIN(user_lat * PI() / 180) + COS(".$filter_lat." * PI() / 180) * COS(user_lat * PI() / 180) * COS((".$filter_long."-user_long) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344))";
				// for KM -> 60  1.1515  1.609344, for MILES -> 60 * 1.1515*/
	
	}
 	public function indexAction(){	
 		global $objSession ; 
		
   		$content = $this->modelStatic->getPage(40); 
		$this->view->btn=1;
		
		$form = new Application_Form_User();
		
		$this->view->show = "front_profile" ; 
		
		$form->profile_front($this->view->user->user_id);
		
		$form->populate((array)$this->view->user);
		//prd((array)$this->view->user);
		
 		$form->user_city->setRegisterInArrayValidator(false);
		
		if($this->view->user->user_id!=''){
			$form->user_first_name->setAttrib('readonly','readonly');
			$form->user_last_name->setAttrib('readonly','readonly');
		}
		
		if($this->getRequest()->isPost()){

		$data_post = $this->getRequest()->getPost();
	//prd($data_post);
		if($form->isValid($data_post)){
			
			$data_to_update = $form->getValues() ;
			
			if($this->view->user->user_email!=$data_post['user_email']){
					$user_email_key = md5("ASDFUITYU"."!@#$%^$%&(*_+".time());
					$data_to_update['user_email_key'] = $user_email_key;
					$data_to_update['user_email_verified'] ='0';
				}
			
 			$is_update  = $this->modelUser->add($data_to_update , $this->view->user->user_id);
			
			if(is_object($is_update) and $is_update->success){
				if($is_update->row_affected > 0){
					
					if($this->view->user->user_email!=$data_post['user_email']){
							$data_post['user_email_key']=$data_to_update['user_email_key'];
							$isSend = $this->modelEmail->sendEmail('email_verification',$data_post);
					}
					
					$objSession->successMsg = " Profile Information Changed Successfully ";
				}else{
					$objSession->infoMsg = " New Information is Same as previous one ";
				}
				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_profile");
			}
			
			$objSession->errorMsg  = $is_update->message; ;
			
		}else{
			$objSession->errorMsg = "Please Check Information Again ...!";	
		}
	}
		
		$this->view->form = $form;
		
		$this->view->content = $content ;
	}
	
	public function accountsettingsAction(){}
	
	public function accountpageAction(){}
	
	public function mainprofileAction(){
			global $objSession ; 
			$user_id =$this->view->user->user_id;
			
			$user_details=$this->modelStatic->Super_Get("users","user_id='".$user_id."'","fetch");
			$this->view->user_details = $user_details;
			
			
			$extra= array();
			$join =  array(
						'0'=>array("services","service_id=us_service_sub_parent_id","left",array('service_name')),
						);
			$user_services=$this->modelStatic->Super_Get("user_services","us_user_id='".$user_id."'","fetchAll",$extra,$join);
			$this->view->user_services = $user_services;
			
			
			$client_location=$this->modelStatic->Super_Get("zips","zip_id='".$user_details['user_city']."'","fetch");
			$this->view->client_location = $client_location;
			
			$extra1= array(
							'group'=>'job_id',
						);
			$join1 =  array(
						'0'=>array("services","service_id=job_cat_id","left",array('service_name')),
						'1'=>array("proposal","p_job_id=job_id","left",array('totalProposal'=> new Zend_Db_Expr('ifnull(count(DISTINCT(proposal_id)),0)'))),
						);
			$client_projects=$this->modelStatic->Super_Get("job","job_posted_by='".$user_id."'","fetchAll",$extra1,$join1);
			$this->view->client_projects = $client_projects;
			
			$extraReview = array();
			$joinReview = array(
						'0'=>array("users","user_id=review_client","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
			);
			$ReviewData=$this->modelStatic->Super_Get("reviews","review_sp='".$user_id."'","fetchAll",$extraReview,$joinReview);
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

			
			$extraRWork= array(
							'fields'=>array('p_job_id')
					   );			   
			$joinRWork = array(
				'0'=>array("job","job_id=p_job_id","left",array('job_status','job_cat_id','job_complete_on')),
				'1'=>array("services","service_id=job_cat_id","left",array('service_name')),
				);
			$ProviderRecentWork=$this->modelStatic->Super_Get("proposal","proposal.sender_id='".$this->view->user->user_id."' && proposal_decline_status='3'","fetchAll",$extraRWork,$joinRWork);
			$this->view->ProviderRecentWork = $ProviderRecentWork;
			
			
			
		}
		
			
	// delete account 
	public function deleteaccountAction(){
		
		global $objSession;
		$user_id=$_REQUEST['user_id'];
		//	prd($user_id);
		$where = "user_id='".$user_id."'";
		$deleteAccount = $this->modelStatic->Super_Delete("users" , $where);
		//prd($deleteAccount);
		if(is_object($deleteAccount)){
			 if($deleteAccount->success){
				$objSession->successMsg ="Account has baan deleted";
				$this->_redirect("/index/");
			 }
			 if($deleteAccount->error){
				 if(isset($deleteAccount->exception)){/* Genrate Message related to the current Exception  */
				 }
				 $objSession->errorMsg = $deleteAccount->message;							 
			 }
		}else{
			$objSession->errorMsg = " Please Check Information again ";
		}
	}
	
		


	public function pagination($searchDataQuery,$page,$record_per_page)
	{
		$adapter = new Zend_Paginator_Adapter_DbSelect($searchDataQuery);
		$paginator = new Zend_Paginator($adapter);
		$page =$page;
		$this->view->page=$page;
		$rec_counts = $this->_getParam('itemcountpage');
		if(!$rec_counts)
		{
			if(isset($record_per_page))
			$rec_counts =$record_per_page;
			else
			$rec_counts =10;
		}
		$paginator->setItemCountPerPage($rec_counts);
		$paginator->setCurrentPageNumber($page);
		$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'pagination-control.phtml');
		$this->view->paginationControl=$paginationControl;
		return $paginator;
	}
	
	
	
	
 	public function imageAction(){
		
		//prd('bnvb');
		
		global $objSession ;
		$this->view->show = "front_image";
		
		 /* Form For Update Profile Image  */
 		$form =  new Application_Form_User();
		$form->image();
		
		
		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
			if($form->isValid($data_post) && $_FILES['user_image']['name']!=''){
 				$is_uploaded = $this->_handle_profile_image();
		
				if(is_object($is_uploaded) and $is_uploaded->success!='' and $is_uploaded->error!=1){
	
					if(empty($is_uploaded->media_path)){
						
						$objSession->defaultMsg = "No Images Selected ...";
						$this->_helper->getHelper("Redirector")->gotoRoute(array(),'front_image');
					}
				
						/* Not Image is Uploaded  */
								$filename = PROFILE_IMAGES_PATH.'/'.$is_uploaded->media_path;
							//echo $is_uploaded->media_path['add_photo']; die;
								$exif = exif_read_data($filename);
								$newName =$is_uploaded->media_path; 
								if(isset($exif['Orientation']) && !empty($exif['Orientation'])) 
								{
									
					$image = imagecreatefromjpeg($filename);
					//echo $exif['Orientation']; die;
					switch ($exif['Orientation']){
						case 3:
							$image = imagerotate($image, 180, 0);
							break;
			
						case 6:
							$image = imagerotate($image, -90, 0);
							break;
			
						case 8:
							$image = imagerotate($image, 90, 0);
							break;
					}
					$newName = time().$is_uploaded->media_path; 
					imagejpeg($image,PROFILE_IMAGES_PATH.'/'.$newName);
					
						$thumb_config = array("source_path"=>PROFILE_IMAGES_PATH,"name"=> $newName);
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
			
			
					
					unlink($filename);
					unlink(REVEAL_IMAGES_PATH.'/300/'.$is_uploaded->media_path['add_photo']);
					unlink(REVEAL_IMAGES_PATH.'/60/'.$is_uploaded->media_path['add_photo']);
					unlink(REVEAL_IMAGES_PATH.'/160/'.$is_uploaded->media_path['add_photo']);
				}
				
					
					
					$is_updated = $this->modelUser->add(array("user_image"=>$newName),$this->view->user->user_id);
				
					if(is_object($is_updated) and $is_updated->success){
						
						/* Remove Old User Images*/
						$this->_remove_image(); 
						$objSession->successMsg = " Image Successfully Updated";
						$this->_helper->getHelper("Redirector")->gotoRoute(array(),'front_image');
						
 					}
										
				}
				else
					{	
						$objSession->errorMsg = $is_uploaded->message;
						$this->redirect("change-avatar");
					}
 			}
			
			else
			{
				$objSession->infoMsg = " New Information is Same as previous one ";
			}
		}
		
		
		$this->view->form = $form ;
		
	}

	
	/* Method to Crop User Images  */
	public function cropimageAction(){

		global $objSession;
		
   		$this->view->pageHeading = "Crop Image";
		$this->view->pageDescription="";

 		$path=$this->_getParam('path');
		

		if(empty($path)){
			$path = $this->view->user->user_image ;
		}
				
		$this->view->path = $path;
 		
		$filePath = PROFILE_IMAGES_PATH."/".$path;
		
		$imgdata = getimagesize($filePath);
		
		$this->view->imageWidth =  $imgdata[0];
		$this->view->imageHeight =  $imgdata[1];

		
		/* Code for Copping Image */
		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
			
			$uploaded_image_extension = getFileExtension($path);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$path);
						
			$file_title = formatImageName($file_title);
			
			/* retrive name */
			$_temp = explode("-",$file_title);
			
			array_pop($_temp);array_pop($_temp);
			$file_title = implode("-",$_temp);
			
  			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
   			$crop_image = array(
				"source_directory" => PROFILE_IMAGES_PATH,
				"name"=>$path,
				"target_name"=>$new_name,
 				'_w'=>$posted_data['w'],
				'_h'=>$posted_data['h'],
				'_x'=>$posted_data['x'],
				'_y'=>$posted_data['y'],
				'destination'=>array(
					"60"=>array("size"=>60),
					"160"=>array("size"=>160),
					"thumb"=>array("size"=>300)
				)
 			);
			
 			$is_crop = $this->pluginImage->universal_crop_image($crop_image);
			
			if($is_crop->success){
				
 				/* Update Name into the database and Replace the prev uploaded news to new names */	
				$this->pluginImage->simple_rename($path,$new_name,array('directory'=>PROFILE_IMAGES_PATH));	
				
				$this->pluginImage->universal_unlink($path,array('directory'=>PROFILE_IMAGES_PATH));	
				
				$is_updated = $this->modelUser->add(array("user_image"=>$new_name),$this->view->user->user_id);
 
   				$objSession->successMsg = $is_crop->message;
			}else{
				$objSession->errorMsg = $is_crop->message;
			}
 			
			$this->_redirect('change-avatar');
		}
		
	}
	
 	
	/* Crop Image  */
	private function _crop_image($param = array()){
 			
			$targ_w = isset($param['width'])?$param['width']:160;
			$targ_h = isset($param['height'])?$param['height']:160;
 			$jpeg_quality = isset($param['quality'])?$param['quality']:100;
 			$src = isset($param['source'])?$param['source']: "";
			$destination = isset($param['destination'])?$param['destination']: "";
			
			$name = isset($param['name'])?$param['name']: "";
			
 
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			
			 
			list($imagewidth, $imageheight, $imageType) = getimagesize($src."/".$name);
			
			$imageType = image_type_to_mime_type($imageType);
			
			
			$uploaded_image_extension = getFileExtension($name);
 	
			$src = $src.'/'.$name;
			
			switch($imageType) {
				case "image/gif":$source=imagecreatefromgif($src);break;

				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					$source=imagecreatefromjpeg($src); 
				break;

				case "image/png":
				case "image/x-png":
					$source=imagecreatefrompng($src); 
				break;
			}
			
			imagecopyresampled($dst_r,$source,0,0,$param['_x'],$param['_y'],$targ_w,$targ_h,$param['_w'],$param['_h']);

			switch($imageType) {
				case "image/gif":
					imagegif($dst_r, $destination."/".$name); 
				break;
				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					imagejpeg($dst_r, $destination."/".$name,$jpeg_quality); 
				break;
				case "image/png":
				case "image/x-png":
					imagepng($dst_r, $destination."/".$name); 
					imagepng($dst_r, $destination."/".$name);  
				break;
				}
	 		
			return true; 
	}
	


	 /* Remove / Unlink Old Profile Image  
 	 */	 
 	private function _remove_image(){
		
		$image_name = $this->view->user->user_image;
		
		if(file_exists(PROFILE_IMAGES_PATH."/".$image_name)){
			unlink(PROFILE_IMAGES_PATH."/".$image_name);
		}
		
		if(file_exists(PROFILE_IMAGES_PATH."/thumb/".$image_name)){
			unlink(PROFILE_IMAGES_PATH."/thumb/".$image_name);
		}
		 
 		if(file_exists(PROFILE_IMAGES_PATH."/60/".$image_name)){
			unlink(PROFILE_IMAGES_PATH."/60/".$image_name);
		}
		if(PROFILE_IMAGES_PATH."/160/".$image_name){
			unlink(PROFILE_IMAGES_PATH."/160/".$image_name);
		}
		
		return true ;
		
	}
	
	
 	
	
	/* Handle The Uploaded Images For Graphic Media  */
	private function _handle_profile_image(){
		
 		global $objSession; 
		
		$uploaded_image_names = array();
	 
		$adapter = new Zend_File_Transfer_Adapter_Http();
	
		$files = $adapter->getFileInfo();
  			$size=$files['user_image']['size'];
			if($size>4000000)
			{
				
				return (object)array("success"=>false,'error'=>true,"message"=>"Maximum upload file size is 5 MB. Please upload valid size of image","media_path"=>$new_name) ;
			}
			else if($size<100000/4)
			{
					return (object)array("success"=>false,'error'=>true,"message"=>"Minimum upload file size is 25kb. Please upload valid size of image","media_path"=>$new_name) ;	
			}
			
		$uploaded_image_names = array();
		
		$new_name = false; 
		 
  		/*prd($adapter);*/
		foreach ($files as $file => $info) { /* Begin Foreach for handle multiple images */
		
  			$name_old = $adapter->getFileName($file);
		
			$size=filesize($files);
			
		
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
	 	
	
	
	public function getfulladdressAction(){
		
		$address_string = $this->_getParam('address_string');
			
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
 
 		$getGeometry=json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address_string).'&sensor=true'));
					
		$address_specification = array();  /*  For Address Specification Array */
		
	
		if(isset($getGeometry->results[0])) {
				
			/* Country City and State */
			foreach($getGeometry->results[0]->address_components as $addressComponet) {
				
				if(in_array('sublocality', $addressComponet->types)) {
					$address_specification['sublocality'] = ($addressComponet->long_name); 
				}
				
				if(in_array('locality', $addressComponet->types)) {
					$address_specification['locality'] = ($addressComponet->long_name); 
				}
				if(in_array('administrative_area_level_2', $addressComponet->types)) {
					$address_specification['city'] = ($addressComponet->long_name); 
				}
				if(in_array('administrative_area_level_1', $addressComponet->types)) {
					$address_specification['state'] = ($addressComponet->long_name); 
				}
				if(in_array('country', $addressComponet->types)) {
					$address_specification['country'] = ($addressComponet->long_name); 
				}
			}
		}else{
			$address_specification['sublocality'] = ""; 
			$address_specification['locality'] = ""; 
			$address_specification['city'] = ""; 
			$address_specification['state'] = "";
			$address_specification['country'] = ""; 
			
		}
 			
		echo json_encode($address_specification);
		exit;
		//prd($address_string);
		
		
		
		
		
		
	}
	

    public function passwordAction(){
		
		global $objSession; 
		
		if($this->view->user->user_login_type!="normal"){
			$objSession->warningMsg = "You cannot access this feature with this login type";
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),'front_profile');
		}
		//prd($this->view->user);
    		
		$this->view->pageHeading = "Change Password";
 		$this->view->pageDescription = "you can change your account password here ";
 
 		$this->view->show = "change_password";
  
		/* Change Password Form */
		$form =  new Application_Form_User();
		$form->changepassword();
  
   		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
			
			if($form->isValid($data_post)){
				
				$data_to_update = $form->getValues();
				//prd($data_to_update);
				if(md5($data_to_update['user_password'])==$this->view->user->user_password)
				{
					$objSession->infoMsg = "New password is same as previous one. please enter any other password";
					$this->_redirect("change-password");
				}
				$data_to_update['user_password'] = md5($data_to_update['user_password']);
				
   				$is_update = $this->modelUser->add($data_to_update,$this->view->user->user_id);
  			
				if(is_object($is_update) and $is_update->success){
					$objSession->successMsg = " Password Changed Successfully ";
					$this->_redirect("change-password");
 				}else{
					$objSession->errorMsg  = $is_update->message; ;
				}
			}else{
				$objSession->errorMsg = "Please Check Information Again ...!";	
			}
  		}
		
		$this->view->form = $form;
		$this->render("index");
	}
	
	 function _handle_uploaded_image($path)
	{
		
		
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
 			
 			$adapter->addFilter('Rename',array('target' => $path."/".$new_name));
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
			if($path!='CV_IMAGES_PATH'){
				$thumb_config = array("source_path"=>$path,"name"=> $new_name);
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$path."/100","size"=>100)));
			}
			
				 
 			//$uploaded_image_names[]=array('media_path'=>$new_name); => For Multiple Images
   		
		}/* End Foreach Loop for all images */
		
		
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 		
   	 
 	}
 	
}