<?php
class UserController extends Zend_Controller_Action
{
  	private $modelUser ,$modelContent; 
	 
	public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelStatic = new Application_Model_Static();
 	}


	public function indexAction(){
 		$this->_redirect('user/login');
 	}
	
	 public function ajaxdataAction(){
		//$post_id = $this->_params('post_id');
		$post_id = $this->_getParam('post_id');
		$zip_code_data = $this->modelStatic->Super_Get('zips',"zip_id='".$post_id."'",'fetch');
		print_r(json_encode($zip_code_data));exit();
	}
	 
	 
 	public function loginAction(){
 		
		global $objSession; 
 		$this->view->pageHeading = "LOGIN";
		$if_job_post = $this->_getParam('if_job_post');
		$pjfcID = $this->_getParam('pjfcID');
		//prd($pjfcID);
	//	$isblogpage = $this->getRequest()->getParam('blogg');
	//	prd($isblogpage);
		
		$auth = Zend_Auth::getInstance(); 
		$this->view->user_type='user';
		$this->view->show="login";
		if ($auth->hasIdentity()){
            $objSession->infoMsg ='It seems you are already logged into the system ';
            $this->_redirect('user-account');
        }
		
 		$form = new Application_Form_User();
		$form->login_front();
 		
		/*If You login by the form*/
		if ($this->getRequest()->isPost()){ // Post Form Data
			
				
 			$posted_data  = $this->getRequest()->getPost();
  			
			//prd($posted_data);
			if ($form->isValid($this->getRequest()->getPost()))
			{ // Form Valid
				
				$received_data  = $form->getValues();
				$received_data['user_password']=$received_data['user_password1'];
				unset($received_data['user_password1']);
				$email_data=$this->modelStatic->Super_get("users",'user_email="'.$received_data['user_email'].'"','fetch');
				//prd($email_data);
  				/* Zend_Auth Setup Code */
				$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_email', 'user_password'," ? AND (user_type!='admin') and  user_status = '1' " /*, 'MD5(CONCAT(?, password_salt))'*/ );
  				// Set the input credential values
 				$authAdapter->setIdentity($received_data['user_email']);
				$authAdapter->setCredential(md5($received_data['user_password']));
				$result = $auth->authenticate($authAdapter);// Perform the authentication query, saving the result				
				 
				if($result->isValid()){ // IF Auth Get the Record 
 					$data = $authAdapter->getResultRowObject(null); //Now get a result row without user_password set is here
 
 					$auth->getStorage()->write($data); //Now seession set is here
				
					if(isset($_GET['url'])){	
						 $this->_redirect(urldecode($_GET['url']));
					}else{
						if($if_job_post!='' && $if_job_post!='\+s'){
							
							if($if_job_post== '1'){
						 		$this->_redirect('search/homesearch?services=&&pjfcID='.$pjfcID);
								}else{
									$this->_redirect('search/homesearch?services='.$if_job_post.'&&pjfcID='.$pjfcID);
									}
						}else{
							$this->_redirect('user-account');
							}
					}
					 
				}
				else
				{ // Auth Not Valid
					Zend_Auth::getInstance()->clearIdentity();
					if($email_data['user_email_verified']==0 && $email_data['user_status']==0)
					{
						$objSession->errorMsg = "Please verify your email address first";
					}
					else if($email_data['user_email_verified']==1 && $email_data['user_status']==0)
					{
						$objSession->errorMsg = "Your account is blocked by admin";
					}
					else
					{
					$objSession->errorMsg = "Email Address or password is invalid";
					}
					
  				}			
			}
 				if($email_data['user_email_verified']==0 && $email_data['user_status']==0)
					{
						$objSession->errorMsg = "Please verify your email address first";
					}
					else if($email_data['user_email_verified']==1 && $email_data['user_status']==0)
					{
						$objSession->errorMsg = "Your account is blocked by admin";
					}
					else
					{
					$objSession->errorMsg = "Email Address or password is invalid";
					}
			$this->redirect("login");
 		} // End Post Form
		
		$this->view->form = $form;
		
	}
		
	
	
	/* Register User  */
	public function registerAction(){
 		global $objSession;
		$this->view->pageHeading="Sign Up";
		$this->view->user_type='user';
		$this->view->show="register";
		$form = new  Application_Form_User();
		$auth = Zend_Auth::getInstance(); 
		if ($auth->hasIdentity()){
            $objSession->infoMsg ='It seems you are already logged into the system ';
            $this->_redirect('profile');
        }
		$form->register();	
		if($this->getRequest()->isPost()){/* begin : isPost() */			
			$posted_data = $this->getRequest()->getPost();
			//prd($posted_data);
 			if($form->isValid($posted_data)){ /* Begin : isValid()  */
 				
				$this->modelUser->getAdapter()->beginTransaction();
				
				 $data = $form->getValues();
				  $data['user_o_password']=($data['user_password']);	
				 $data['user_password']=md5($data['user_password']);	
				 $data['user_type']='client';			 
				 $data['user_created']=date('Y-m-d H:i:s');
				 
				 $isInserted = $this->modelUser->add($data);
					
				if(is_object($isInserted)){
					 
					 if($isInserted->success){
						 $this->modelUser->getAdapter()->commit();
						 $objSession->successMsg = " Registration Successfully Done . You will receive an activation email on your registered email to activate your account ";
							$this->_redirect('login');
					 }
					 
					  $this->modelUser->getAdapter()->rollBack();

					 if($isInserted->error){
						 
						 if(isset($isInserted->exception)){/* Genrate Message related to the current Exception  */
						
						 }
						 
						 $objSession->errorMsg = $isInserted->message;							 
					 }
					
				}else{
					$objSession->errorMsg = " Please Check Information again ";
				}
 				
			}/* end : isValid()  */
			else{/* begin : else isValid() */
				$objSession->errorMsg = " Please Check Information Again..! ";
 			}/* end : else isValid() */
			$this->_redirect('login');
 		}/* end : isPost() */
		
		
		$this->view->form = $form;
	}

  	
	
	public function registerproAction(){
 		global $objSession;
		$result=$this->modelStatic->Super_Get('services',"service_parent_id ='0'",'fetchAll');
		$this->view->result=$result;
		$auth = Zend_Auth::getInstance(); 
		if ($auth->hasIdentity()){
            $objSession->infoMsg ='It seems you are already logged into the system ';
            $this->_redirect('profile');
        }
		//prd($result);
		$this->view->pageHeading="Sign Up";
		$this->view->user_type='user';
		$this->view->show="register";
		$images=array(
           0=>array(
		   
		   	 'slider_image_title' => '',
            'slider_image_alt' => '',
            'slider_image_path' =>'busniess_pro.jpg'
		   )
		);
		$form = new  Application_Form_User();
		$this->view->slider_images = $images ;
			if(isset($_GET['type']) && $_GET['type']!=''){
				$type = $_GET['type'];
				$form->populate((array)$type);
			} 
			
		
		$form->register();	
		if($this->getRequest()->isPost()){/* begin : isPost() */			
			$posted_data = $this->getRequest()->getPost();
			
			//prd($posted_data);
 			if($form->isValid($posted_data)){ /* Begin : isValid()  */
 				
				$this->modelUser->getAdapter()->beginTransaction();
				
				 $data = $form->getValues();
				 
				 $data['user_password']=md5($data['user_password']);	
				 $data['user_type']='user';			 
				 $data['user_created']=date('Y-m-d H:i:s');
				 
				 $isInserted = $this->modelUser->add($data);
					
				if(is_object($isInserted)){
					 
					 if($isInserted->success){
						 $this->modelUser->getAdapter()->commit();
						 $objSession->successMsg = " Registration Successfully Done . You will receive an activation email on your registered email to activate your account ";
							$this->_redirect('login');
					 }
					 
					  $this->modelUser->getAdapter()->rollBack();

					 if($isInserted->error){
						 
						 if(isset($isInserted->exception)){/* Genrate Message related to the current Exception  */
						
						 }
						 
						 $objSession->errorMsg = $isInserted->message;							 
					 }
					
				}else{
					$objSession->errorMsg = " Please Check Information again ";
				}
 				
			}/* end : isValid()  */
			else{/* begin : else isValid() */
				$objSession->errorMsg = " Please Check Information Again..! ";
 			}/* end : else isValid() */
			$this->_redirect('login');
 		}/* end : isPost() */
		
		
		$this->view->form = $form;
	}
	

	 
	
	 public function registerbusniessAction(){
		
		 global $objSession;
		$images=array(
           0=>array(
		   	 'slider_image_title' => '',
            'slider_image_alt' => '',
            'slider_image_path' =>'busniess_pro.jpg'
		   )
		);
		$auth = Zend_Auth::getInstance(); 
		if ($auth->hasIdentity()){
            $objSession->infoMsg ='It seems you are already logged into the system ';
            $this->_redirect('profile'); 
        }
	
		$this->view->slider_images = $images ;
			$type='';
			$request = $this->getRequest();
			
			if(isset($_GET['type']) && $_GET['type']!=''){
				$type = $_GET['type'];
			}
				$this->view->ftypename=$type;
			if($type == '')
			{
				$this->_redirect('user/registerpro');
			}
			
			$getType = $this->modelStatic->Super_Get('services',"service_name = '".$type."'",'fetch');
			
			if(!$getType){
				$this->_redirect('user/registerpro');  
			}
			$type_id = $getType['service_id'];
			$getCategory = $this->modelStatic->Super_Get('services',"service_parent_id='".$type_id."'","fetchAll");
			//prd($getCategory);
			$subCat = array();
			$i=0;
			foreach($getCategory as $value){
				$getSubCat = $this->modelStatic->Super_Get("services","service_sub_parent_id='".$value['service_id']."'","fetchAll");
				if($getSubCat){
					$subCat[$value['service_id'].'_'.$i] = $getSubCat;
					$i++;
				}
			}
			
			$form = new  Application_Form_User();
			$form->registerpro($type);	
			
				if ($this->getRequest()->isPost()){ // Post Form Data
		
				
 			$posted_data = $this->getRequest()->getPost();
			$form->user_city->setRegisterInArrayValidator(false);
			//prd($posted_data['user_city']);
			if($form->isValid($posted_data)){
				
					
						$data_to_insert  = $form->getValues();
						//prd($data_to_insert);
						$generate_password=genratePassword("test@");
						//prd($generate_password);
						//$zip_code_data = $this->modelStatic->Super_Get("aus_postal_code","postcode_id='".$data_to_insert['user_zip_code']."'","fetch");
						$val=getLnt($data_to_insert['user_zip_code']);
						$data_to_insert['user_type'] ="service_provider";
						$data_to_insert['user_profession'] = $getType['service_id'];
						$data_to_insert['user_travel_lat'] =$val['lat'];
						$data_to_insert['user_travel_long'] =$val['lng'];
						$data_to_insert['user_created'] =date('Y-m-d H:i:s');
						
						/*$data_to_insert['user_full_name'] =$data_to_insert['user_first_name'].' '.$data_to_insert['user_last_name'];*/
					
						
						if($data_to_insert['user_password']!=''){
							//$data_to_insert['without_md5_pass'] = $data_to_insert['user_password'];
							 $data_to_insert['user_o_password']=($data_to_insert['user_password']);	
							$data_to_insert['user_password'] = md5($data_to_insert['user_password']);
						}else {
							//$data_to_insert['without_md5_pass'] = $generate_password;
							 $data_to_insert['user_o_password']=($generate_password);	
							$data_to_insert['user_password'] = md5($generate_password);
						}
						
						
						unset($data_to_insert['submit']);
						unset($data_to_insert['user_protect_account']);
						unset($data_to_insert['user_cpassword']);
						
						
						//$user_insert = $this->modelStatic->Super_Insert('rewilla_site_users',$data_to_insert);
						$isInsert=$this->modelUser->add($data_to_insert);
						
						//prd($isInsert);
						
						$insertedId = $isInsert->inserted_id;

						if($isInsert->success)
						{
							$ServicesToInsert= array();
							
							foreach($_POST['service_cat'] as $val)
							{
								if(isset($_POST['service_sub_cat_'.$val]))
								{
									foreach($_POST['service_sub_cat_'.$val] as $values)
									{
										$ServicesToInsert['us_user_id'] = $insertedId;
										$ServicesToInsert['us_service_id'] = $getType['service_id'];
										$ServicesToInsert['us_service_parent_id'] = $val;
										$ServicesToInsert['us_service_sub_parent_id'] = $values;
										$this->modelStatic->Super_Insert('user_services',$ServicesToInsert);
									}
								}
							}
							$objSession->successMsg="Registration Done Successfully,Please follow the link in your mailbox to activate your account";
							return $this->_redirect("login");
						}
				}
			
			else
			{  //prd($form->getMessages());
					$objSession->errorMsg = " Please Check Information Again..! ";
			}
		}
			$this->view->Categories = $getCategory ;
			$this->view->subCategory = $subCat ;
			$this->view->getTypeForBack = $getType;
			$this->view->form = $form ;
        
	 }
	 
	/*Social media sign up*/
	
	/* 	Forgot Password Send Reset Key to User Email Address 
	 *	@
	 *  Author  - zend
	 */
  	public function forgotpasswordAction(){
		
 		global $objSession;	
		
		$this->view->pageHeading="Forgot Password";
		$form = new  Application_Form_User();
		$superModel = new Application_Model_SuperModel();
 		$form->forgotPassword();
		if($this->getRequest()->getPost()){
		  
			$posted_data  =  $this->getRequest()->getPost();
			
			if($form->isValid($posted_data)){
 				$received_data = $form->getValues();
				$isuser=$superModel->Super_Get("users","user_email='".$received_data['user_email']."' and user_status='1' and user_email_verified='1'");
				if(empty($isuser))
				{
					$objSession->errorMsg="Your email is not verified . Please verify your email address first.";	
					$this->redirect('login');
				}
 				$isSend = $this->modelUser->resetPassword($received_data['user_email']);
 				if($isSend){
					$objSession->successMsg = " Mail has been sent to your account ..! ";
					$this->_redirect('login');
				}
				else{
					$objSession->errorMsg = " Please Check Information Again..! ";
				}
			}else{
				$objSession->errorMsg = " Please Check Information Again..! ";
  			}
		  
		}
		
		$this->view->form = $form;
		//$this->_redirect('');
	}
	
	
	
	/* 	Handle Email Link and Reset the Password 
	 *	@
	 *  Author  - zend
	 */
	public function resetpasswordAction(){
		 
		 global $objSession;
		 
		 $this->view->pageHeading = "Reset Password";
  		
		 $form = new Application_Form_User();
		 $form->resetPassword1();
		 
 		 $key = $this->_getParam('key');
		 
		 if(empty($key)){
 			 $objSession->errorMsg = "Invalid Request for Reset Password ";
			 //$this->_helper->getHelper("Redirector")->gotoRoute(array(),"login");
		 }
		 
 		 $user_info = $this->modelUser->get(array("key"=>"$key"));
		 
		 if(!$user_info){
			 $objSession->errorMsg = "Invalid Request for Password Reset , Please try again .";
			 //$this->_redirect("login");
		 }
		 
 
 		 if($this->getRequest()->getPost()){
			 
			 $posted_data  = $this->getRequest()->getPost();
			 
			 if($form->isValid($posted_data)){
				 
				$data_to_update = $form->getValues() ;
				
				$data_to_update['pass_resetkey']="";
				$data_to_update['user_reset_status']="0";
				
				$data_to_update['user_password'] = md5($data_to_update['user_password']);
				
				$ischeck = $this->modelUser->add($data_to_update,$user_info['user_id']);
				
				//prd($ischeck );
				
				if($ischeck){
					$objSession->successMsg = " Password change Successfully Done ..! ";
					$this->_redirect('login');
					
				}
						
			 }else{
					$objSession->errorMsg = " Please Check Information Again..! ";
 			 }/* end : Else isValid() */
 		 
		 }/* end  : isPost()  */
		 
		 
		 
		 $this->view->form = $form;
		 
	 }
	 
	 
	 
	 
	 
	 /* Email Varification and Account Activation 
	 *	@
	 *  Author  - zend
	 */
	 public function activateAction(){
		
 		global $objSession;
		
		$this->view->pageHeading = "Active Account";
		
 		$key = $this->_getParam('key');
		//echo "select * from users where pass_resetkey='8ef08b1b8f2f059a9a2156b222aa080d'"; 
		$user_info = $this->modelUser->get(array("key"=>"$key"));
		 
		 if(!$user_info){
			 $objSession->errorMsg = "Invalid Request for Account Activation ";
			$this->_redirect('login');
		 }
		 
 		 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1','user_status'=>"1"),$user_info['user_id']);
		 
		 $objSession->successMsg = "Your Account is Successfully Activated , Please Login";
		$this->_redirect('login');
	 
	}
	
	 
	 
	
	public function changepasswordAction(){
		
		global $objSession;
		
 		if(!$this->view->user){
			$objSession->infoMsg = "Please Login First to make Changes";
			$this->_redirect("login");
		}
 			 		
		$this->view->pageHeading = "Change Password";
		
		$form = new Application_Form_User();
		$form->changePassword();
		
 		
		if($this->getRequest()->getPost()){
			
			
			
			$posted_data = $this->getRequest()->getPost();
			
			 
				
			if($form->isValid($posted_data)){
				
				$checkOldPassword = $this->modelUser->get(array("where"=>" user_password='".md5($posted_data['user_old_password'])."' and user_id=".$this->view->user->user_id));
			
				if($checkOldPassword){
					
 	 				if($posted_data['user_password'] == $posted_data['user_rpassword']){
					
						//prd($posted_data);
						
						 
				
  						$ischeck = $this->modelUser->add($form->getValues(), $checkOldPassword['user_id']);
						
						if($ischeck){
							$objSession->successMsg = " Password change Successfuly Done ..! ";
							$this->_redirect('user/changepassword');
							
						}
						else{
							$objSession->errorMsg = " Please Check Information Again..! ";
						}
						 
					}else{
						
 						$form->user_password->setErrors(array('Password Mismatch'));
						$form->user_rpassword->setErrors(array('Password Mismatch'));
						$objSession->errorMsg = " Please type the same password.!";
						$this->render('changepassword');
					}
			}else{
				$form->user_old_password->setErrors(array('Old Password is not match '));
				$objSession->errorMsg = " This Old Password is not match.!";
				$this->render('changepassword');
			}
			
			}else{
				$objSession->errorMsg = " Please Check Information Again..! ";
 				$this->render('changepassword');
			}
		}
		
		$this->view->form = $form;
	
	}
	
	
	/* Send Verification Email */
	public function sendverificationAction(){
 		global $objSession;
		
		$modelEmail = new Application_Model_Email();
		
  		$data_form_values = (array) $this->view->user ;
   		if($this->view->user->user_email_verified!="1"){
  			$user_email_key = md5("ASDFUITYU"."!@#$%^$%&(*_+".time());
			$data_to_update = array("user_email_verified"=>"0","user_email_key"=>$user_email_key);
			$this->modelUser->update($data_to_update, 'user_id = '.$this->view->user->user_id);
			$data_form_values['user_email_key'] = $user_email_key ;
			$modelEmail->sendEmail('email_verification',$data_form_values);
 			$objSession->successMsg = " Email Successfully Send to your email address , please follow the verification link to verify the email address ";
 		}else{
			$objSession->infoMsg = "Your Email Address is already verified..";
		}
  		$this->_redirect("profile");
	}
	
	
	
	/* Email Varification  
	 *	@
	 *  Author  - zend
	 */
	 public function verifyemailAction(){
 	
		global $objSession;
 	
		$key = $this->_getParam('key');
		 


		if(empty($key)){
			$objSession->errorMsg = "Please Check Verifications link again";
			 $this->_redirect("login");	
		}
		
 		$user_info = $this->modelUser->get(array("where"=>"user_email_key='".$key."'"));
		 
 		 if(!$user_info){
			 $objSession->errorMsg = "Invalid Request for Account Activation ";
			 $this->_redirect("profile");
		 }
		 
		 $this->modelUser->update(array('user_email_verified'=>'1',"user_email_key"=>""),"user_id=".$user_info['user_id']);
		 
		 $objSession->successMsg = "Your Email Address is successfully verified";
		 $this->_redirect("profile");
 	}
	
  	
	
 	
	/* 	** Private Method for Handling the Uploaded Image 
	 *	@
	 *  Author  - zend
	 */
	private function upload_user_image(){
		
 		$adapter = new Zend_File_Transfer_Adapter_Http();
 		
		$video = $adapter->getFileInfo('user_image');
		
   		$video_extension = $video['user_image']['name'];
		
 		$extension = explode('.',$video['user_image']['name']); 
		
 		$extension = array_pop($extension);
		
  		$name_for_video = md5(rand(1,999)."@#$%@#&^#$@".time()).".".$extension;
		
  		rename(ROOT_PATH .'/images/profile/'.$video_extension ,  ROOT_PATH .'/images/profile/'.$name_for_video);
		
		return $name_for_video ;
  	}
	
	
	/* 	Ajax Call For Checking the Email Existance for the user email 
	 *	@
	 *  Author  - zend
	 */
	public function checkemailAction(){

 		$email_address = strtolower($this->_getParam('user_email'));
		
		$exclude = strtolower($this->_getParam('exclude'));
		
		$user_id = false ;
		if(!empty($exclude)){
			 $user = $this->view->user;
			 $user_id =$user->user_id;
			
		}

		$email = $this->modelUser->checkEmail($email_address,$user_id);
		
		$rev = $this->_getParam("rev");
		
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
	
	
	
	
	
	/* 	Ajax Call For Checking the Old Password for the Logged User 
	 *	@
	 *  Author  - zend
	 */
	public function checkpasswordAction(){
		
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity()){
			
			$user_password = md5($this->_getParam('user_old_password'));
 			$user = $this->modelUser->get(array('where'=>"user_password='".$user_password."' and user_id=".$this->view->user->user_id));
			
			if(!$user){
				echo json_encode("Old Password Mismatch , Please Enter Correct old password");
			}else{
				echo json_encode("true");	
			}
		}else{
			echo json_encode("Please Login For Make Changes..");
		}
				
 		exit();
	}
	
	
	
	
	/* 	Logout Action 
	 *	@ *  Author  - zend
	
	 */
  	public function logoutAction(){ 
 	    global $objSession;	
		
		$auth = Zend_Auth::getInstance();
 	
		if($this->view->user){
			
			$user =  $this->view->user;
			
			if($user->user_login_type!="normal"){
				
				 
				if($user->user_oauth_provider=="facebook"){
					
 					$facebook = new Facebook(array(
						'appId' => Zend_Registry::get("keys")->facebook->appId ,
						'secret' =>Zend_Registry::get("keys")->facebook->secret ,
						'cookie' => true
					));
					
					$auth->clearIdentity();
								
					$logout_url = $facebook->getLogoutUrl(array( 'next' => APPLICATION_URL."/login"));
					
					header("Location:".$logout_url);
					 		
					$objSession->successMsg = "You are now logged out. ..! ";
					
					exit();
				
 				}
 			}
			
			$auth->clearIdentity();
 		
			$objSession->successMsg = "You are now logged out. ..! ";
			
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_page");
 				 
		}
			
			$objSession->successMsg = "You are now logged out. ..! ";

			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_page");
	}
	
// dropdown for cities
	public function citiesdropdownAction(){
			$state = $_GET['name'];		
			$data=$this->modelStatic->Super_Get("zips",'state = "'.$state.'"',"fetchAll",array('group'=>"city"));
			//prd($data);
			   echo json_encode($data);
			exit;
		}	
		
	
  	
}

