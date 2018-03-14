<?php
class StaticController extends Zend_Controller_Action
{

	private $modelStatic = "" , $modelTeam ="" ;
	
    public function init(){
		$this->modelsuper = new Application_Model_SuperModel();
		$this->modelStatic = new Application_Model_Static();
		$this->modelCommon= new Application_Model_Common();
	}
	
	// casa slider 
	
	  public function casahomesliderAction()
   {
	  global $objSession; 
	  $form = new Application_Form_Casa();
	  $form->casahomeslider();
	  $this->view->form = $form;
  }
  
	
	// end casa slider
	
	
	
	/* Static Pages  */
	public function faqAction(){
		global $objSession;
		$page_id = 22;
 	    $this->view->show='faq';
		$this->view->page_id = $page_id;
		$faqdata = $this->modelsuper->Super_Get('faq',"1","fetchAll");
		$this->view->faqdata = $faqdata;
	}
	
	public function indexAction(){
		
		global $objSession;
 
 		$page_id =  $this->getRequest()->getParam('page_id');
		//prd($page_id);
 		$this->view->page_id = $page_id;
 		$content = $this->modelStatic->getPage($page_id); 
		
		$TeamData = $this->modelsuper->Super_Get('about_team_member',"1","fetchAll");
		$this->view->TeamData = $TeamData;
		
		if($page_id==1)
		{
		$this->view->show='about';	
		}
		else if($page_id==2)
		{
			$this->view->show='privacy';
		}
		else if($page_id==3)
		{
			$this->view->show='how';
		}
		else if($page_id==21)
		{
			$this->view->show='term';
		}
		else if($page_id==22)
		{
			$this->view->show='faq';
		}
		
		
		
		$block_keys = array();
		$block_values = array();
		
		$first_flag = true;

		
		/*$this->view->pageHeading=$content['page_title'];*/
		
		$this->view->content=$content;
		
 	}	
	
	public function aboutteammemberAction(){
				global $objSession ; 
		$this->_helper->layout->disableLayout();
		$member_id = $this->getRequest()->getParam('member_id');
		
		$memberDetails = $this->modelsuper->Super_Get('about_team_member',"ateam_member_id='".$member_id."'","fetch");
		$this->view->memberDetails = $memberDetails;

		}
	
	public function contactAction(){
		
 		global $objSession;
 		
		$page_id = 9;
 	 $this->view->show='contact_us';
		$this->view->page_id = $page_id;
		
		$this->view->pageHeading= " Contact Us ";
		
		$this->view->layout_show_map = true ;
		
		$content = $this->modelStatic->getPage($page_id); 
		$this->view->show='contact';
		$this->view->content=$content;
		//prd($content); 
		$form=new Application_Form_User();
		$form->contact_us();
 		
		if($this->getRequest()->isPost()){
			$data =$this->getRequest()->getPost();
			if($form->isValid($data)){
				
 				$modelEmail = new Application_Model_Email();
  				$is_send =  $modelEmail->sendEmail("contact_us",$form->getValues());
 				$objSession->successMsg = "Mail Successfully Send "; 
 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"contact_us");
			}
		}
		
		
		$this->view->form=$form;		
 		//prd($form);
		
 	}
	
 	
	public function subscribeAction(){
		
 		global $objSession;
 		
		$page_id=7;
 	 
		$this->view->page_id = $page_id;
		
		$content = $this->modelStatic->getPageContent($page_id); 
		
		$this->view->content=$content;
		
		
		$form=new Application_Form_Subscribe();
		$this->view->form=$form;
		
		if($this->getRequest()->isPost()){
			$data =$this->getRequest()->getPost();
			if($form->isValid($data)){
				
				if($this->modelTeam->addSubscription($form->getValues())){
					$objSession->successMsg = " Your Subscription is Successfully Done .. Thank You  ";
 					$this->subscription_request_mail($form->getValues());	
					$this->_redirect('content/subscribe');
				}else{
					
					unset($objSession->errorMsg);
					$objSession->successMsg = "You are already subscribed for new listing alerts";
					$this->_redirect('content/subscribe');
				}
 				//
			}
		}
				
 		
		$this->view->heading=$content['content_title'];
		
	}
	
	
	private function subscription_request_mail($data_form){
		
		global $objSession; 
		
		$site_config= Zend_Registry:: get("site_config"); 

		$admin_email=Zend_Registry::get('admin_email');
		$admin_name=Zend_Registry::get('admin_name');
		$site_title = $site_config['site_title'];
		
		$modelTemplate = new Application_Model_Email();
  		$template =$modelTemplate->getEmailTemplateByKey("subscription_request");

		$sender_email = $data_form['guest_email'];
		$sender_name = $data_form['guest_name'];
 		
		$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
 		 
		$mail_content = str_ireplace(
								array( "{SITE_TITLE}" ,"{site_admin}","{sender_name}","{sender_email}" ), 
								array(	$site_title , $admin_name,$sender_name,$sender_email),
								$template['emailtemp_content']
							);
		
		
		
		$mail = new Zend_Mail();
    	$mail->setBodyHtml($mail_content)
        ->addTo($sender_email , $sender_name)
        ->setFrom($admin_email, $site_title)
        ->setSubject($subject);
	 
		
		if(!TEST){
			 $mail->send();
		}
		
		
		
		/* Send Email to Admin */
		$modelTemplate = new Application_Model_Email();
  		$template =$modelTemplate->getEmailTemplateByKey("subscription_request_admin");

		$sender_email = $data_form['guest_email'];
		$sender_name = $data_form['guest_name'];
 		
		$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
 		 
		$mail_content = str_ireplace(
			array( "{SITE_TITLE}" ,"{site_admin}","{sender_name}","{sender_email}" ), 
			array(	$site_title , $admin_name,$sender_name,$sender_email),
			$template['emailtemp_content']
		);
		
							
 		$mail = new Zend_Mail();
    	$mail->setBodyHtml($mail_content)
        ->setFrom($sender_email , $sender_name)
        ->addTo($admin_email, $site_title)
        ->setSubject($subject);
				
 		//$objSession->successMsg = " Your Contact Request Successfully Sent.  Thank You ! ";
 		if(!TEST and $mail->send()){ return true;} else {return false;}
		 
 		
	}
	
 	
 }

