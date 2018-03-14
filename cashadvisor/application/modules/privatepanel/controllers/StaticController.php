<?php
class Privatepanel_StaticController extends Zend_Controller_Action
{
 	private $admin = "" , $modelStatic;
	
    public function init(){ 
  		$this->modelStatic  = new Application_Model_Static();
  	}
 	
	
	public function siteconfigsAction(){
	
		global $mySession; 

		$this->view->pageHeading = " Site Configuration  ";
 		$this->view->pageDescription = "manage site configuration  ";
		
		
		$form = new Application_Form_StaticForm();
		$form->configuration();
		
   		if ($this->getRequest()->isPost()) {
			
 			$posted_data = $this->getRequest()->getPost();
			
			if($form->isValid($posted_data)){
				
				$data = $form->getValues();
				
				foreach($data as $key=>$values){
 					try{
						$this->modelStatic->getAdapter()->update('config',array('config_value'=>$values),"config_key='".$key."'");
					}catch(Zend_Exception $e){
 						 $mySession->errorMsg = $e->getMessage();
						 $this->render("index");
						 break;
					} 
				}
				
				$mySession->successMsg = " Site Configuration Successfully Updated ";
				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_site_configs");
   			}
		}
  		  $this->view->form = $form;
  		  $this->_helper->getHelper('viewRenderer')->renderScript('add.phtml');
    }
	
//about team member	
	
	public function aboutteamAction(){
		global $mySession;
		$this->view->pageHeading = "Manage About Team Member";
		$this->view->pageDescription = "manage about team member";
		$this->view->pageIcon = "fa fa-user";
	}
	
	public function getaboutteamAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('ateam_member_id','ateam_member_id','ateam_member_name','ateam_member_about','ateam_member_image');
		$sIndexColumn = 'ateam_member_id';
		$sTable = 'about_team_member';
 		
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
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				//	$sOrder .= "".$aColumns2[ intval( $_GET['iSortCol_'.$i] ) ]." ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
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
		
		
		if($sWhere== ""){
			$sWhere =" WHERE 1  ";
		}else{
			$sWhere.=" AND 1 ";
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
			$row[]='<img src="'.HTTP_TEAM_MEMBER_IMAGES_PATH.'/60/'.$row1['ateam_member_image'].'" />';
			$row[]=$row1['ateam_member_name'];
			$row[]=$row1['ateam_member_about'];
			
			$row[] ='<a href="'.SITE_HTTP_URL.'/privatepanel/static/addaboutteam?ateam_member_id='.$row1['ateam_member_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
  			$output['aaData'][] = $row;
			$j++;
		}
		echo json_encode( $output );
		exit();
  	}

	public function addaboutteamAction(){
				global $mySession; 
				
				// create team image folder
				$path_for_team_images=ROOT_PATH.'/public/resources/about_team_member';
				if(!is_dir($path_for_team_images)){
							   mkdir($path_for_team_images,0755);
							   chmod($path_for_team_images,0777);
							   
							    mkdir($path_for_team_images/60,0755);
							   chmod($path_for_team_images/60,0777);
				}
				
				$form = new Application_Form_StaticForm();
				
				$form->aboutteam();
				$TeamMemberID = $this->getRequest()->getParam('ateam_member_id');
				$this->view->TeamMemberID = $TeamMemberID;
				
				if($TeamMemberID==''){
						 $form->ateam_member_image->setRequired(true);
						$AddEdit = "Add";
					}else{
						$AddEdit = "Edit";
						}
										
				 $this->view->pageHeading = $AddEdit." Team Member";
				 $this->view->pageDescription = $AddEdit." Team Member";
				 $this->view->pageIcon = "fa fa-tags";
				
				if($TeamMemberID!='' && $TeamMemberID!='/d+'){
					$AboutTeamData = $this->modelStatic->Super_Get('about_team_member',"ateam_member_id='".$TeamMemberID."'",'fetch');
					$this->view->ateam_member_image = $AboutTeamData['ateam_member_image'];
					if(!($AboutTeamData)){ 
						$mySession->infoMsg = "No Such about Step  Exists in the database...!";
						$this->_redirect('/privatepanel/static/aboutteam');
					}
					else {
						$form->populate($AboutTeamData);
					}
				}
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
						$SaveData['ateam_member_name'] = $data_to_insert['ateam_member_name'];
						$SaveData['ateam_member_about'] = $data_to_insert['ateam_member_about'];
						
						if(!empty($AboutTeamData)){
							
								if(isset($_FILES['ateam_member_image']['name']) && !empty($_FILES['ateam_member_image']['name']))
								{
									 unlink(TEAM_MEMBER_IMAGES_PATH."/".$AboutTeamData['ateam_member_image']);
									  unlink(TEAM_MEMBER_IMAGES_PATH."/60/".$AboutTeamData['ateam_member_image']);
									 $is_uploaded = $this->_handle_team_image();
									 $SaveData['ateam_member_image'] = $is_uploaded;
									
								}
								else
								{	
									 $is_uploaded = $AboutTeamData['ateam_member_image'];
									 $SaveData['ateam_member_image'] = $is_uploaded;
								}
							
							$is_insert = $this->modelStatic->Super_Insert("about_team_member",$SaveData,"ateam_member_id='".$TeamMemberID."'");
							$mySession->successMsg  = "About Step successfully updated ";
							
						}else{
							
							$is_uploaded = $this->_handle_team_image();
								if($is_uploaded!=''){
									$SaveData['ateam_member_image'] = $is_uploaded;
								}
							
							$is_insert = $this->modelStatic->Super_Insert("about_team_member",$SaveData);
							
							$mySession->successMsg  = "Team member successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/static/aboutteam");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
				
	 
	 
	 }

	public function  removeaboutteamAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['about_team_member']) && count($formData['about_team_member'])) {
			$about_team_member = implode(",",$formData['about_team_member']) ;
  			$removed = $this->modelStatic->getAdapter()->delete('about_team_member',"ateam_member_id IN (".$about_team_member.")");
 			$mySession->successMsg="Team Member Deleted Successfully";
 		}
  		$this->_redirect("privatepanel/static/aboutteam");
 	}
	
	private function _handle_team_image(){
		$adapter = new Zend_File_Transfer_Adapter_Http();
		$image = $adapter->getFileInfo('ateam_member_image');
		
		$video_extension = $image['ateam_member_image']['name'];
		$extension = explode('.',$image['ateam_member_image']['name']); 
		$extension = array_pop($extension);
		$name_for_image = md5(rand(1,999).time()).".".$extension;
		
		rename(TEAM_MEMBER_IMAGES_PATH .'/'.$video_extension ,  TEAM_MEMBER_IMAGES_PATH .'/'.$name_for_image);
		
		$source = TEAM_MEMBER_IMAGES_PATH.'/'.$name_for_image;
		$newName = "Img_".time()."-".rand(1,100000).".".$extension;
		$destination = TEAM_MEMBER_IMAGES_PATH.'/'.$newName;
		$d = compress($source,$destination,70);
		//prd($source);
		unlink($source);
		
		// save final new named img in folder
		$thumb_config = array("source_path"=>TEAM_MEMBER_IMAGES_PATH,"name"=> $newName);
		
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>TEAM_MEMBER_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>TEAM_MEMBER_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>TEAM_MEMBER_IMAGES_PATH."/400","crop"=>true ,"size"=>400,"ratio"=>false)));

		return $newName ;
		
  	}

	
// end about team member	
	
// faq section

public function faqAction(){
		global $mySession;
		$this->view->pageHeading = "Manage FAQ";
		$this->view->pageDescription = "manage all FAQs";
		$this->view->pageIcon = "fa icon-question";
    }
	public function getfaqsAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array('faq_id','faq_id','faq_for','faq_question','faq_answer','faq_added_date');

		$sIndexColumn = 'faq_id';
		$sTable = 'faq';
  		
		
		/*Table Setting*/{
		
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
			}
		}
		
		
		
		}/* End Table Setting */
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable $sWhere";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		foreach($qry as $row1)
		{
			$row=array();
			
			/* Page Author Image */
 		 
			$row[] =($j+1).'.';
			
			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			if($row1['faq_for']==0){ $faq_for = "Client";} else{ $faq_for="Service Provider";}
			$row[]=$faq_for;
  			$row[]=ucwords($row1['faq_question']);
			$row[]=substr($row1['faq_answer'],0,20);
			$row[]=date('d-m-Y',strtotime($row1['faq_added_date']));
   			$row[]='<a class="btn default btn-xs purple" href="'.$this->view->url(array("module"=>'privatepanel',"controller"=>"static","action"=>"addfaq",'faq'=>base64_encode($row1[$sIndexColumn])),'default',true).'"><i class="fa fa-edit"></i> Edit </a>';
			
			
  			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 

 public function  removefaqAction(){
		
		global $mySession;
		$this->modelSuper=new Application_Model_SuperModel();
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		$formData = $this->getRequest()->getPost();
 	 
		if ($this->getRequest()->isPost() &&  isset($formData['faq']) && count($formData['faq'])) {
			
			$faqs = implode(",",$formData['faq']) ;
			
  			$removed = $this->modelSuper->Super_Delete('faq',"faq_id IN (".$faqs.")");
 			$mySession->successMsg="FAQ has been deleted.";
			$this->_redirect('privatepanel/static/faq');
 		}
  		
 	}
	public function addfaqAction(){
		
		global $mySession; 
 		$this->modelSuper=new Application_Model_SuperModel();
  		$form = new Application_Form_StaticForm();
		$form->faq();
		$faq_id =$this->_getParam('faq') ;
		if($faq_id!=''){
			$faq_id =base64_decode($faq_id);
			$Type="Edit";$page_type="edit";$msgType="Updated";
			$option_info = $this->modelSuper->Super_Get("faq","faq_id='".$faq_id."'","fetch");
			if(!$option_info){
				$mySession->infoMsg = "No Such FAQ Exists in the database";
				$this->_redirect('privatepanel/static/faq');
			}
			$form->populate($option_info);
		}
		else{
			$Type="Add";$page_type="add";$msgType="Added";
		}
		$this->view->pageHeading = $Type." FAQ";
		$this->view->pageDescription = $page_type." faq";
		$this->view->pageIcon = "fa icon-question";
		
		
		  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
			//prd($data_form);
			
   			if($form->isValid($data_form)){
				
				$data_to_insert = $form->getValues() ;
				$data_to_insert['faq_added_date']=date('Y-m-d H:i:s');
				
				if($faq_id=='')
					$is_insert = $this->modelSuper->Super_Insert("faq",$data_to_insert);
				else
					$is_insert = $this->modelSuper->Super_Insert("faq",$data_to_insert,"faq_id='".$faq_id."'");	
					$mySession->successMsg  = "FAQ Successfully ".$msgType." ";
					$this->_redirect('privatepanel/static/faq');
 				
   			}else{
				$mySession->errorMsg = "Please Check Information Again";
 			}
		 }
  		 $this->view->form =$form;
 		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
  	}
// end faq section	
	
	
	
	
	/* Show All Email Templates */
	public function showmailtemplatesAction(){
		$this->view->pageHeading = "Manage Email Templates";
		$this->view->pageDescription = "manage all email templates";
		$this->view->pageIcon = "icon-envelope-letter";
   	}
	
	/* Edit Email Template */
	public function editmailtemplateAction(){
		
		global $mySession; 
		
		$this->view->pageHeading = "Edit Email Templates";
		$this->view->pageDescription = "manage all email templates";
		$this->view->pageIcon = "icon-envelope-letter";
 		
		$id =  $this->_getParam('email_key');
		
		
 		$record = $this->modelStatic->getTemplate($id);
		
 		if(!$record){
 			$mySession->errorMsg = "No Such Email Template Exists";
 			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_email_templates");
  		}
 
 		$form = new Application_Form_StaticForm();
 		$form->email_template();
		
  		$form->populate((array)$record);
		
		if($this->getRequest()->isPost()){
			
			if($form->isValid($this->getRequest()->getPost())){
				$data=$form->getValues();
				$data['emailtemp_modified']=date('Y-m-d H:i:s');
				$this->modelStatic->add("email_templates",$data,"emailtemp_key='".$id."'");
				$mySession->successMsg = "Email Template Updated Successfully";
 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_email_templates");
				
			}
 		}
		
  		$this->view->form = $form;
		$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	}
	
	
	
	/* DataTable Get Email Template */
	public function gettemplatesAction(){
 
 		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array( 'emailtemp_key','emailtemp_title' ,'emailtemp_subject','emailtemp_modified' );
		$sIndexColumn = 'emailtemp_key';
		
		$sTable = 'email_templates';
		
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR "; // NEW CODE
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET['sSearch_'.$i]))."%' ";
			}
		}
		
 		
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM   $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable ";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
 		 
		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		foreach($qry as $row1)
		{
			$row=array();
 			$row[] = $j+1;
  			$row[]=ucwords($row1['emailtemp_title']);
			$row[]=ucwords(substr($row1['emailtemp_subject'],0,40));
  			$row[]=date("j F Y",strtotime($row1['emailtemp_modified']));
			
			$row[]='<a class="btn btn-xs purple" href="'.APPLICATION_URL.'/privatepanel/static/editmailtemplate/email_key/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
			
 			$output['aaData'][] = $row;
			$j++;
		}
		
		echo json_encode( $output );
		exit();
  	
		
	}
	
 
 
 	
	/* Show Pages  */
	public function indexAction(){
		global $mySession;
		$this->view->pageHeading = "Static Page Content";
		$this->view->pageDescription = "manage static page content";
		$this->view->pageIcon = "fa fa-save";
    }
	
	
	
  	/* Create a New Page   */
	public function addAction(){
		
		global $mySession; 
 
  		$this->view->pageHeading = "Create Page";
		$this->view->pageDescription = "add new page";
		$this->view->pageIcon = "fa fa-save";
		
		$this->view->showEditorUpload = "page";
 				
 		$form = new Application_Form_StaticForm();
		$form->page();
		  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
			
   			if($form->isValid($data_form)){
				
				$data_to_insert = $form->getValues() ;
				
				$is_insert = $this->modelStatic->add("pages",$data_to_insert);
				
				if($is_insert->success){
					$mySession->successMsg  = " Page Successfully Created ";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_static_pages");
 				}
 				$mySession->errorMsg  = $is_insert->message;
   			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
  		 $this->view->form =$form;
 		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
  	}
	
	
 	/* Edit Page Information */
 	public function editAction(){
  		
		global $mySession; 
 		
		$this->view->pageHeading = "Edit Page Content";
		$this->view->pageDescription = "edit page content";
 		$this->view->pageIcon = "fa fa-save";
		$this->view->showEditorUpload = "page";
		 
 		$page_id = $this->_getParam('page_id') ;
	
		$content =  $this->modelStatic->getPage($page_id);
		
  		if(!$content){
			$mySession->errorMsg = "No Such Page Found ";
			$this->_redirect("privatepanel/pages");
		}
		
  		 
 		$form = new Application_Form_StaticForm();
		$form->page();
 		$form->populate($content);
		
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
 			
   			if($form->isValid($posted_data)){
				
				$data=$form->getValues();
				$data['page_updated']=date('Y-m-d H:i:s');
  				$is_update = $this->modelStatic->add("pages",$data ,"page_id=".$page_id);
				
				if($is_update->success){
					$mySession->successMsg  = " Page Content Successfully Updated ";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_static_pages");
				}

				$mySession->errorMsg  = $is_update->message;

    			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
  		 
		 
		 $this->view->page_content = $content;
 		 
		 $this->view->form =$form;
		 
		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	}
	
	
	/* View Page Information */
	public function viewpageAction(){
 		 
		global $mySession; 
		 
 		$this->view->pageHeading = " Page Information  ";
		$this->view->pageDescription = "view page information and delete page  ";
		
 		$this->view->pageIcon = "fa fa-save";
		$page_id = $this->_getParam('page_id') ;
		$content =  $this->modelStatic->getPage($page_id);
		
		
		
		if(!$content){
			$mySession->errorMsg = "No Such Page Found ";
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_static_pages");
		}
		
  		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
 		 
 			if($form->isValid($posted_data)){
				$this->modelStatic->getAdapter()->delete("pages","page_id=".$page_id);
				$mySession->successMsg = " Page Successfully Deleted";
				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_static_pages");
				
			}else{
				$mySession->errorMsg = "Please assign Request Admin";
			}
	    			 
		 }
  		 
		 $this->view->page_content = $content;
 	 
 		
	}
	
	
 	/* Datatables Get Pages */
	public function getpagesAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array('page_id','page_title','page_updated');

		$sIndexColumn = 'page_id';
		$sTable = 'pages';
  		
		
		/*Table Setting*/{
		
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR "; // NEW CODE
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET['sSearch_'.$i]))."%' ";
			}
		}
		
		
		
		}/* End Table Setting */
		
		 
		
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM   $sTable 
			
		$sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable ";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
 		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		foreach($qry as $row1)
		{
			$row=array();
			
			/* Page Author Image */
 			$row[] = $j+1;
			
			
			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			
  			$row[]=ucwords($row1['page_title']);
			
			$row[]=date('j F Y',strtotime($row1['page_updated']));
 			 
   			$row[]='<a class="btn default btn-xs purple" href="'.APPLICATION_URL.'/privatepanel/static/edit/page_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
			$row[]='<a class="btn default btn-xs green" href="'.APPLICATION_URL.'/privatepanel/static/viewpage/page_id/'.$row1[$sIndexColumn].'"><i class="fa fa-search"></i> View </a>';
			
  			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 
	
	/* Remove Pages */
	public function  removepagesAction(){
		
		global $mySession;
		
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		$formData = $this->getRequest()->getPost();
 	 
		if ($this->getRequest()->isPost() &&  isset($formData['pages']) && count($formData['pages'])) {
			
			$pages = implode(",",$formData['pages']) ;
			
  			$removed = $this->modelStatic->getAdapter()->delete('pages',"page_id IN (".$pages.")");
 			$mySession->successMsg=" Recored(s) Deleted Successfuly for the database.. ";
 		}
  		$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_static_pages");
 	}
	
	
	
	/* Manange All Content Blocks  */
	public function contentblockAction(){
		global $mySession;
		$this->view->pageHeading = "Content Block";
		$this->view->pageDescription = "manage content blocks";
		$this->view->pageIcon = "fa fa-copy";
		
		
	}
	
	/* Add Content Block */
  	public function addblockAction(){
		
		global $mySession; 
 		$this->view->pageHeading = "Add Content Block ";
		$this->view->pageDescription = "add new content block";
		$this->view->showEditorUpload = "contentBlock";
		$this->view->pageIcon = "fa fa-copy";
	  		 
 		$form = new Application_Form_StaticForm();
		$form->content_block();
  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
			
  			if($form->isValid($data_form)){
				
				$data_to_insert = $form->getValues() ;

 				$is_insert = $this->modelStatic->add("content_block",$data_to_insert);
				
				if($is_insert->success){
					$mySession->successMsg  = " Content Block Successfully Created ";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_content_block");
 				}
				
				$mySession->errorMsg  = $is_insert->message;
				
  			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
 		 
 		 $this->view->form =$form;
		 
		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	
 	}
  	
	
 	/* Edit Content Blocks  */
	public function editcontentblockAction(){
		
		global $mySession; 
 		
		$this->view->pageHeading = "Add Content Block ";
		$this->view->pageDescription = "add new content block";
		$this->view->pageIcon = "fa fa-copy";
		
		
		$this->view->showEditorUpload = "contentBlock";
		
		$content_block_id = $this->_getParam('content_block_id') ;
		$content =  $this->modelStatic->getContentBlock($content_block_id);
		
		if(!$content){
			$mySession->errorMsg = "No Such Page Found ";
			$this->_redirect("privatepanel/pages/contentblock");
		}
		
  		$form = new Application_Form_StaticForm();
		$form->content_block();
		
		$form->populate($content);
		
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
		 
  			if($form->isValid($posted_data)){
				
				$data_to_insert = $form->getValues();
				
				$is_update = $this->modelStatic->add('content_block' , $data_to_insert ,"content_block_id =".$content_block_id);
					
				if($is_update->success){
					$mySession->successMsg  = " Content Block Successfully Updated ";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_content_block");
				}
 				$mySession->errorMsg  = $is_update->message;
				
   			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
 		 $this->view->page_content = $content;
 		 $this->view->form =$form;
		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
 	}
	
	/* View Content Block  */
	public function viewblockAction(){
 		 
		global $mySession; 
		 
 		$this->view->pageHeading = " Content Block  ";
		$this->view->pageDescription = "view content block information ";
		$this->view->pageIcon = "fa fa-copy";
 		
		$content_block_id = $this->_getParam('content_block_id') ;
		$content =  $this->modelStatic->getContentBlock($content_block_id);
		
		if(!$content){
			$mySession->errorMsg = "No Such Page Found ";
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_content_block");
		}
		
  		 
		
		 
		 
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
			
			$approval_required = false; 
 			
			if($content['content_block_status']){
				/* Direct Delete */
				$form->request_admin->setRequired(true);
				$approval_required = true;
			}
			
 			if($form->isValid($posted_data)){
				
				if(!$approval_required){
  					$this->modelStatic->getAdapter()->delete("content_block","content_block_id=".$content_block_id);
					$mySession->successMsg = " Content Block Successfully Deleted";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_content_block");
 					
				}else{
					/* Approval is needed */
					$modelRequest = new Application_Model_Request();
					
 					$data = $form->getValues();
 					$content['request_admin'] = $data['request_admin']; 
		 			$content['block_to_delete'] = $content['content_block_id']; /* Which Block Needs to Update*/ ;
					 
 					$is_added = $modelRequest->addRequest($content , DELETE_CONTENT_BLOCK , $this->view->admin->user_id /* Author */ );
					 
					 if($is_added->success){
						 $mySession->successMsg = " Request is Successfully Sent to Request Admin. ";
						 $this->_redirect('privatepanel/pages/contentblock');
					 }else{
						  $mySession->errorMsg = $is_added->message;
					 }
  				}
				
				$mySession->errorMsg  = $is_update->message;
 				
				
 				if(false){
 					/* Approval is needed */
					$modelRequest = new Application_Model_Request();
					
 					$data = $form->getValues();
 					$content['request_admin'] = $data['request_admin']; 
		 			$content['block_to_delete'] = $content['content_block_id']; /* Which Block Needs to Update*/ ;
					 
 					$is_added = $modelRequest->addRequest($content , DELETE_CONTENT_BLOCK , $this->view->admin->user_id /* Author */ );
					 
					 if($is_added->success){
						 $mySession->successMsg = " Request is Successfully Sent to Request Admin. ";
						 $this->_redirect('privatepanel/pages/contentblock');
					 }else{
						  $mySession->errorMsg = $is_added->message;
					 }
				}/* end false */
  				
			}else{
				
				$mySession->errorMsg = "Please assign Request Admin";
			}
			
	    			 
		 }
 		 $this->view->page_content = $content;
   	}
	
 	/* Remove removeblock */
	public function  removeblockAction(){
		
		global $mySession;
		
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		$formData = $this->getRequest()->getPost();
 		 
		if ($this->getRequest()->isPost() &&  isset($formData['content_block']) && count($formData['content_block'])) {
			
			$pages = implode(",",$formData['content_block']) ;
			
  			$removed = $this->modelStatic->getAdapter()->delete('content_block',"content_block_id IN (".$pages.")");
 			$mySession->successMsg=" Recored(s) Deleted Successfuly for the database.. ";
 		}
  		$this->_helper->getHelper("Redirector")->gotoRoute(array(),"admin_content_block");
 	}
	
  	
	/* Datatable Get Content Blocks */
	public function getblocksAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array('content_block_id','content_block_title','content_block_updated');

		$sIndexColumn = 'content_block_id';
		$sTable = 'content_block';
  		
		
		/*Table Setting*/{
		
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
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
		
		
		
		}/* End Table Setting */
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable ";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		foreach($qry as $row1)
		{
			$row=array();
			
			/* Page Author Image */
 		 
			$row[] = $row1[ $aColumns[0] ];
			
			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
						
  			$row[]=ucwords($row1['content_block_title']);
			
			$row[]=date('d-F , Y',strtotime($row1['content_block_updated']));
     	 		 
   			$row[]='<a class="btn default btn-xs purple" href="'.APPLICATION_URL.'/privatepanel/static/editcontentblock/content_block_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
			$row[]='<a class="btn default btn-xs green" href="'.APPLICATION_URL.'/privatepanel/static/viewblock/content_block_id/'.$row1[$sIndexColumn].'"><i class="fa fa-search"></i> View </a>';
  			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 
	
	
 	/*
	 *	Graphic Media
	*/
	public function graphicmediaAction(){
 		global $mySession; 
 	
		$this->view->pageHeading = " Manage Graphic Media ";
	
		$this->view->pageDescription = "manage all images ";
		
		$all_media = $this->modelStatic->getMedia();
		
		$this->view->all_media = $all_media ;
		
  	}
	
	
	/*
	 * Add Graphic Media
 	 */
	public function addgraphicmediaAction(){
 		
		global $mySession;
 		
		$this->view->pageHeading = "Add Graphic Media";
		$this->view->pageDescription = "add new graphic media";
 		
  		
		$form = new Application_Form_StaticForm();
		$form->graphic_media();
		
		
 		
   		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
			
			if($form->isValid($posted_data)){
 
  				$media_path = $this->_handle_uploaded_image(); /* Receive Uploaded File */
				
				if(is_object($media_path) and $media_path->success){
					
					$inserted_data = $form->getValues();
					
					$inserted_data['media_path'] = $media_path->media_path;
					
					$insertedMedia = $this->modelStatic->add("graphic_media",$inserted_data); // Save Style in db
  					
					if(is_object($insertedMedia) and $insertedMedia->success){
						$mySession->successMsg = "Graphic Media Item Successfully added "; // Sucessss
						
						$this->_redirector = $this->_helper->getHelper('Redirector')->gotoRoute(array(),'admin_graphic_media');
 					}
				
 				}
			 
 				$mySession->errorMsg = $media_path->message; 
			
			}else{
 				$mySession->errorMsg = " Please Enter Correct Information ..! ";
 			}
		}
 	
		$this->view->form = $form;
		
		$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	}
	
 	/*
	 * Edit Graphic Media
	 */
	public function editgraphicmediaAction(){

		global $mySession;
		
 		$this->view->pageHeading = "Edit Media Information ";
	
		$this->view->pageDescription = "Edit Media information ";
	
		$media_id =$this->_getParam('media_id') ;
 	
 		
		
		$form = new Application_Form_StaticForm();
		$form->graphic_media();
		
		 
  		$media_info = $this->modelStatic->getMedia($media_id);
		
 		if(!$media_info){
			$mySession->infoMsg = "No Such Media Item  Exists in the database...!";
			$this->_redirect('privatepanel/Media');
		}
		
		$form->populate($media_info);
		
 		 if($this->getRequest()->isPost()){
			 
			 $posted_data = $this->getRequest()->getPost();
			 
 			 if($form->isValid($posted_data)){
				 
 				$media_path = $this->_handle_uploaded_image();
				
				if($media_path->success){
					
					$data_array = $form->getValues();
					
					if($media_path->media_path){
 						$data_array['media_path'] = $media_path->media_path ;
					}else{
						unset($data_array['media_path']);
					}
					
 					$is_update = $this->modelStatic->add("graphic_media",$data_array,"media_id=".$media_id); // Save Style in db
				
					if(is_object($is_update) and $is_update->success){
						
						if(isset($data_array['media_path'])){
							$this->_unlink_media_image($media_info['media_path']);
						}
						$mySession->successMsg = "Media Information Successfully Updated "; // Sucessss
						$this->_helper->getHelper('Redirector')->gotoRoute(array(),'admin_graphic_media');
					} 
				
 					$mySession->errorMsg = $is_update->message; 
 				}
				
				$mySession->errorMsg = $media_path->message; 

			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 
		 }
		
		 $this->view->form =  $form;
		 /* User Add Style Page to Render */
		$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
		 
 	}
 	
 	/* 
	 *	Remove Graphic Media 
	 */
 	public function deletegraphicmediaAction(){
		
		global $mySession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$media_id  = $this->_getParam("media_id");
		
		$media_info = $this->modelStatic->getMedia($media_id);
		
		
		if(empty($media_info)){
			$mySession->errorMsg = "No Such Graphic Media Found in the database";
			$this->_helper->getHelper('Redirector')->gotoRoute(array(),'admin_graphic_media');
 		}
		
		
		$this->_unlink_media_image($media_info['media_path']);
		
 		$removed = $this->modelStatic->getAdapter()->delete("graphic_media","media_id IN (".$media_id.")");
		
		$mySession->successMsg = "Graphic Media Successfully Deleted From the Database ";
		
		$this->_helper->getHelper('Redirector')->gotoRoute(array(),'admin_graphic_media');
			
  		 

	
	}
 	
	/* 
	 *	Delete graphic Media Images 
	 */
	private function _unlink_media_image($image){
		
  		if($image!="" and file_exists(MEDIA_IMAGES_PATH."/".$image)){
			unlink(MEDIA_IMAGES_PATH."/".$image);
			unlink(MEDIA_IMAGES_PATH."/300/".$image);
		}
		return true ;
	}
	
	/* 
	 *	Handle The Uploaded Images For Graphic Media  
	 */
	private function _handle_uploaded_image(){
		
 		global $mySession; 
		
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
 			
 			$adapter->addFilter('Rename',array('target' => MEDIA_IMAGES_PATH."/".$new_name));
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
			$thumb_config = array("source_path"=>MEDIA_IMAGES_PATH,"name"=> $new_name);
			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>MEDIA_IMAGES_PATH."/300","crop"=>true ,"size"=>300,"ratio"=>false)));
				 
 			//$uploaded_image_names[]=array('media_path'=>$new_name); => For Multiple Images
   		
		}/* End Foreach Loop for all images */
		
		
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 		
   	 
 	}
	
	
	
 
}

