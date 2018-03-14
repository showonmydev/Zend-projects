<?php
class Privatepanel_IndexController extends Zend_Controller_Action
{
	private $modelUser;
	
	
	public function init(){	
         $this->modelUser = new Application_Model_User();
		 $this->SuperModel = new Application_Model_SuperModel();
    }
	
	
	/* DASHBORAD */	
	public function indexAction(){
			
		$this->view->pageHeading = "Dashboard";
		$this->view->pageDescription = "statistics and more";
		
		
		$visitorMonthData=$this->SuperModel->Super_Get("site_visitors","1","fetchAll",$extra=array( 'fields'=>array('Year'=> new Zend_Db_Expr('YEAR(visitor_date)'),'Month'=> new Zend_Db_Expr('MONTH(visitor_date)'),'totalCount'=> new Zend_Db_Expr('ifnull(count(DISTINCT(visitor_id)),0)')),'group'=>array('YEAR(visitor_date)','MONTH(visitor_date)')));

		$allVisitorsArr=array();
		for($i=0;$i<12;$i++){
			$allVisitorsArr[$i]=0;
		}
		foreach($visitorMonthData as $allVisitorsData){
			$allVisitorsArr[$allVisitorsData['Month']-1]=(int)$allVisitorsData['totalCount'];
		}
		$this->view->allVisitorsArr=json_encode($allVisitorsArr);
		
// monthly jobs		
		$visitorjobsData=$this->SuperModel->Super_Get("job","1","fetchAll",$extra=array( 'fields'=>array('Year'=> new Zend_Db_Expr('YEAR(posted_job_created)'),'Month'=> new Zend_Db_Expr('MONTH(posted_job_created)'),'totalCount'=> new Zend_Db_Expr('ifnull(count(DISTINCT(job_id)),0)')),'group'=>array('YEAR(posted_job_created)','MONTH(posted_job_created)')));

		$alljobsArr=array();
		for($i=0;$i<12;$i++){
			$alljobsArr[$i]=0;
		}
		foreach($visitorjobsData as $alljobData){
			$alljobsArr[$alljobData['Month']-1]=(int)$alljobData['totalCount'];
		}
		$this->view->alljobsArr=json_encode($alljobsArr);
		
// monthly Revenue		
		$RevenueData=$this->SuperModel->Super_Get("proposal","1","fetchAll",$extra=array( 'fields'=>array('Year'=> new Zend_Db_Expr('YEAR(proposal_date)'),'Month'=> new Zend_Db_Expr('MONTH(proposal_date)'),'totalCount'=> new Zend_Db_Expr('ifnull(count(DISTINCT(proposal_id)),0)'),'RevenueSum'=>'SUM(proposal_casa_revenue)'),'group'=>array('YEAR(proposal_date)','MONTH(proposal_date)')));

//prd($RevenueData);
		$allRevenueArr=array();
		for($i=0;$i<12;$i++){
			$allRevenueArr[$i]=0;
		}
		foreach($RevenueData as $allRevenuData){
			$allRevenueArr[$allRevenuData['Month']-1]=(int)$allRevenuData['RevenueSum'];
		}
		$this->view->allRevenueArr=json_encode($allRevenueArr);

		
	}	
	
	public function deniedAction(){	
 		$this->view->pageHeading = "Access Denied";
		$this->view->pageDescription = "You Cannot Access That Page , ";
		$this->view->breadcrumb =array('Access Denind'=>'#');
 	}	
	
   	public function changepasswordAction(){
		
 			
		global $mySession; 
		
		$this->view->pageHeading = "Change Password";
		$this->view->pageDescription = "change admin login password";
		$this->view->breadcrumb =array('Manage Site '=>'/index/changepassword#' , 'Change Password' =>'/index/changepassword#');
		
		$form = new Application_Form_Admin();
		$form->changePassword();
 		
		if($this->getRequest()->isPost()){
			
			$formData = $this->getRequest()->getPost();
	 		
			if($form->isValid($formData) && $formData['new_password']==$formData['confirm_password']){
				
				 
   				$isOkay = $this->modelAdmin->checkOldPassword($formData['old_password']);
 				if($isOkay){
 					$this->modelAdmin->editProfile(array('admin_password'=>md5($formData['new_password'])));
					$mySession->successMsg = " Password Change Succeessfuly...! ";
					$this->_redirect('privatepanel');
 				} 
				$mySession->errorMsg = " Old Password Mismatch Please Insert Correct Old Password...! ";
			}else{
				$mySession->errorMsg = " Please Enter Correct Information ..! "; 
			}
			
		}

		$this->view->form = $form;
 	  
	}
	
	
 	
	/* Admin Login  */
   	public function loginAction(){	
		
		global $mySession ; 
		
 		$Admin_User = Zend_Session::namespaceGet(ADMIN_AUTH_NAMESPACE);
		
		if(isset($Admin_User['storage'])){
			$adminLogged = $Admin_User['storage'];
			
			if(isset($adminLogged->user_id)){
				$this->_redirect('/admin');
			}
 		}
		
		
		$form = new Application_Form_User();
		$form->login_front(true);
		
  
		$this->_helper->layout->setLayout('login');
		 
   
		if ($this->getRequest()->isPost()){/* begin : isPost() */
		
			$formData = $this->getRequest()->getPost();
			
			if($form->isValid($formData)){
 				 
 
  				$received_data = $form->getValues();
 				$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_email',   'user_password'," ? AND user_type = 'admin' " /*, 'MD5(CONCAT(?, password_salt))'*/ );
 				$authAdapter->setIdentity($received_data['user_email']);
				$authAdapter->setCredential((md5($received_data['user_password'])));
 				
				$auth   = Zend_Auth::getInstance();	
				$auth->setStorage(new Zend_Auth_Storage_Session(ADMIN_AUTH_NAMESPACE));
				$result = $auth->authenticate($authAdapter);
				$adminLogged = Zend_Auth::getInstance()->getIdentity();
			    
				if($result->isValid()){
					
					
					$data = $authAdapter->getResultRowObject(null);
 
 					$auth->getStorage()->write($data); 
					
					if(isset($_GET['url'])){	
						$this->_redirect(urldecode($_GET['url']));
					}else{
						$this->_redirect('/privatepanel');
					}
				}else{  
					
					$mySession->errorMsg ="Invalid Login Details";
				}
			}
			
 			$mySession->errorMsg="Invalid Login Details";
 		}/* end : isPost() */ 
			
		
		$this->view->form = $form ;
		 
    }
	
 	
  	public function logoutAction(){	
		$this->_helper->viewRenderer->setNoRender(true);
		$Admin_User=Zend_Session::namespaceUnset(ADMIN_AUTH_NAMESPACE);
		$this->_redirect('/privatepanel/login');
	}
	
	
 	/* Editor Browse Images */
 	public function browseAction(){

		$modelStatic = new Application_Model_Static();

		$images = $modelStatic->getMedia();
  
		$this->view->images = $images;
  		
		$this->_helper->layout->setLayout('media');
		//$this->_helper->layout->disableLayout(0);
		$this->_helper->viewRenderer->setNoRender(true);
		
 	}
  	
	/* Editor Upload Image */
	public function uploadAction(){

		 
		if(isset($_FILES['upload'])){
			
			$filen = $_FILES['upload']['tmp_name']; 
			
			$mysize=$_FILES['upload']['size'];
			
			$name = time().rand(1,500).$_FILES['upload']['name'] ;
			$con_images = MEDIA_IMAGES_PATH."/".$name;
			
			move_uploaded_file($filen, $con_images );
			
			$url = HTTP_MEDIA_IMAGES_PATH."/".$name;
			
			$funcNum = $_GET['CKEditorFuncNum'] ;
			// Optional: instance name (might be used to load a specific configuration file or anything else).
			$CKEditor = $_GET['CKEditor'] ;
			// Optional: might be used to provide localized messages.
			$langCode = $_GET['langCode'] ;
		   // Usually you will only assign something here if the file could not be uploaded.
			if($mysize>10485760){
				$message = 'Please upload image of size 1Mb or Less';
				$url='';
				echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
				exit();
			}else{
				 echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
			}
		}
 
  		
		//$this->_helper->layout->setLayout('media');
		//$this->_helper->layout->disableLayout(0);
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		exit();
 	}
	
	
	
	/* 	Ajax Call For Checking the Old Password for the Logged User 
	 *	@
	 *  Author  - zend
	 */
	public function checkpasswordAction(){/*AJAX*/
		
 		$Admin_User=Zend_Session::namespaceGet(ADMIN_AUTH_NAMESPACE);
 		
		if(isset($Admin_User['storage'])){
			
			$adminLogged = $Admin_User['storage'];

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
	
	
  
}

