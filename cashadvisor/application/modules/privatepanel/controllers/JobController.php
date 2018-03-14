<?php
class Privatepanel_JobController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelStatic  = new Application_Model_Static();
		$this->view->pageIcon = "fa  fa-users";
		$this->modelsuper = new Application_Model_SuperModel();
		$this->modelProject = new Application_Model_Project();
    }
 	
	
	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage Jobs";
		$this->view->pageDescription = "manage jobs";
		if(isset($_GET['noticeId'])){
		$GetNoticeJobId = $_GET['noticeId'];
			if($GetNoticeJobId!=''){
				$where1 = "notification_job_id='".$GetNoticeJobId."'";
				$changeStatus1['notification_status']=1; 
				$change_job_reminder_status = $this->modelsuper->Super_Insert("notifications",$changeStatus1,$where1);
			}
		}
 	}
	
	public function allowmorequoteAction(){
		$this->_helper->layout->disableLayout();
		$job_id = $this->getRequest()->getParam('job_id');
		//prd($job_id);
		
		$form2 = new Application_Form_ProjectForm();
		$form2->moreQuoteAdmin();
		$this->view->form2=$form2;

		
		$extra_send_by= array('fields'=>array('totalProposal'=> new Zend_Db_Expr('ifnull(count(DISTINCT(proposal_id)),0)')));
		$joinArr_send_by= array(
			'0'=> array("users","user_id=receiver_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_id')),
			'1'=>array("job","job_id=p_job_id","left",array('job_cat_id','job_id')),
			'2'=>array("services","service_id=job_cat_id","left",array('service_name')),
			'3'=>array("more_quote","more_quote_job_id=p_job_id","left",array('more_quote_client_request')),
			'4'=>array("notifications","notification_job_id=p_job_id && notification_type='1'","left",array('totalSpReceivedNotice'=> new Zend_Db_Expr('ifnull(count(DISTINCT(notification_id)),0)'))),
		);
		$ProposalReceived=$this->modelsuper->Super_Get("proposal","proposal.p_job_id='".$job_id."'","fetch",$extra_send_by,$joinArr_send_by);
		$this->view->ProposalReceived = $ProposalReceived;
		
		}
// Accept More Quote Request		
	public function sendmorequoteAction(){
		global $objSession;
		
		$form2 = new Application_Form_ProjectForm();
		$form2->moreQuoteAdmin();
			
		if($this->getRequest()->isPost()){			
				$posted_data = $this->getRequest()->getPost();
			if($form2->isValid($posted_data)){ 
					
				// update more_quote table	
					$where = "more_quote_job_id='".$posted_data['allow_more_quote_job_id']."'";
					$changeStatus['more_quote_status']=1;
					$changeStatus['more_quote_admin_allowed']=$posted_data['more_quote_admin_allowed'];
					$change_more_quoteStatus = $this->modelsuper->Super_Insert("more_quote",$changeStatus,$where);
					
					if(is_object($change_more_quoteStatus) and $change_more_quoteStatus->success){
						
						// update job table  more quote field
							$whereJob = "job_id='".$posted_data['allow_more_quote_job_id']."'";
							$changeJobStatus['job_more_quote_status']=1;
							$changeJobMoreQuoteStauts = $this->modelsuper->Super_Insert("job",$changeJobStatus,$whereJob);
						
						// send notice to SP
								$job_id = $posted_data['allow_more_quote_job_id'];
								$AllreadySendNotice=$this->modelStatic->Super_Get("notifications","notification_job_id='".$job_id."' && notification_type='1'","fetchAll");
								$ProposalReceived=$this->modelStatic->Super_Get("proposal","p_job_id='".$job_id."'","fetchAll");
								$ProposalSenderIdArray = array_column($ProposalReceived,'sender_id');
								foreach($AllreadySendNotice as $NoticeReceiver){
										if(!in_array($NoticeReceiver['notification_reciver'],$ProposalSenderIdArray)){
							// save data in notification table
												$reminder['notification_job_id']= $job_id;
												$reminder['notification_sender']= $posted_data['moreQuoteRequestedBy'];
												$reminder['notification_reciver']= $NoticeReceiver['notification_reciver'];
												$reminder['notification_date']=date('Y-m-d H:i:s');
												$reminder['notification_message']="Reminder! New job posted";
												$reminder['notification_type']='4';
												$reminder['notification_status']='0';
												$reminder['notification_main_status']='0';
												
												$send_reminder = $this->modelsuper->Super_Insert("notifications",$reminder);
												//$lastIsertedId = $send_reminder->inserted_id;
							//end saved data in notification table	
											}
									}
						// end send notice to SP
						
						// send accept notice to client
								$AcceptNotice['notification_job_id']= $job_id;
								$AcceptNotice['notification_sender']= "1";
								$AcceptNotice['notification_reciver']= $posted_data['moreQuoteRequestedBy'];
								$AcceptNotice['notification_date']=date('Y-m-d H:i:s');
								$AcceptNotice['notification_message']="Accept more quote request";
								$AcceptNotice['notification_type']='6';
								$AcceptNotice['notification_status']='0';
								$AcceptNotice['notification_main_status']='0';
								
								$SentAcceptNotice = $this->modelsuper->Super_Insert("notifications",$AcceptNotice);
						// end send accept notice to client
							}
		   }
		}
		echo 'More Quotes are allowed successfully';
		exit();
		
	}
	
// Decline more Quote Request	
	public function decinemorequoteAction(){
		global $objSession;
			$Decline_job_id = $this->getRequest()->getParam('decline_job_id');
		//	prd($Decline_job_id);
				// update more_quote table	
					$where = "more_quote_job_id='".$Decline_job_id."'";
					$changeStatus['more_quote_status']=2;
					$changeStatus['more_quote_admin_allowed']='0';
					
					$change_more_quoteStatus = $this->modelsuper->Super_Insert("more_quote",$changeStatus,$where);
					
					if(is_object($change_more_quoteStatus) and $change_more_quoteStatus->success){
						
						// update job table  more quote field
							$whereJob = "job_id='".$Decline_job_id."'";
							$changeJobStatus['job_more_quote_status']=2;
							$changeJobMoreQuoteStauts = $this->modelsuper->Super_Insert("job",$changeJobStatus,$whereJob);
						//	prd($changeJobMoreQuoteStauts);
						
						
						$RequestedBy = $this->modelsuper->Super_Get("more_quote","more_quote_job_id='".$Decline_job_id."'","fetch");
						
						// send accept notice to client
								$DeclineNotice['notification_job_id']= $Decline_job_id;
								$DeclineNotice['notification_sender']= "1";
								$DeclineNotice['notification_reciver']= $RequestedBy['more_quote_sender'];
								$DeclineNotice['notification_date']=date('Y-m-d H:i:s');
								$DeclineNotice['notification_message']="Decline more quote request";
								$DeclineNotice['notification_type']='7';
								$DeclineNotice['notification_status']='0';
								$DeclineNotice['notification_main_status']='0';
								
								$SentDeclineNotice = $this->modelsuper->Super_Insert("notifications",$DeclineNotice);
							//	prd($SentDeclineNotice);
						// end send accept notice to client
						
							}
		exit();
		
	}
	
  	public function getjobsAction(){
		
		$noticeID =  $this->getRequest()->getParam('noticeId');
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('job_id','job_posted_by','job_all_data','job_cat_id','posted_job_created','job_more_quote_status','job_enable_disable');
		$sIndexColumn = 'job_id';
		$sTable = 'job';
 		
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
		$sOrder = "ORDER BY posted_job_created desc";
		$aColumns2 = array('job_id','user_name','service_name','posted_job_created','job_more_quote_status');
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					//$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
					//	($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
					$sOrder .= "".$aColumns2[ intval( $_GET['iSortCol_'.$i] ) ]." ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";

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
			//	$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR ";
				
				$sWhere .= "LOWER(".$aColumns[$i].") LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR LOWER(service_name) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR  LOWER(CONCAT(user_first_name,' ',user_last_name)) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR "; // NEW CODE
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
		
		
		/*if($sWhere== ""){
			$sWhere ="";
		}else{
			$sWhere.="";
		}*/
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." ,
					service_name, CONCAT(user_first_name,' ',user_last_name) as user_name
					FROM  $sTable 
				 join services on service_id = job_cat_id
				join users on user_id = job_posted_by
					$sWhere $sOrder $sLimit";
					//prd($sQuery);
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
			$row[]=$row1['user_name'];
			$row[]=$row1['service_name'];
			$status = $row1['job_enable_disable']!=1?"checked='checked'":" ";
						$row[]='<div class="danger-toggle-button">
									<input type="checkbox" class="toggle status-'.(int)$row1['job_enable_disable'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
								</div>';
					
					
			$row[]=$row1['posted_job_created'];
			
			$isRequest=$this->modelStatic->Super_Get("more_quote","more_quote_job_id='".$row1['job_id']."' && more_quote_status='0'","fetch");
			
			
			if($row1['job_more_quote_status']=='0'){
				if($isRequest!=''){
					 $row[] = '<div id="AccDeclineDiv_'.$row1['job_id'].'">
									<button class="btn btn-xs red" onclick="moreQuoteDecline('.$row1['job_id'].')" type="button">Decline</button>
									<button class="btn btn-xs yellow" type="button" onclick="moreQuoteAllowed('.$row1['job_id'].')">Accept</button>
								</div>';
				}else{
					 $row[] =	 '<p>No any Request</p>';
					}
			}elseif($row1['job_more_quote_status']=='1'){
				 $row[] =	 '<p>Accepted</p>';
			}else{
				 $row[] =  '<p>Declined</p>';	
			}
						
			
								
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/job/viewjob?job_id='.$row1['job_id'].'" class="btn btn-xs green"> View <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	public function viewjobAction(){
		$job_id = $this->getRequest()->getParam('job_id');
			$extra= array(
						);
		$joinArr = array(
					'0'=>array("services","service_id=job_cat_id","left",array('service_name')),
					'1'=>array("users","user_id=job_posted_by","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image','user_id as ClientId')),
					'2'=>array("reviews","review_job_id=job_id","left",array('*')),
					);
		$getjob = $this->modelsuper->Super_Get('job','job_id="'.$job_id.'"',"fetch",$extra,$joinArr);
		$this->view->getjob = $getjob;
	//prd($getjob);
		$all_data=unserialize($getjob['job_all_data']);
		$this->view->all_data=$all_data;
		
			$extra_send_by= array();
			$joinArr_send_by= array(
				'0'=> array("users","user_id=sender_id","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
			);
			$all_proposal_sender=$this->modelStatic->Super_Get("proposal","proposal.p_job_id='".$job_id."'","fetchAll",$extra_send_by,$joinArr_send_by);
			$this->view->all_proposal_sender = $all_proposal_sender;
			
/*			$extraReview = array();
			$joinReview = array(
						'0'=>array("users","user_id=review_client","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
			);
			$ReviewData=$this->modelStatic->Super_Get("reviews","review_sp='".$user_id."'","fetchAll",$extraReview,$joinReview);
			$this->view->ReviewData = $ReviewData;
*/
		}
		
	public function editreviewAction(){
			$formData = $this->getRequest()->getPost();
			if ($this->getRequest()->isPost()){
				$data=array();
				$data['review_title']=$formData['review_title'];
				$data['review_msz']=$formData['review_msz'];
				$data['review_reply_msz'] =$formData['review_reply_msz'];
				$isInserted = $this->modelStatic->Super_Insert("reviews",$data,'review_id="'.$formData['review_id'].'"');
				if($isInserted->success){
					$Updated = $this->modelStatic->Super_Get("reviews",'review_id="'.$formData['review_id'].'"','fetch');
					print_r(json_encode($Updated));
				}else{
					echo "0";
				}
			}else{
				echo '0';
			}
			exit();
		}
		
	public function removereviewAction(){
			global $mySession;
			$review_id=$this->_getParam('review_id');
			$review_job_id=$this->_getParam('review_job_id');
			$Updated = $this->modelStatic->Super_Delete("reviews",'review_id="'.$review_id.'"');
			$Updated_job = $this->modelStatic->Super_Insert("job",array('job_review_status'=>'0'),'job_id="'.$review_job_id.'"');
			if($Updated_job->success){
				echo '1';
				$mySession->successMsg = "Review removed successfully.";	
			}else{
				echo '0';
				$mySession->errorMsg = "Error found.";	
			}
			exit();
		}
	
		
/*Get Job Page Contant*/
	public function jobpageAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage Job Page Content";
		$this->view->pageDescription = "Manage Job Page Content";
		$this->view->pageIcon = "fa fa-sitemap";
		
 	}

	public function getjobpageAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('job_page_id','job_page_how_icon','job_page_how_step_heading','job_page_step_desc');
		$sIndexColumn = 'job_page_id';
		$sTable = 'Job_Page';
 		
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
			$row[]='<img src="'.HTTP_JOB_PAGE_ICON_IMAGES_PATH.'/60/'.$row1['job_page_how_icon'].'" />';
			$row[]=$row1['job_page_how_step_heading'];
			$row[]=$row1['job_page_step_desc'];
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/job/editjobpage?job_page_id='.$row1['job_page_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	public function addjobpageAction(){

				global $mySession; 
				$this->view->pageHeading = "Add Job Page Content";
				$this->view->pageDescription = "add Job Page Content";
				$this->view->pageIcon = "fa fa-sitemap";
				$form = new Application_Form_ProjectForm();
				$form->jobpage();
				$form->job_page_how_icon->setRequired(true);
				$job_page_id = $this->getRequest()->getParam('job_page_id');
				if($job_page_id!='' && $job_page_id!='/d+'){
					$pageData = $this->modelStatic->Super_Get('Job_Page',"job_page_id='".$job_page_id."'",'fetch');
					if(!($pageData)){ 
						$mySession->infoMsg = "No Such Page Content Exists in the database...!";
						$this->_redirect('/privatepanel/job/jobpage');
					}
				
				}
				
				// prd($form);
				
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
					//	prd($data_to_insert);
				
						$is_uploaded = $this->_handle_job_icon_image();
						if($is_uploaded!=''){
							$data_to_insert['job_page_how_icon'] = $is_uploaded;
							//prn($data_to_insert);
						}
							$is_insert = $this->modelStatic->add("Job_Page",$data_to_insert);
							$mySession->successMsg  = "Page Content successfully added ";
						if($is_insert->success){
							$this->_redirect("privatepanel/job/jobpage");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
			
	 
	 
	 }

	
	public function editjobpageAction(){

				global $mySession; 
				$job_page_id = $this->getRequest()->getParam('job_page_id');
	
				$this->view->pageHeading = "Edit Job Page Content";
				
				$this->view->pageDescription = "Job Page Content";
				
				$this->view->pageIcon = "fa fa-file-tex";
				 
				$form = new Application_Form_ProjectForm();
				
				$form->jobpage();
				$form->job_page_how_icon->setRequired(false);

				
					$pageData = $this->modelStatic->Super_Get('Job_Page',"job_page_id='".$job_page_id."'",'fetch');
					$this->view->job_page_how_icon = $pageData['job_page_how_icon'];
					if(!($pageData)){ 
						$mySession->infoMsg = "No Such page Exists in the database...!";
						$this->_redirect('/privatepanel/job/jobpage');
					}
					else 
						$form->populate($pageData);
			
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
// edit job icon image						
						if(isset($_FILES['job_page_how_icon']['name']) && !empty($_FILES['job_page_how_icon']['name']))
						{// echo "image selectecd";
							 unlink(JOB_PAGE_ICON_IMAGES_PATH."/".$pageData['job_page_how_icon']);
							  unlink(JOB_PAGE_ICON_IMAGES_PATH."/60/".$pageData['job_page_how_icon']);
							 $is_uploaded = $this->_handle_job_icon_image();
							 $data_to_insert['job_page_how_icon'] = $is_uploaded;
							 //prd($is_uploaded);
							
						}
						else
						{	//echo "image not  selectecd";
							 $is_uploaded = $pageData['job_page_how_icon'];
							 $data_to_insert['job_page_how_icon'] = $is_uploaded;
							  //prn($is_uploaded);
						}
// end edit job icon image						
							$is_insert = $this->modelStatic->add("Job_Page",$data_to_insert,"job_page_id='".$data_to_insert['job_page_id']."'");
						
							$mySession->successMsg  = "Page Content updated successfully";
						
						if($is_insert->success){
							$this->_redirect("privatepanel/job/jobpage");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	 }
	 
	public function  removejobpageAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['Job_Page']) && count($formData['Job_Page'])) {
			$Job_Page_Content = implode(",",$formData['Job_Page']) ;
  			$removed = $this->modelUser->getAdapter()->delete('Job_Page',"job_page_id IN (".$Job_Page_Content.")");
 			$mySession->successMsg="Job Page Content Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/job/jobpage");
 	}

	public function editprojectmainpageAction(){

				global $mySession; 
					
				$this->view->pageHeading = "Page Description";
				
				$this->view->pageDescription = "Page Description";
				
				$this->view->pageIcon = "fa fa-file-tex";
				 
				$form = new Application_Form_ProjectForm();
				
				$form->projectpagedesc();
					$pageData = $this->modelStatic->Super_Get('Project_Page',"1",'fetch');
					if(!($pageData)){ 
						$mySession->infoMsg = "No Such page Exists in the database...!";
						$this->_redirect('/privatepanel/job/editprojectmainpage');
					}
					else 
						$form->populate($pageData);
			
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
							$is_insert = $this->modelStatic->add("Project_Page",$data_to_insert,"project_page_id='".$data_to_insert['project_page_id']."'");
						
							$mySession->successMsg  = "Page Description updated successfully";
						
						if($is_insert->success){
							$this->_redirect("privatepanel/job/editprojectmainpage");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	 
	 }

	 
/*handle_job_icon_image*/

	private function _handle_job_icon_image(){
		
 		$adapter = new Zend_File_Transfer_Adapter_Http();
		$image = $adapter->getFileInfo('job_page_how_icon');
   		$video_extension = $image['job_page_how_icon']['name'];
 		$extension = explode('.',$image['job_page_how_icon']['name']); 
 		$extension = array_pop($extension);
  		$name_for_image = md5(rand(1,999).time()).".".$extension;
  		rename(JOB_PAGE_ICON_IMAGES_PATH .'/'.$video_extension ,  JOB_PAGE_ICON_IMAGES_PATH .'/'.$name_for_image);
		
		$thumb_config = array("source_path"=>JOB_PAGE_ICON_IMAGES_PATH,"name"=> $name_for_image);
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>JOB_PAGE_ICON_IMAGES_PATH."/60","crop"=>true ,"width"=>60,"height"=>60,"ratio"=>false)));
		prn($name_for_image);
		return $name_for_image ;
		
  	}

	

}