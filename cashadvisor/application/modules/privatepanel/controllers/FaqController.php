<?php
class Privatepanel_FaqController extends Zend_Controller_Action
{
 	private $privatepanel = "" , $modelStatic;
	
    public function init(){ 
  		$this->modelStatic  = new Application_Model_Static();
  	}
	 
 	
	/* Show Industries  */
	public function indexAction(){
		global $mySession;
		$this->view->pageHeading = "Help Section";
		$this->view->pageDescription = "manage Help Section";
		$this->view->pageIcon = "fa fa-cog";
    }

  	/* Create a New Industry   */
	public function addAction(){
		
		global $mySession; 
 
  		$this->view->pageHeading = "Create Help Section";
		$this->view->pageDescription = "add new Help Section";
		$this->view->pageIcon = "fa fa-cog";		
		$this->view->showEditorUpload = "Help Section"; 				
 		$form = new Application_Form_StaticForm();
		$form->faq();
		  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
			
   			if($form->isValid($data_form)){
				
				$data_to_insert = $form->getValues() ;
				$is_insert = $this->modelStatic->add("faq",$data_to_insert);
				
				if($is_insert->success){
					$mySession->successMsg  = " Faq Successfully Created ";
					$this->redirect('privatepanel/faq');
 				}
 				$mySession->errorMsg  = $is_insert->message;
   			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
  		 $this->view->form =$form;
 		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
  	}
	
 	/* Edit Industry Information */
 	public function editAction(){
  		
		global $mySession; 
 		
		$this->view->pageHeading = "Edit Help Section";
		$this->view->pageDescription = "edit Help Section";
 		$this->view->pageIcon = "fa fa-cog";
		
		$this->view->showEditorUpload = "Help Section";
		 
 		$page_id = (int) $this->_getParam('page_id') ;
	
	    $where="faq_id='".$page_id."'";
		$content =  $this->modelStatic->getparticulardata('faq',$where);
		
  		if(!$content){
			$mySession->errorMsg = "No Such faq Found ";
			$this->_redirect("privatepanel/faq");
		}
		
  		 
 		$form = new Application_Form_StaticForm();
		$form->faq();
		
 		$form->populate($content);
		
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
 			
   			if($form->isValid($posted_data)){
				
  				$is_update = $this->modelStatic->add("faq",$form->getValues() ,"faq_id=".$page_id);
				
				if($is_update->success){
					$mySession->successMsg  = " Help Section Successfully Updated ";
					$this->redirect('privatepanel/faq');
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
	
 	/* Datatables Get Industry */
	public function getfaqAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array('faq_id','topic_name','faq_question','faq_answer');

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
		
		 
		
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM   $sTable  INNER JOIN `faq_topic` on $sTable.faq_topic_id = faq_topic.topic_id
			
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
		
		$j=1;
		foreach($qry as $row1)
		{
			$row=array();
			
			/* Page Author Image */
 			$row[] = $j;
			
			
			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			
			$row[]=ucwords($row1['topic_name']);
			
  			$row[]=ucwords($row1['faq_question']);
 			 
   			$row[]='<a class="btn default btn-xs purple" href="'.APPLICATION_URL.'/privatepanel/faq/edit/page_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
			
  			$output['aaData'][] = $row;
			$j++;
		}
		echo json_encode( $output );
		exit();
 	} 
	
	/* Remove Industry */
	public function  removeAction(){
		
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		$formData = $this->getRequest()->getPost();
		
		if ($this->getRequest()->isPost() &&  isset($formData['faq']) && count($formData['faq'])) {
			
			$pages = implode(",",$formData['faq']) ;
			
  			$removed = $this->modelStatic->getAdapter()->delete('faq',"faq_id IN (".$pages.")");
 			$mySession->successMsg=" Record(s) Deleted Successfully from the database.. ";
 		}
		$this->redirect('privatepanel/faq');
 	}
	
 
 	/* Show Industries  */
	public function topicsAction(){
		global $mySession;
		$this->view->pageHeading = "Help Section Topics";
		$this->view->pageDescription = "manage Help Section topics";
		$this->view->pageIcon = "fa fa-cog";
    }

  	/* Create a New Industry   */
	public function addtopicAction(){
		
		global $mySession; 
 
  		$this->view->pageHeading = "Create Help Section Topic";
		$this->view->pageDescription = "add new Help Section topic";
		$this->view->pageIcon = "fa fa-cog";		
		$this->view->showEditorUpload = "Help Section Topic"; 				
 		$form = new Application_Form_StaticForm();
		$form->faqtopic();
		  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
			
   			if($form->isValid($data_form)){
				
				$data_to_insert = $form->getValues() ;
				$is_insert = $this->modelStatic->add("faq_topic",$data_to_insert);
				if($is_insert->success){
					$mySession->successMsg  = " Faq Topic Successfully Created ";
					$this->redirect('privatepanel/faq/topics');
 				}
 				$mySession->errorMsg  = $is_insert->message;
   			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
  		 $this->view->form =$form;
 		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
  	}
	
 	/* Edit Industry Information */
 	public function edittopicAction(){
  		
		global $mySession; 
 		
		$this->view->pageHeading = "Edit Help Section Topic";
		$this->view->pageDescription = "edit Help Section Topic";
 		$this->view->pageIcon = "fa fa-cog";
		
		$this->view->showEditorUpload = "Help Section Topic";
		 
 		$page_id = (int) $this->_getParam('page_id') ;
	
	    $where="topic_id='".$page_id."'";
		$content =  $this->modelStatic->getparticulardata('faq_topic',$where);
		
  		if(!$content){
			$mySession->errorMsg = "No Such faq Topic Found ";
			$this->_redirect("privatepanel/faq/topics");
		}
		
  		 
 		$form = new Application_Form_StaticForm();
		$form->faqtopic($page_id);
		
 		$form->populate($content);
		
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
 			
   			if($form->isValid($posted_data)){
				$formval=$form->getValues();
				if($formval["topic_image"]=='')
				{
					$formval["topic_image"]=$content["topic_image"];
				}
  				$is_update = $this->modelStatic->add("faq_topic",$formval ,"topic_id=".$page_id);
				
				if($is_update->success){
					$mySession->successMsg  = " Help Section Topic Successfully Updated ";
					$this->redirect('privatepanel/faq/topics');
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
	
 	/* Datatables Get Industry */
	public function gettopicsAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array('topic_id','topic_name','topic_status');

		$sIndexColumn = 'topic_id';
		$sTable = 'faq_topic';
  		
		
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
		
		$j=1;
		foreach($qry as $row1)
		{
			$row=array();
			
			/* Page Author Image */
 			$row[] = $j;
			
			
			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			
  			$row[]=ucwords($row1['topic_name']);
 			 
   			$row[]='<a class="btn default btn-xs purple" href="'.APPLICATION_URL.'/privatepanel/faq/edittopic/page_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
			
  			$output['aaData'][] = $row;
			$j++;
		}
		echo json_encode( $output );
		exit();
 	} 
	
	/* Remove Industry */
	public function  removetopicAction(){
		
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		$formData = $this->getRequest()->getPost();
		
		if ($this->getRequest()->isPost() &&  isset($formData['faq_topic']) && count($formData['faq_topic'])) {
			
			$pages = implode(",",$formData['faq_topic']) ;
			
  			$removed = $this->modelStatic->getAdapter()->delete('faq_topic',"topic_id IN (".$pages.")");
 			$mySession->successMsg=" Record(s) Deleted Successfully from the database.. ";
 		}
		$this->redirect('privatepanel/faq/topics');
 	}
 
}