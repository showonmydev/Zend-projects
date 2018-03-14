<?php

class ProjectController extends Zend_Controller_Action
{
	public function init(){	
		$this->modelsuper = new Application_Model_SuperModel();
		$this->modelStatic = new Application_Model_Static();
		$this->modelProject = new Application_Model_Project();
		
   	}
	
 	public function indexAction(){
		$extra= array(
			'group'=>'job_id',
			'order'=>'proposal_id desc'
		);
		$joinArr = array(
					'0'=>array("proposal","p_job_id=job_id","left",array('totalProposal'=> new Zend_Db_Expr('ifnull(count(DISTINCT(proposal_id)),0)'))),
					'1'=>array("services","service_id=job_cat_id","left",array('service_name')),
					'2'=>array("notifications","notification_job_id=job_id && notification_reciver='".$this->view->user->user_id."'","left",array('notification_main_status','notification_message')),
					);
					
		$posted_jobs=$this->modelStatic->Super_Get("job","job_posted_by='".$this->view->user->user_id."' && job_enable_disable='1'","fetchAll",$extra,$joinArr);
		$this->view->posted_jobs = $posted_jobs;
//get page desc
		$page_desc=$this->modelStatic->Super_Get("Project_Page",1,"fetch");
		$this->view->page_desc = $page_desc;

		}

// Decline Service Provider 
	public function declineproAction(){
		global $objSession;
		
		if($this->getRequest()->isPost()){			
			$posted_data = $this->getRequest()->getPost();
			if($posted_data!=''){ 
					$decline['proposal_decline_status']='2';
					$where = "proposal_id='".$posted_data['decline_proposal_ID']."' and sender_id='".$posted_data['declined_provider_id']."' && p_job_id='".$posted_data['decline_job_id']."'";
					$isUpdateDecline = $this->modelsuper->Super_Insert("proposal",$decline,$where);
					
					if(is_object($isUpdateDecline) and $isUpdateDecline->success){
						
					// send hire notice to SP
							/*	$notifiDecline['notification_job_id']= $posted_data['decline_job_id'];
								$notifiDecline['notification_sender']= $this->view->user->user_id;
								$notifiDecline['notification_reciver']= $posted_data['decline_proposal_ID'] ;
								$notifiDecline['notification_date']=date('Y-m-d H:i:s');
								$notifiDecline['notification_message']="Declined";
								//$notifiDecline['notification_type']=3;
								$send_notification = $this->modelsuper->Super_Insert("notifications",$notifiHire);*/
					//end send hire notice to SP	
								$objSession->successMsg = "Declined";
	$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['decline_job_id'],'quote_sender'=>$posted_data['declined_provider_id']),"front_viewquote");
							}
		   }
			else{	
				$objSession->errorMsg = " Please Check Information Again..! ";
				}
	$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['decline_job_id'],'quote_sender'=>$posted_data['declined_provider_id']),"front_viewquote");
			
		}
		
	}
		
// hire Service Provider 
	public function hireAction(){
		global $objSession;
		
		//$provider_id=$_REQUEST['provider_id'];
		$form1 = new Application_Form_ProjectForm();
		$form1->hireSP();
			
		if($this->getRequest()->isPost()){			
				$posted_data = $this->getRequest()->getPost();
			if($form1->isValid($posted_data)){ 
					$hire['hired_provider_id']=$posted_data['provider_id'];
					$hire['hire_quote_client']=$posted_data['hire_quote_client'];
					$hire['hired_proposal_id']=$posted_data['hire_proposal_ID'];
					$hire['job_status']="running";
					$hire['hired_on_date']=date('Y-m-d H:i:s');
					//prn($hire);
					$where = "job_id='".$posted_data['hire_job_id']."' and job_status='pending'";
					$isUpdateHire = $this->modelsuper->Super_Insert("job",$hire,$where);
					//prn($isUpdateHire);
					if(is_object($isUpdateHire) and $isUpdateHire->success){
						
					// send hire notice to SP
								$notifiHire['notification_job_id']= $posted_data['hire_job_id'];
								$notifiHire['notification_sender']= $this->view->user->user_id;
								$notifiHire['notification_reciver']= $posted_data['provider_id'] ;
								$notifiHire['notification_date']=date('Y-m-d H:i:s');
								$notifiHire['notification_message']="Hired";
								$notifiHire['notification_type']=3;
								$send_notification = $this->modelsuper->Super_Insert("notifications",$notifiHire);
					//end send hire notice to SP	
					
					// Change proposal status
							$hireproposal['proposal_decline_status']= "1";
							$whereProposal = "p_job_id='".$posted_data['hire_job_id']."' and proposal.sender_id='".$posted_data['provider_id']."' && proposal_decline_status='0'";
							$isUpdateHireproposal = $this->modelsuper->Super_Insert("proposal",$hireproposal,$whereProposal);
							
						$declineproposal['proposal_decline_status']= "2";
						$whereDeclineProposal = "p_job_id='".$posted_data['hire_job_id']."' and proposal.sender_id!='".$posted_data['provider_id']."' && proposal_decline_status='0'";
						$isUpdateDeclineproposal = $this->modelsuper->Super_Insert("proposal",$declineproposal,$whereDeclineProposal);
							
						
					//end Change proposal status	
						
						
								$objSession->successMsg = "hire";
		$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['hire_job_id'],'quote_sender'=>$posted_data['provider_id']),"front_viewquote");
							}
		   }
			else{	
				$objSession->errorMsg = " Please Check Information Again..! ";
				}
		$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['hire_job_id'],'quote_sender'=>$posted_data['provider_id']),"front_viewquote");
			
		}
		
	}
	
// Post Review
	public function postreviewAction(){
			
		global $objSession ; 
		$ServiceProviderId = $this->getRequest()->getParam('provider_id');
		$ProposalId = $this->getRequest()->getParam('proposalID');
		
		$form2 = new Application_Form_ProjectForm();
		$form2->review();
		$this->view->form2=$form2;
		
		$extra= array(
						);
		$joinArr = array(
					'0'=>array("users","user_id=proposal.sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
					);
		$ReviewFor = $this->modelsuper->Super_Get('proposal','proposal_id="'.$ProposalId.'"',"fetch",$extra,$joinArr);
		$this->view->ReviewFor = $ReviewFor;
		
		
// save data in review table	
		if($this->getRequest()->isPost()){			
			$posted_data = $this->getRequest()->getPost();
			
				if($form2->isValid($posted_data)){ 
				
				$review['review_client']=$this->view->user->user_id;
				$review['review_sp']=$posted_data['review_sp'];
				$review['review_job_id']=$posted_data['review_job_id'];
				$review['review_proposal_id']=$posted_data['review_proposal_id'];
				$review['review_ratings']=$posted_data['review_rating'];
				$review['review_msz']=$posted_data['review_msz'];
				$review['review_title']=$posted_data['review_title'];
				$review['review_date']=date('Y-m-d H:i:s');
				
				$isInserted = $this->modelsuper->Super_Insert("reviews",$review);
				
				if(is_object($isInserted) and $isInserted->success){
					// Change job status		
					$completeJob['job_status']="completed";
					$completeJob['job_complete_on']=date('Y-m-d H:i:s');
					$completeJob['job_review_status']='1';
					$where = "job_id='".$posted_data['review_job_id']."' and job_status='running'";
					$isUpdateHire = $this->modelsuper->Super_Insert("job",$completeJob,$where);
					// Change proposal status
						$completeProposal['proposal_decline_status']= "3";
						$whereProposal = "p_job_id='".$posted_data['review_job_id']."' and proposal.sender_id='".$posted_data['review_sp']."' && proposal_decline_status='1'";
						$isUpdateHireproposal = $this->modelsuper->Super_Insert("proposal",$completeProposal,$whereProposal);
					// send Pro notice
							$reviewNotice['notification_job_id']= $posted_data['review_job_id'];
							$reviewNotice['notification_sender']= $this->view->user->user_id;
							$reviewNotice['notification_reciver']= $posted_data['review_sp'];
							$reviewNotice['notification_date']= date('Y-m-d H:i:s');
							$reviewNotice['notification_message']="Client send review";
							$reviewNotice['notification_type']="8";
							$reviewNotice['notification_status']="0";
							$reviewNotice['notification_main_status']="0";
							
							$SendReviewNotice = $this->modelsuper->Super_Insert("notifications",$reviewNotice);
							
							$objSession->successMsg = "Thank you for review";
							//$this->_helper->getHelper("Redirector")->gotoRoute(array('proposalID'=>$posted_data['review_proposal_id']),"front_review");
		$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['review_job_id'],'quote_sender'=>$posted_data['review_sp']),"front_viewquote");
						}
				}
				else{	
					$objSession->errorMsg = " Please Check Information Again..! ";
					}
			//	$this->_helper->getHelper("Redirector")->gotoRoute(array('proposalID'=>$posted_data['review_proposal_id']),"front_review");
		$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['review_job_id'],'quote_sender'=>$posted_data['review_sp']),"front_viewquote");
		}
		
		
  	}
	
// all review
	public function allreviews(){
		$where = "review_id='".$id."'";
		$result = $this->modelStatic->Super_Get("reviews",$data,$where);
		}
	
// reply review

	public function replyreviewAction(){
			$formData = $this->getRequest()->getPost();
		//	prn($formData);
			if ($this->getRequest()->isPost()){
				$data=array();
				
				$data['review_reply_msz']=$formData['review_reply_msz'];
				$where = "review_id='".$formData['review_id']."'";
				$isInserted = $this->modelStatic->Super_Insert("reviews",$data,$where);
			//	prn($isInserted);
				if($isInserted->success){ 
				// send Client notice for review reply
				$getReviewClient = $this->modelStatic->Super_Get("reviews","review_id='".$formData['review_id']."'","fetch",array('fields'=>'review_client'));
							$reviewNotice['notification_job_id']= $formData['review_job_id'];
							$reviewNotice['notification_sender']= $this->view->user->user_id;
							$reviewNotice['notification_reciver']=$getReviewClient['review_client'];
							$reviewNotice['notification_date']= date('Y-m-d H:i:s');
							$reviewNotice['notification_message']="SP replyed to review";
							$reviewNotice['notification_type']="9";
							$reviewNotice['notification_status']="0";
							$reviewNotice['notification_main_status']="0";
							
							$SendReviewNotice = $this->modelsuper->Super_Insert("notifications",$reviewNotice);
							//prd($SendReviewNotice);
						$result = $this->modelStatic->Super_Get("reviews",$where,"fetch",array('fields'=>'review_reply_msz'));
						echo $result['review_reply_msz'];
				}else{
					echo "0";
				}
			}else{
				echo '0';
			}
			exit();
		}
	

// view job(project)
	public function viewprojectAction(){
			
		global $objSession ; 
		$this->_helper->layout->disableLayout();
		$job_id = $this->getRequest()->getParam('id');
		$CurrentAction = $this->getRequest()->getParam('cur_action');
		$this->view->CurrentAction =$CurrentAction;
			$extra= array(
						);
		$joinArr = array(
					'0'=>array("services","service_id=job_cat_id","left",array('service_name')),
					'1'=>array("users","user_id=job_posted_by","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
					);
		$getjob = $this->modelsuper->Super_Get('job','job_id="'.$job_id.'"',"fetch",$extra,$joinArr);
		$this->view->getjob = $getjob;
		
		$all_data=unserialize($getjob['job_all_data']);
		$this->view->all_data=$all_data;
  	}
		
// remove job 
	public function removejobAction(){
		
		global $objSession;
		$job_id=$_REQUEST['job_id'];
			//prd($job_id);
		$where = "job_id='".$job_id."'";
		$removeJob = $this->modelStatic->Super_Delete("job" , $where);
		//prd($removeJob);
		if(is_object($removeJob)){
			 if($removeJob->success){
				$objSession->successMsg ="Job removed successfully.";
				$this->_redirect("/project/");
			 }
			 if($removeJob->error){
				 if(isset($removeJob->exception)){/* Genrate Message related to the current Exception  */
				 }
				 $objSession->errorMsg = $removeJob->message;							 
			 }
		}else{
			$objSession->errorMsg = " Please Check Information again ";
		}
	}
	
// all SP notifications
	public function jobrequestAction(){
	// show all job request after his registration
	
		$max_proposal_limit = $this->view->site_configs['max_proposal_limit'];
		$extra_sender= array(
						'order'=>'notification_date desc',
						'group'=> 'notification_id',
						'having'=>'totalProposal=totalProposal_check ' ,
						'pagination'=>1,
					   );
		$joinArr = array(
			'0'=>array("users","user_id=notification_sender","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
			'1'=>array("job","job_id=notification_job_id && job_status='pending' && job_enable_disable=1","full",array('job_posted_by','posted_job_created','job_all_data','job_reminder_status','job_more_quote_status','job_enable_disable','job_status')),
			'2'=>array("proposal as p1","p1.p_job_id=notification_job_id","left",array('totalProposal'=> new Zend_Db_Expr('ifnull(count(DISTINCT(p1.proposal_id)),0)'))),
			'3'=>array("proposal as p2","p2.p_job_id=notification_job_id && p2.sender_id!='".$this->view->user->user_id."'","left",array('totalProposal_check'=> new Zend_Db_Expr('ifnull(count(DISTINCT(p2.proposal_id)),0)'))),
			//'4'=>array("more_quote","more_quote_job_id=notification_job_id && more_quote_status='1'","left",array('more_quote_admin_allowed1'=> new Zend_Db_Expr('ifnull(more_quote_admin_allowed,0)'),'more_quote_admin_allowed')),
			);
		$AllRequestedJobs= $this->modelStatic->Super_Get("notifications","notification_reciver='".$this->view->user->user_id."' && notification_type='1'","fetchAll",$extra_sender,$joinArr);
		$this->view->AllRequestedJobs = $AllRequestedJobs;
	//prd($AllRequestedJobs);
		
		  
			$adapter = new Zend_Paginator_Adapter_DbSelect($AllRequestedJobs);
			$paginator = new Zend_Paginator($adapter);
			$page = $this->_getParam('page',1);
			$rec_counts = 7; //Item per page
			
			$paginator->setItemCountPerPage($rec_counts);
			$paginator->setCurrentPageNumber($page);
			$paginator->setDefaultPageRange(8);
			
			$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'pagination-control.phtml');
			$this->view->paginationControl=$paginationControl;
			$this->view->paginator=$paginator;

// end show all job request after his registration
		}	
// end SP all notifications	

// Send Reminder to Admin
	public function morequoteadminAction(){
		global $objSession;
		
		$form2 = new Application_Form_ProjectForm();
		$form2->moreQuoteClient();
			
		if($this->getRequest()->isPost()){			
				$posted_data = $this->getRequest()->getPost();
			//	prd($posted_data);
			if($form2->isValid($posted_data)){ 
					
					$moreQuoteRequest['more_quote_sender']=$posted_data['more_quote_sender'];
					$moreQuoteRequest['more_quote_receiver']="1"; // admin id
					$moreQuoteRequest['more_quote_job_id']=$posted_data['more_quote_job_id'];
					$moreQuoteRequest['more_quote_client_request']=$posted_data['more_quote_client_request'];
					$moreQuoteRequest['more_quote_admin_allowed']='';
					$moreQuoteRequest['more_quote_date']=date('Y-m-d H:i:s');
					//prd($moreQuoteRequest);
					
					$isSendRequestAdmin = $this->modelsuper->Super_Insert("more_quote",$moreQuoteRequest);
					//prd($isSendRequestAdmin);
					if(is_object($isSendRequestAdmin) and $isSendRequestAdmin->success){
						
	// save data in notification table
						$reminderToAdmin['notification_job_id']= $posted_data['more_quote_job_id'];
						$reminderToAdmin['notification_sender']= $this->view->user->user_id;
						$reminderToAdmin['notification_reciver']= "1";
						$reminderToAdmin['notification_date']=date('Y-m-d H:i:s');
						$reminderToAdmin['notification_message']="Reminder! need more Quote";
						$reminderToAdmin['notification_type']='5';
						$reminderToAdmin['notification_status']='0';
						$reminderToAdmin['notification_main_status']='0';
						$send_reminder_to_admin = $this->modelsuper->Super_Insert("notifications",$reminderToAdmin);
						$lastIsertedId = $send_reminder_to_admin->inserted_id;

	//end saved data in notification table	
	// update job reminder status					
								if($lastIsertedId!=''){
										$where1 = "job_id='".$posted_data['more_quote_job_id']."'";
										$changeStatus1['job_reminder_status']=1; 
										$change_job_reminder_status = $this->modelsuper->Super_Insert("job",$changeStatus1,$where1);	
								}
								$objSession->successMsg = "Request sent";
								$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['more_quote_job_id']),"front_viewquote");
							}
		   }
			else{	
				$objSession->errorMsg = " Please Check Information Again..! ";
				}
			$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$posted_data['more_quote_job_id']),"front_viewquote");
		}
		
		
	}
// end Send Reminder to Admin

// Send Reminder to SP
	public function sendreminderAction(){
		global $objSession;
		$job_id = $this->getRequest()->getParam('id');
		
		$AllreadySendNotice=$this->modelStatic->Super_Get("notifications","notification_job_id='".$job_id."' && notification_type='1'","fetchAll");
		$ProposalReceived=$this->modelStatic->Super_Get("proposal","p_job_id='".$job_id."'","fetchAll");
		$ProposalSenderIdArray = array_column($ProposalReceived,'sender_id');
		
		
		foreach($AllreadySendNotice as $NoticeReceiver){
				if(!in_array($NoticeReceiver['notification_reciver'],$ProposalSenderIdArray)){
	// save data in notification table
						$reminder['notification_job_id']= $job_id;
						$reminder['notification_sender']= $this->view->user->user_id;
						$reminder['notification_reciver']= $NoticeReceiver['notification_reciver'];
						$reminder['notification_date']=date('Y-m-d H:i:s');
						$reminder['notification_message']="Reminder! New job posted";
						$reminder['notification_type']='4';
						$reminder['notification_status']='0';
						$reminder['notification_main_status']='0';
						
						$send_reminder = $this->modelsuper->Super_Insert("notifications",$reminder);
						$lastIsertedId = $send_reminder->inserted_id;
					//	prn($send_reminder);
	//end saved data in notification table	
					}
			}
		
		if($lastIsertedId!=''){
		$where = "job_id='".$job_id."'";
		$changeStatus['job_reminder_status']=1; 
		$change_job_reminder_status = $this->modelsuper->Super_Insert("job",$changeStatus,$where);	
		}
			
		exit();
		}	
// end Send Reminder to SP

// SP my job
	public function myjobAction(){
		$jobTypeTab = $this->getRequest()->getParam('jobtype');
		$this->view->jobTypeTab =$jobTypeTab;
		
// Query for job in progress	
		$extra= array(
					'pagination'=>1,
					'order'=>'proposal_id desc',
					   );			   
		$join = array(
			'0'=>array("users","user_id=proposal.receiver_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_first_name as FirstName','user_image')),
			'1'=>array("job","job_id=p_job_id && job_enable_disable=1","full",array('job_posted_by','posted_job_created','job_all_data','job_status','job_cat_id')),
			'2'=>array("services","service_id=job_cat_id","left",array('service_name')),
		);
		if($jobTypeTab=='inprogress'){	
			$myjob=$this->modelStatic->Super_Get("proposal","proposal.sender_id='".$this->view->user->user_id."' && (proposal_decline_status='0' || proposal_decline_status='2')","fetchAll",$extra,$join);
			$this->view->myjob = $myjob;
			//prd($myjob);
		}elseif($jobTypeTab=='hire'){
			$extra= array(
					'pagination'=>1,
					'order'=>'job_complete_on desc',
			 );
			$myjob=$this->modelStatic->Super_Get("proposal","proposal.sender_id='".$this->view->user->user_id."' && proposal_decline_status='1'","fetchAll",$extra,$join);
			$this->view->myjob = $myjob;
		}elseif($jobTypeTab=='complete'){
			$extra= array(
					'pagination'=>1,
					'order'=>'hired_on_date desc',
			 );
			
		 $join = array(
			'0'=>array("users","user_id=proposal.receiver_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_first_name as FirstName','user_image')),
			'1'=>array("job","job_id=p_job_id && job_enable_disable=1","full",array('job_posted_by','posted_job_created','job_all_data','job_status','job_cat_id')),
			'2'=>array("services","service_id=job_cat_id","left",array('service_name')),
			'3'=>array("reviews","review_sp='".$this->view->user->user_id."' && review_job_id=p_job_id","left",array('*')),
			);
			$myjob=$this->modelStatic->Super_Get("proposal","proposal.sender_id='".$this->view->user->user_id."' && proposal_decline_status='3'","fetchAll",$extra,$join);
			$this->view->myjob = $myjob;
		}
		$adapter = new Zend_Paginator_Adapter_DbSelect($myjob);
		$paginator = new Zend_Paginator($adapter);
		$page = $this->_getParam('page',1);
		$rec_counts = 5; //Item per page
		
		$paginator->setItemCountPerPage($rec_counts);
		$paginator->setCurrentPageNumber($page);
		$paginator->setDefaultPageRange(8);
		
		$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'pagination-control.phtml');
		$this->view->paginationControl=$paginationControl;
		$this->view->paginator=$paginator;
		
		
	

	}
// End SP my Job


// all client notifications
	public function receivedquoteAction(){
		
// show all job request after his registration
		$where = "notification_reciver='".$this->view->user->user_id."' and notification_type='2'";
		$changeStatus['notification_status']=1; 
		$change_norificationStatus = $this->modelsuper->Super_Insert("notifications",$changeStatus,$where);
		
		$extra_sender= array(
						/*'fields'=> array('notification_date','notification_message'),*/
						'order'=>'notification_date desc',
						'group'=> 'notification_id',
						'pagination'=>1,
					   );
		$joinArr = array(
			'0'=>array("users","user_id=notification_sender","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
			'1'=>array("job","job_id=notification_job_id && job_enable_disable=1","full",array('job_posted_by','posted_job_created','job_all_data')),
			'2'=>array("proposal","p_job_id=notification_job_id","left",array('proposal_description','proposal_credit','totalProposal'=> new Zend_Db_Expr('ifnull(count(DISTINCT(proposal_id)),0)'))),
			'3'=>array("communication","c_job_id=notification_job_id && c_sender_id=notification_sender","left",array('totalMsz'=> new Zend_Db_Expr('ifnull(count(DISTINCT(communication_id)),0)'))),
			);
		$messages=$this->modelStatic->Super_Get("notifications","notification_reciver='".$this->view->user->user_id."'","fetchAll",$extra_sender,$joinArr);
		$this->view->allmassages = $messages;
		
		$adapter = new Zend_Paginator_Adapter_DbSelect($messages);
			$paginator = new Zend_Paginator($adapter);
			$page = $this->_getParam('page',1);
			$rec_counts = 10; //Item per page
			
			$paginator->setItemCountPerPage($rec_counts);
			$paginator->setCurrentPageNumber($page);
			$paginator->setDefaultPageRange(8);
			
			$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'pagination-control.phtml');
			$this->view->paginationControl=$paginationControl;
			$this->view->paginator=$paginator;
		
	//prd($massages);
		}	
// end client all notifications	

	
// start sendquote
		public function sendquoteAction(){
				global $objSession;
		$form = new Application_Form_ProjectForm();
		$form->proposal();
		$this->view->form = $form;
		
		$formCreditCard = new Application_Form_PackageForm();
		$formCreditCard->creditcardform();
		$this->view->formCreditCard=$formCreditCard;
			
		$job_id =$this->getRequest()->getParam("job_id");
		$this->view->job_id = $job_id;
		
// change job seen notification status		
		$where = "notification_reciver='".$this->view->user->user_id."' and (notification_type='1' || notification_type='3' || notification_type='4') and notification_job_id='".$job_id."'";
		$changeStatus['notification_status']=1; 
		$change_norificationStatus = $this->modelsuper->Super_Insert("notifications",$changeStatus,$where);
		
// rajshree
		$check_already_send=$this->modelStatic->Super_Get('proposal','p_job_id="'.$job_id.'" and sender_id="'.$this->view->user->user_id.'" ',"fetchAll");
		if(!empty($check_already_send)){
			//$objSession->errorMsg = " You have already sent request for this service proposal";
			$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$job_id),"front_view_quotemessage");
		}
		
		$extra = array();
		$joinArr = array(
			'0'=>array("users","user_id=job_posted_by","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image','user_city','user_id','user_state','user_zip_code','user_image')),
			'1'=>array("services","service_id=job_cat_id","left",array('service_price','service_name')),
			);
		$bid=$this->modelStatic->Super_Get("job","job_id='".$job_id."'","fetch",$extra,$joinArr);
		$this->view->bid = $bid;
		//prn($bid);
		$all_data=unserialize($bid['job_all_data']);
		//prd($all_data);
		$ClientPhoneNumber = $all_data['job_phone_client'];
		
		$city = $all_data['client_zip_code'];
		$get_customer_City=$this->modelStatic->Super_Get("zips","zip='".$city."'","fetch");
		$this->view->customer_City = $get_customer_City;
		
		$Credit_IN_wallet = $this->modelProject->getcredits($this->view->user->user_id);
		$this->view->wallet = $Credit_IN_wallet;
		
		$extra_send_by= array();
		$joinArr_send_by= array(
			'0'=> array("users","user_id=sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
		);
		$all_proposal_sender=$this->modelStatic->Super_Get("proposal","proposal.p_job_id='".$job_id."'","fetchAll",$extra_send_by,$joinArr_send_by);
		$this->view->all_proposal_sender = $all_proposal_sender;
		
		
		
//add proposal to table
		if($this->getRequest()->isPost()){			
			$posted_data = $this->getRequest()->getPost();
			if($form->isValid($posted_data)){ 
				$data_to_insert = $form->getValues();
				$proposal['proposal_description']=$data_to_insert['proposal_description'];
				$proposal['p_job_id']= $job_id;
				$proposal['sender_id']= $this->view->user->user_id;
				$proposal['receiver_id']= $bid['user_id'];
				$proposal['proposal_credit']=$data_to_insert['proposal_credit'];
				$proposal['proposal_status']=0;
				$proposal['proposal_decline_status']=0;
				$proposal['proposal_date'] = date('Y-m-d H:i:s');
				$proposal['proposal_casa_revenue'] = $bid['service_price']*$this->view->site_configs['per_bid_credit'];
				
			//	echo $IsSendMsz; die;
				
				$send_proposal = $this->modelsuper->Super_Insert("proposal",$proposal);
				if(is_object($send_proposal) and $send_proposal->success){
				// minus credits from total credits
				//prn($send_proposal);
						$insert_data['pp_request_by']=$this->view->user->user_id;
						$insert_data['pp_request_date']=date("Y-m-d");
						$insert_data['pp_pay_status']=0;
						$insert_data['package_desc']="You have send proposal";
						$insert_data['pp_amount_paid']='0';
						$insert_data['pp_title']='none';
						$insert_data['package_id']='00';
						$insert_data['package_points']=$bid['service_price'];
						$insert_data['pp_payment_method']="Authorize.net";
					//	prn($insert_data);
						$Debited_val=$this->modelsuper->Super_Insert("package_purchased",$insert_data);
					//	prn($Debited_val);
				// end minus credits 	
					
				// save data in notification table
								$notifi['notification_job_id']= $job_id;
								$notifi['notification_sender']= $this->view->user->user_id;
								$notifi['notification_reciver']= $bid['user_id'] ;
								$notifi['notification_date']=date('Y-m-d H:i:s');
								$notifi['notification_message']="Proposal arrived";
								$notifi['notification_type']=2;
						//		prn($notifi);
								$send_notification = $this->modelsuper->Super_Insert("notifications",$notifi);
								
						//		prd($send_notification);
					//end saved data in notification table	
//send msz by phone
				if($all_data['how_receive_quote']=='1'){
				$IsSendMsz = sendsms($ClientPhoneNumber,$proposal['proposal_description']);
				}
				$modelEmail = new Application_Model_Email();
				//get details to send email
				$extraMail = array();
				$joinMail = array(
					'0'=>array("users as client","client.user_id=job_posted_by","left",array('CONCAT(client.user_first_name," ",client.user_last_name) as Client_name','client.user_email as client_email_id')),
					'1'=>array("services","service_id=job_cat_id","left",array('service_price','service_name')),
					'2'=>array("users as serviceProvider","serviceProvider.user_id='".$this->view->user->user_id."'","left",array('CONCAT(serviceProvider.user_first_name," ",serviceProvider.user_last_name) as ServiceProvider_name')),
					);
				$EmailDetails=$this->modelStatic->Super_Get("job","job_id='".$job_id."'","fetch",$extraMail,$joinMail);
				// end get detail for email
				
				$is_send_Mail =  $modelEmail->sendEmail("proposal_received",$EmailDetails);
				//prd($is_send_Mail);
// end send msz by phone
					
					$objSession->successMsg = " Proposal submitted Successfully ";
					$this->_redirect('Quote-Messages/'.$job_id);
				}
				else{	
						$objSession->errorMsg = " Please Check Information Again..! ";
					}
			}
				$this->_redirect('Quote-Messages/'.$job_id);
				}
		}	
// end sendquote	

// view proposal
	public function viewproposalAction(){
			//$job_id =$this->getRequest()->getParam("job_id");
//			$this->view->job_id = $job_id;
//			$extra = array('group'=>'job_id');
//			$joinArr = array(
//			'0'=>array("proposal","proposal.p_job_id=job.job_id","left",array('proposal_description','proposal_credit')),
//			'1'=>array("services as t1","t1.service_id=posted_job_sub_category","right",array('t1.service_name as sub_cat_name')),
//			'2'=>array("services as t2","t2.service_sub_parent_id=posted_job_category","right",array('t2.service_name as category_name')),
//			'3'=>array("services as t3","t3.service_parent_id=posted_job_service","right",array('t3.service_name as service')),
//			);
//			$view_proposal=$this->modelStatic->Super_Get("job","job.job_id='".$job_id."'","fetchAll",$extra,$joinArr);
//			$this->view->view_proposal = $view_proposal;
//			//prd($view_proposal);
//			$extra_send_by= array();
//			$joinArr_send_by= array(
//				'0'=> array("users","user_id=sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
//			);
//			$all_proposal_sender=$this->modelStatic->Super_Get("proposal","proposal.p_job_id='".$job_id."'","fetchAll",$extra_send_by,$joinArr_send_by);
//			//prd($all_proposal_sender);
//			$this->view->all_proposal_sender = $all_proposal_sender;
		}	
		
	public function quotemessageAction(){
			   global $objSession; 
			$form = new Application_Form_ProjectForm();
			$form->communication();
			$this->view->form = $form;
		
			$job_id =$this->getRequest()->getParam("job_id");
			$this->view->job_id = $job_id;
			$extra = array('group'=>'job_id');
			$joinArr = array(
			'0'=>array("proposal","proposal.p_job_id=job.job_id && sender_id='".$this->view->user->user_id."'","left",array('proposal_description','proposal_credit','proposal_id','receiver_id as proposal_receiver','sender_id as proposal_sender','proposal_date','proposal_status')),
			'1'=> array("users","user_id=job_posted_by","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image','user_state','user_city')),
			'2'=>array("services as t1","t1.service_id=job_cat_id","left",array('t1.service_name as Job_type','service_price')),
			/*'3'=>array("job_details","detail_job_id=job_id","left",array('job_cat_form_id','job_cat_form_option')),*/
			);
			$view_job=$this->modelStatic->Super_Get("job","job.job_id='".$job_id."'","fetch",$extra,$joinArr);
			$this->view->view_job = $view_job;
	//	prd($view_job);
			
			$extra_send_by= array();
			$joinArr_send_by= array(
				'0'=> array("users","user_id=sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
			);
			$all_proposal_sender=$this->modelStatic->Super_Get("proposal","proposal.p_job_id='".$job_id."'","fetchAll",$extra_send_by,$joinArr_send_by);
			$this->view->all_proposal_sender = $all_proposal_sender;
			
			
			//$ifReceiveCnfmMsz = $this->modelStatic->Super_Get("communication","c_receiver_id= '".$this->view->user->user_id."' && c_job_id='".$job_id."'","fetchAll");
		//	$this->view->ifReceiveCnfmMsz = $ifReceiveCnfmMsz;

			
		}	
// end view proposal	
	
// quoterequest requests....................................	
	public function quoterequestAction(){
		   global $objSession; 
		$job_id =$this->getRequest()->getParam("job_id");
		$this->view->job_id = $job_id;
		
		$quote_senderID = $this->getRequest()->getParam("quote_sender");
		$this->view->quote_senderID = $quote_senderID;
		
		//$NoticJobID = $this->getRequest()->getParam('job_id');
	//	$this->view->NoticeForJob = $NoticJobID;
		
		$form2 = new Application_Form_ProjectForm();
		$form2->moreQuoteClient();
		$this->view->form2=$form2;

		
		
		$form1 = new Application_Form_ProjectForm();
		$form1->hireSP();
		$this->view->form1=$form1;
		

	    $form = new Application_Form_ProjectForm();
		$form->communication();
		$this->view->form = $form;
		
			$extra= array(
				);
			$join = array(
				'0'=>array("users","user_id=sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_first_name','user_image','user_state','user_city','user_zip_code','user_phone')),
				);
			$proposal_received=$this->modelStatic->Super_Get("proposal","p_job_id='".$job_id."'","fetchAll",$extra,$join);		
			$this->view->proposal_received= $proposal_received;
			
			$extraJob = array();
			$joinJob =  array(
				'0'=>array("services","service_id=job_cat_id","left",array('service_name')),
				'1'=>array("users","user_id=job_posted_by","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
				);
			$job=$this->modelStatic->Super_Get("job","job_id='".$job_id."'","fetch",$extraJob,$joinJob);		
			$this->view->job= $job;
			
			$ifReceiveCnfmMsz = $this->modelStatic->Super_Get("communication","c_receiver_id= '".$this->view->user->user_id."' && c_job_id='".$job_id."'","fetchAll");
			$this->view->ifReceiveCnfmMsz = $ifReceiveCnfmMsz;

			
			
	// save data in communication table	
		if($this->getRequest()->isPost()){			
			$posted_data = $this->getRequest()->getPost();
			
				if($form->isValid($posted_data)){ 
				
				$data_to_insert = $form->getValues();
				$massages['c_job_id']=$job_id;
				$massages['c_sender_id']=$this->view->user->user_id;
				$massages['c_receiver_id']=$posted_data['c_receiver_id'];
				$massages['c_massage']=$data_to_insert['cmassage'];
				$massages['c_date']=date('Y-m-d H:i:s');
				$isInserted = $this->modelsuper->Super_Insert("communication",$massages);
				if(is_object($isInserted) and $isInserted->success){
							$objSession->successMsg = "message sended  ";
							$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$job_id),"front_viewquote");
						}
				}
				else{	
					$objSession->errorMsg = " Please Check Information Again..! ";
					}
				$this->_helper->getHelper("Redirector")->gotoRoute(array('job_id'=>$job_id),"front_viewquote");
		}
   }	
// end quoterequest 

// save message by ajax
	public function savemessageAction(){
		 $this->_helper->layout->disableLayout();
			if($this->getRequest()->isPost()){			
				$posted_data = $this->getRequest()->getPost();
					$massages['c_job_id']=$posted_data['job_id'];
					$massages['c_sender_id']=$this->view->user->user_id;
					$massages['c_receiver_id']=$posted_data['c_receiver_id'];
					$massages['c_massage']=$posted_data['cmassage'];
					$massages['c_date']=date('Y-m-d H:i:s');
					$is_semd_msz = $this->modelsuper->Super_Insert("communication",$massages);
					
					$extra= array(
						);
					$join = array(
								'0'=>array("users as receiver_user","receiver_user.user_id=c_receiver_id","left",array('CONCAT(receiver_user.user_first_name," ",receiver_user.user_last_name) as receiver_user_name','receiver_user.user_image as receiver_user_image'))
								);
					
					$Messages=$this->modelStatic->Super_Get("communication","communication_id='".$is_semd_msz->inserted_id."' and  c_sender_id='".$this->view->user->user_id."' and c_receiver_id= '".$posted_data['c_receiver_id']."'","fetch",$extra, $join);
					$this->view->Send_msz = $Messages;




			// send Message Notice
				$ChatNotice['notification_job_id']= $posted_data['job_id'];
				$ChatNotice['notification_sender']= $this->view->user->user_id;
				$ChatNotice['notification_reciver']=$posted_data['c_receiver_id'];
				$ChatNotice['notification_date']= date('Y-m-d H:i:s');
				$ChatNotice['notification_message']="New message";
				$ChatNotice['notification_type']="10";
				$ChatNotice['notification_status']="0";
				$ChatNotice['notification_main_status']="0";
				
				$SendChatNotice = $this->modelsuper->Super_Insert("notifications",$ChatNotice);



					
			}
			//exit();
		}
		
	public function refreshchatAction(){
			 $this->_helper->layout->disableLayout();
					
				$sender_id = $this->_getParam('user_id');	
				$job_id =  $this->_getParam('job_id');
				$last_msz =  $this->_getParam('last_msz_id');
				//prd($last_msz);
					
					$extra = array();
					$joinArr = array(
								'0'=>array("users","user_id=c_sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image as sender_image'))
								);
					
					$refresh_data = $this->modelStatic->Super_Get("communication","(c_sender_id='".$sender_id."' and c_receiver_id='".$this->view->user->user_id."')  and c_job_id='".$job_id."' and communication_id > '".$last_msz."' and c_message_read_status='0' ","fetchAll", $extra,$joinArr);
					if(count($refresh_data) >0){
							$this->modelStatic->Super_Insert("communication",array('c_message_read_status'=>'1'),"c_sender_id='".$sender_id."'  and c_job_id='".$job_id."' and communication_id > '".$last_msz."' and c_message_read_status='0'");
						}
					
					$this->view->refresh_data = $refresh_data;
		}
		
	public function packagepurchasehistoryAction(){
		$this->view->show = "wallet" ; 
		$purchased_packages=$this->modelStatic->Super_Get("package_purchased","pp_request_by='".$this->view->user->user_id."'","fetchAll");
		$this->view->package_history = $purchased_packages;
		$getWallet=$this->modelStatic->Super_Get("wallet","wallet_user_id='".$this->view->user->user_id."'","fetch",array("fields"=>"SUM(wallet_user_point) as total_points"));
		$this->view->wallet = $getWallet;
		}
		
	public function addnewprojectAction(){
		global $objSession;

		$form = new  Application_Form_ProjectForm();
		$form->addnewproject();
		$form->service_sub_parent_id->setRegisterInArrayValidator(false);	
		$form->service_id->setRegisterInArrayValidator(false);
// get page content		
		$pageContent=$this->modelsuper->Super_Get("Job_Page","1","fetchAll");
		$this->view->pageContent = $pageContent;
		
		$path_for_user=ROOT_PATH.'/public/resources/uploads/image_'.$this->view->user->user_id;
		if(!is_dir($path_for_user)){
					   mkdir($path_for_user,0755);
					   chmod($path_for_user,0777);
		}
		
		$thumbPath150=CLIENT_JOB_IMAGES_PATH.'/150';
		if(!is_dir($thumbPath150)){
				   mkdir($thumbPath150,0755);
				   chmod($thumbPath150,0777);
		}
		
				
		if($this->getRequest()->isPost()){			
		$posted_data = $this->getRequest()->getPost();

		$file_data_name=array();
		if(isset($posted_data['form_ID_and_Type']) &&  !empty($posted_data['form_ID_and_Type'])){
			foreach($posted_data['form_ID_and_Type'] as $key=>$value){
				if($value=='4' && isset($posted_data['add_service_image_'.$key]) && $posted_data['add_service_image_'.$key]=='1'){
					$path =ROOT_PATH.'/public/resources/uploads/image_'.$this->view->user->user_id.'/media_'.$key.'/';
					$files =@scandir($path);
					$array=array();
					foreach($files as $file){
						if($file!='.' && $file!='..' && ((strpos($file,"."))))
						{
							$newname=rand().$this->view->user->user_id.time().'.'.pathinfo($path.$file, PATHINFO_EXTENSION);
							rename($path.$file,CLIENT_JOB_IMAGES_PATH.'/'.$newname);
							$thumb_config = array("source_path"=>CLIENT_JOB_IMAGES_PATH,"name"=> $newname);
      						Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$thumbPath150,"crop"=>true ,"width"=>150,"height"=>150,"ratio"=>false)));
							array_push($array,$newname);
							
						}
					} 
					if(!empty($array)){
							$file_data_name = implode(",",$array);
					}
					
				}
				
			}
		}
		$checkTotalImage = count($file_data_name);
		if($checkTotalImage>0){
			$jobImages = $file_data_name;
			}else{
				$jobImages = "";
				}
						 
		$serilaized_data=serialize($posted_data);	
		//prn($posted_data);
if(isset($posted_data['form_ID_and_Type']) && count($posted_data['form_ID_and_Type'])>0 && !empty($posted_data['form_ID_and_Type']))
{
		$ques_id_arr=array();
		foreach($posted_data['form_ID_and_Type'] as $ques=>$form_ques_id)
		{
			$ques_id_arr[count($ques_id_arr)]=$ques;
		}
		$ques_id_arr_imp=implode(",",$ques_id_arr);
}

		
			if($posted_data!=''){
				
					$sender_location = $this->modelProject->userlocation($posted_data['client_zip_code']);
				
				$insertit['job_posted_by']= $this->view->user->user_id;
				$insertit['job_cat_id']=  $posted_data['Cat_id'];
				$insertit['posted_job_created']= date('Y-m-d H:i:s');
				$insertit['job_all_data']=$serilaized_data;
				$insertit['job_images']=$jobImages;
				
				$insertit['job_lng']=  $sender_location['lng'];
				$insertit['job_lat']=  $sender_location['lat'];

				$insertit['job_zip_code']=  $posted_data['client_zip_code'];
				if(isset($posted_data['form_ID_and_Type']) && count($posted_data['form_ID_and_Type'])>0 && !empty($posted_data['form_ID_and_Type']))
				{
				$insertit['job_ques_id']=  $ques_id_arr_imp;
				}
				
		//		prd($insertit);
				$isInserted_job = $this->modelsuper->Super_Insert("job",$insertit);
					//prd($isInserted_job);
					
				if(is_object($isInserted_job) and $isInserted_job->success){
											
											$distanceQuery = "(((acos(sin(".$sender_location['lat']." * PI() / 180) * sin(lat * PI() / 180) + cos(".$sender_location['lat']." * PI() / 180) * cos(lat * PI() / 180) * cos((".$sender_location['lng']."-lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515))";
											
									//	prn($distanceQuery);


											$extra_d = array('fields'=>array('*' ,'distance'=>new Zend_Db_Expr($distanceQuery)),'having'=>"distance<='".$this->view->site_configs['provider_distance_val']."'");
											
											$joinArr_d = array(
														'0'=>array("users","user_id=us_user_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_city','user_id')),
														'1'=>array("zips","zip_id=user_city","left",array('*')),
														);
					
											$receiver=$this->modelStatic->Super_Get("user_services","us_service_sub_parent_id='".$insertit['job_cat_id']."'","fetchAll",$extra_d,$joinArr_d);	
										
									
										
											foreach($receiver as  $key => $receiver1){
													$notifi['notification_job_id']= $isInserted_job->inserted_id;
													$notifi['notification_sender']= $this->view->user->user_id;
													$notifi['notification_reciver']= $receiver1['us_user_id'];
													$notifi['notification_date']= $insertit['posted_job_created'];
													$notifi['notification_message']="new job posted";
													$notifi['notification_type']=1;
													$send_notification = $this->modelsuper->Super_Insert("notifications",$notifi);
													//prn($send_notification);
												}
												
												
												$modelEmail = new Application_Model_Email();
										//get details to send email to Service Provider
											foreach($receiver as  $provider){
												
												$extraMail = array();
												$joinMail = array(
													'0'=>array("users as client","client.user_id='".$this->view->user->user_id."'","left",array('CONCAT(client.user_first_name," ",client.user_last_name) as Client_name','client.user_email as client_email_id')),
													'1'=>array("services","service_id='".$posted_data['Cat_id']."'","left",array('service_price','service_name')),
													'2'=>array("users as pro","pro.user_id='".$provider['us_user_id']."'","left",array('CONCAT(pro.user_first_name," ",pro.user_last_name) as ServiceProvider_name','pro.user_email as ServiceProvider_email_id')),
													);
												$EmailDetailsSP=$this->modelStatic->Super_Get("job","job_id='".$isInserted_job->inserted_id."'","fetch",$extraMail,$joinMail);
												$is_send_Mail_To_SP =  $modelEmail->sendEmail("job_request_send_email",$EmailDetailsSP);
											}
										// end get detail for email
										
										
							// saved data in notification table	
							
							
//get details to send email to client
				$extraMail = array();
				$joinMail = array(
					'0'=>array("users as client","client.user_id='".$this->view->user->user_id."'","left",array('CONCAT(client.user_first_name," ",client.user_last_name) as Client_name','client.user_email as client_email_id')),
					'1'=>array("services","service_id=job_cat_id","left",array('service_price','service_name')),
					);
				$EmailDetails=$this->modelStatic->Super_Get("job","job_id='".$isInserted_job->inserted_id."'","fetch",$extraMail,$joinMail);
				// end get detail for email
				
				$is_send_Mail =  $modelEmail->sendEmail("new_job_posted",$EmailDetails);
				//prd($is_send_Mail);
// end send msz by phone
							
							
							
							
							
							
						$objSession->successMsg = " job submitted Successfully ";
					$this->_redirect('project');
				}
			}
			else{	
					//prd($form->getMessages());
				$objSession->errorMsg = " Please Check Information Again..! ";
			}
			
			$this->_redirect('project');
		}
		$this->view->form = $form;
		
	}
	
	public function editprojectAction(){
		global $objSession;

		$form = new  Application_Form_ProjectForm();
		$form->addnewproject();
		$form->service_sub_parent_id->setRegisterInArrayValidator(false);	
		$form->service_id->setRegisterInArrayValidator(false);
// get page content		
		$pageContent=$this->modelsuper->Super_Get("Job_Page","1","fetchAll");
		$this->view->pageContent = $pageContent;

		
		$job_id = $this->getRequest()->getParam('job_id');
		$this->view->job_id = $job_id;
		
		
		$path_for_user=ROOT_PATH.'/public/resources/uploads/image_'.$this->view->user->user_id;
		if(!is_dir($path_for_user)){
					   mkdir($path_for_user,0755);
					   chmod($path_for_user,0777);
		}
		$thumbPath150=CLIENT_JOB_IMAGES_PATH.'/150';
		if(!is_dir($thumbPath150)){
				   mkdir($thumbPath150,0755);
				   chmod($thumbPath150,0777);
		}
		

		if(!empty($job_id) && $job_id!='\+d')
		{	
			$job_data=$this->modelsuper->Super_Get("job","job_id='".$job_id."'","fetch");
			$all_data=unserialize($job_data['job_all_data']);
			
			$this->view->all_data=$all_data;
			$form->populate($all_data);
			
			if($this->getRequest()->isPost()){		
			$posted_data = $this->getRequest()->getPost();
			
			$file_data_name=array();
			if(isset($posted_data['form_ID_and_Type']) &&  !empty($posted_data['form_ID_and_Type'])){
			foreach($posted_data['form_ID_and_Type'] as $key=>$value){
				if($value=='4' && isset($posted_data['add_service_image_'.$key]) && $posted_data['add_service_image_'.$key]=='1'){
					$path =ROOT_PATH.'/public/resources/uploads/image_'.$this->view->user->user_id.'/media_'.$key.'/';
					$files =@scandir($path);
					$array=array();
					foreach($files as $file){
						if($file!='.' && $file!='..' && ((strpos($file,"."))))
						{
							$newname=rand().$this->view->user->user_id.time().'.'.pathinfo($path.$file, PATHINFO_EXTENSION);
							rename($path.$file,CLIENT_JOB_IMAGES_PATH.'/'.$newname);
							$thumb_config = array("source_path"=>CLIENT_JOB_IMAGES_PATH,"name"=> $newname);
      						Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$thumbPath150,"crop"=>true ,"width"=>150,"height"=>150,"ratio"=>false)));
							array_push($array,$newname);
							
						}
					} 
					if(!empty($array)){
							$file_data_name = implode(",",$array);
					}
				}
				
			}
		}
			$checkTotalImage = count($file_data_name);
			if($checkTotalImage>0){
				$jobImages = $file_data_name;
				}else{
					$jobImages = "";
					}
			
			$serilaized_data=serialize($posted_data);
			//prd($posted_data);
			
if(isset($posted_data['form_ID_and_Type']) && count($posted_data['form_ID_and_Type'])>0 && !empty($posted_data['form_ID_and_Type']))
{
		$ques_id_arr=array();
		foreach($posted_data['form_ID_and_Type'] as $ques=>$form_ques_id)
		{
			$ques_id_arr[count($ques_id_arr)]=$ques;
		}
		$ques_id_arr_imp=implode(",",$ques_id_arr);
}


			if($posted_data!=''){
				
				$insertit['job_posted_by']= $this->view->user->user_id;
				$insertit['job_cat_id']=  $posted_data['Cat_id'];
				$insertit['posted_job_created']= date('Y-m-d H:i:s');
				$insertit['job_all_data']=$serilaized_data;
				$insertit['job_images']=$jobImages;
				
				$sender_location = $this->modelProject->userlocation($posted_data['client_zip_code']);
				
				$insertit['job_lng']=  $sender_location['lng'];
				$insertit['job_lat']=  $sender_location['lat'];

				$insertit['job_zip_code']=  $posted_data['client_zip_code'];
				if(isset($posted_data['form_ID_and_Type']) && count($posted_data['form_ID_and_Type'])>0 && !empty($posted_data['form_ID_and_Type']))
				{
				$insertit['job_ques_id']=  $ques_id_arr_imp;
				}

				$isUpdate_job = $this->modelsuper->Super_Insert("job",$insertit,"job_id='". $job_id."'");
				//prd($isUpdate_job);
				if(is_object($isUpdate_job) and $isUpdate_job->success){
						$objSession->successMsg = " job Updated Successfully ";
					$this->_redirect('project');
				}
			}
			else{	
					//prd($form->getMessages());
				$objSession->errorMsg = " Please Check Information Again..! ";
			}
			
			$this->_redirect('project');
		}
		}
		$this->view->form = $form;
	}
	


	
	public function postnewjobAction(){
		$this->_helper->layout->disableLayout();
		$service_id = $this->getRequest()->getParam('id');
		$this->view->service_id = $service_id;
		//prd($service_id);
		
		$FileTypeQues = $this->modelsuper->Super_Get("category_form","c_form_field_type='4' and category_id='".$service_id."'","fetchAll");
		$this->view->FileTypeQues = $FileTypeQues;
		//prn($FileTypeQues);
		
		$action_type = $this->getRequest()->getParam('pagetype');
		$this->view->action_type = $action_type;
		//prd($action_type);
		
		$job_id_EDIT =  $this->_getParam('job_id'); 
		$this->view->job_id_EDIT=$job_id_EDIT;
	//	prd($job_id_EDIT);
		$form = new  Application_Form_ProjectForm();
		$form->postnewjob();
		
		if($job_id_EDIT!='' && $job_id_EDIT!='undefined'){
			
			$job_data=$this->modelsuper->Super_Get("job","job_id='".$job_id_EDIT."'","fetch");
			$all_data=unserialize($job_data['job_all_data']);
			$this->view->job_data = $job_data;
		//prd($all_data);
			
			$this->view->all_data=$all_data;
			$form->populate($all_data);
		}
		
		
		$cat_form = $this->modelProject->getcategoryform($service_id);
		$this->view->category_form = $cat_form;
		
		$Cat_detail=$this->modelsuper->Super_Get("services","service_id='".$service_id."'","fetch");
		$this->view->Cat_detail = $Cat_detail;
		
		$this->view->form = $form;
	}
	
	
	
	public function paybycardAction(){	
 		global $objSession ; 
		$job_id = $this->getRequest()->getParam('job_id');
		$this->view->job_id=$job_id;
		//prd($job_id);

		$this->view->pageHeading = "Featured request";
		$this->view->show = "Featured request";
		
		$business_id=$this->_getParam('business_id');
		$this->view->business_id=$business_id;
		
		$form = new Application_Form_PackageForm();
		$form->creditcardform();
			
								 		
		if($this->getRequest()->isPost()){/* begin : isPost() */			
			$posted_data = $this->getRequest()->getPost();
			//,array("fields"=>"cp_price")
		//	prd($posted_data);
 			if($form->isValid($posted_data)){ /* Begin : isValid()  */
 				
				 $data = $form->getValues();
			
				   require_once ROOT_PATH.'/public/anet_php_samples/samples/anet_php_sdk/AuthorizeNet.php';
				  
				   $site_configs=$this->view->site_configs;
					//prd($this->view->site_configs);
				   define("AUTHORIZENET_API_LOGIN_ID",$site_configs['authrize_loginkey']);
				   define("AUTHORIZENET_TRANSACTION_KEY",$site_configs['authrize_tranctionkey']);
				   define("AUTHORIZENET_SANDBOX", true);
				   $sale = new AuthorizeNetAIM;
				   $sale->amount = $posted_data['payAmountByCard'];
				   $sale->card_num = $data['card_number'];
				   $sale->exp_date = str_pad($data['user_credit_card_expire_month'], 2, '0', STR_PAD_LEFT).''.$data['user_credit_card_expire_year'];
				   $sale->description = 'Featured request Payment';
				   $sale->card_code=$data['cvv'];
				 
				   // Set Invoice Number:
				   $sale->invoice_num = 'INVOICE_'.time();
				   $response = $sale->authorizeAndCapture();
					//prd($response);
					if($response->approved=="1")
					{ // insert values into package_purchased table
					
						$insert_data['pp_request_by']=$this->view->user->user_id;
						$insert_data['pp_request_date']=date("Y-m-d");
						$insert_data['pp_pay_status']=1;
						$insert_data['package_desc']="You had pay by card for send proposal.";
						$insert_data['pp_amount_paid']=$posted_data['payAmountByCard'];
						$insert_data['pp_title']="none";
						$insert_data['package_id']="0";
						$insert_data['package_points']=$posted_data['getCreditbyCardPayment'];
						$insert_data['pp_payment_method']="Authorize.net";
						$isInserted=$this->modelsuper->Super_Insert("package_purchased",$insert_data);
						
					}
					else
					{	
								$objSession->errorMsg = $response->response_reason_text;
								$this->_redirect("/sendQuote/".$job_id);
					}
					
						
					
				if(is_object($isInserted)){
					 
					 if($isInserted->success){
							$objSession->successMsg ="You can send proposal now.";
							$this->_redirect("/sendQuote/".$job_id);
					 }
					 
					

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
			
 		}/* end : isPost() */
		
		
		$this->view->form = $form;
	}	

// dropdown service category list
	public function categorydataAction()
		{
			$service_id = $this->getRequest()->getParam('id');
	
			$data=$this->modelsuper->Super_Get("services","service_sub_parent_id='0' and service_parent_id='".$service_id."'","fetchAll");
		
			$Optionvalue="";
			 if($data!="" and count($data)>0)
			 {
			  foreach($data as $key=>$value)
			  {
			  $Optionvalue.=$value['service_id']."|||".$value['service_name']."***";
			  }
			
			  $Optionvalue=substr($Optionvalue,0,strlen($Optionvalue)-3);
			  }
			   echo $Optionvalue;
		
				
			exit;
		}
		
// dropdown service sub category list
	public function subcategorydataAction()
		{		
			$category_id = $this->getRequest()->getParam('id');
		
			$data=$this->modelsuper->Super_Get("services","service_sub_parent_id='".$category_id."'","fetchAll");
		
			$Optionvalue="";
			 if($data!="" and count($data)>0)
			 {
			  foreach($data as $key=>$value)
			  {
			  $Optionvalue.=$value['service_id']."|||".$value['service_name']."***";
			  }
			
			  $Optionvalue=substr($Optionvalue,0,strlen($Optionvalue)-3);
			  }
			   echo $Optionvalue;
		
				
			exit;
		}
// zip code list
	public function zipcodelistAction()
		{	
			$input_zip =  $this->_getParam('inputZip'); 
			
			$zipcodedata=$this->modelsuper->Super_Get("zips","zip='".$input_zip."'","fetch");
			//prn($zipcodedata);
			if($zipcodedata!=''){
				echo "1";
				}
			else{
					echo "0";
				}	
		   exit();
			
		}

	public function companymediaAction()
	{
					$this->_helper->layout->disableLayout();
					$this->_helper->viewRenderer->setNoRender(true);
					$options = array();
					//prd(HTTP_UPLOADS_PATH);
					if(isset($_GET['file']) && $_GET['file'] != ""){
					
					
					}
					$options['script_url'] = SITE_HTTP_URL."/project/companymedia";
					$path=ROOT_PATH."/public/resources/uploads/image_".$this->view->user->user_id."/";
					  if(!is_dir($path))
					  {//prd($path);
						  mkdir($path,0755);
						  chmod($path,0777);
					  }
		
		
				
					$options['upload_dir'] = $path;
					$options['upload_url'] = SITE_HTTP_URL."/public/resources/uploads/image_".$this->view->user->user_id."/";
					$imageUpload = new Application_Plugin_UploadHandler($options);
					exit;
	}
	
	public function companymedia2Action()
	{
					$qa_id=$this->getRequest()->getParam('qa_id');
					
					$this->_helper->layout->disableLayout();
					$this->_helper->viewRenderer->setNoRender(true);
					$options = array();
					//prd(HTTP_UPLOADS_PATH);
					if(isset($_GET['file']) && $_GET['file'] != ""){
					
					
					}
					$options['script_url'] = SITE_HTTP_URL."/project/companymedia/qa_id/".$qa_id;
					$path=ROOT_PATH."/public/resources/uploads/image_".$this->view->user->user_id."/media_".$qa_id.'/';
					  if(!is_dir($path))
					  {//prd($path);
						  mkdir($path,0755);
						  chmod($path,0777);
					  }
		
		
				
					$options['upload_dir'] = $path;
					$options['upload_url'] = SITE_HTTP_URL."/public/resources/uploads/image_".$this->view->user->user_id."/media_".$qa_id.'/';
					
					
					$imageUpload = new Application_Plugin_UploadHandler($options);
					
					exit;
	}
	
	// view pro profile
	public function providerprofileAAWorkAction(){
			global $objSession ; 
			$user_id =$this->getRequest()->getParam("user_id");
			
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
			
			$extraRWork= array(
							'fields'=>array('p_job_id')
					   );			   
			$joinRWork = array(
				'0'=>array("job","job_id=p_job_id","left",array('job_status','job_cat_id','job_complete_on')),
				'1'=>array("services","service_id=job_cat_id","left",array('service_name')),
				);
			$ProviderRecentWork=$this->modelStatic->Super_Get("proposal","proposal.sender_id='".$user_id."' && proposal_decline_status='3'","fetchAll",$extraRWork,$joinRWork);
			$this->view->ProviderRecentWork = $ProviderRecentWork;			
		}
// end view pro profile

		
		
}