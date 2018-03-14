<?php
class Privatepanel_PackagesController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelStatic  = new Application_Model_Static();
		$this->view->pageIcon = "fa  fa-users";
		$this->modelsuper = new Application_Model_SuperModel();
		
    }
 	
	
	
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage Credit Packages ";
		$this->view->pageDescription = "manage all Credit Packages ";
		$this->view->pageIcon = "fa fa-credit-card";
		
 	}
	
	 	

/*get packages*/	
	   	public function getpackagesAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('cp_id','cp_title','cd_date','cp_price','cp_points','cp_desc','cp_sub_desc');
		$sIndexColumn = 'cp_id';
		$sTable = 'credit_package';
 		
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
			$row[]=$row1['cp_title'];
			$row[]=$row1['cd_date'];
			$row[]=PRICE_SYMBOL.' '.$row1['cp_price'];
			$row[]=$row1['cp_points'];
			$row[]=$row1['cp_desc'];
			$row[]=$row1['cp_sub_desc'];
			

					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/Packages/addpackages?cp_id='.$row1['cp_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
/* end get packages*/
	
// add packages  
		 public function addpackagesAction(){

				global $mySession; 
					
				$this->view->pageHeading = "Add Package";
				
				$this->view->pageDescription = "add Package";
				
				$this->view->pageIcon = "fa fa-credit-card";
				 
				$form = new Application_Form_PackageForm();
				
				$form->packages();
				// prd($form);
				$creditPackage_id = $this->getRequest()->getParam('cp_id');
				if($creditPackage_id!='' && $creditPackage_id!='/d+'){
					$creditPackageData = $this->modelStatic->Super_Get('credit_package',"cp_id='".$creditPackage_id."'",'fetch');
					if(!($creditPackageData)){ 
						$mySession->infoMsg = "No Such Package Exists in the database...!";
						$this->_redirect('/privatepanel/packages');
					}
					else 
						$form->populate($creditPackageData);
				}
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
					//	prd($data_to_insert);
						if(!empty($creditPackageData)){
							
							$is_insert = $this->modelStatic->add("credit_package",$data_to_insert,"cp_id='".$creditPackage_id."'");
						
							$mySession->successMsg  = "Package successfully updated ";
						}else{
							$is_insert = $this->modelStatic->add("credit_package",$data_to_insert);
							$mySession->successMsg  = "Package successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/packages");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
				
	 
	 
	 }
// end add packages ............................

// remove packages
	public function  removepackagesAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['credit_package']) && count($formData['credit_package'])) {
			$packages = implode(",",$formData['credit_package']) ;
  			$removed = $this->modelUser->getAdapter()->delete('credit_package',"cp_id IN (".$packages.")");
 			$mySession->successMsg="Package Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/packages");
 	}
// end remove packages...........................

public function providercredithistoryAction(){
 		global $mySession; 
 		$this->view->pageHeading = " Service Provider Payment History ";
		$this->view->pageDescription = "Payment History ";
		$this->view->pageIcon = "fa fa-credit-card";
		$form = new Application_Form_PackageForm();
		$form->serchpackages();
		$this->view->form=$form;
		$this->view->search_date ='';
		if(isset($_GET['pp_request_date']) && $_GET['pp_request_date']!=''){
	       $this->view->search_date = $_GET['pp_request_date'];	
		   $form->populate(array("pp_request_date"=>$this->view->search_date));
		}
		
		
		
		
 	}

/*get provider credit history*/	
	   	public function getprovidercredithistoryAction(){
		
		$this->dbObj = Zend_Registry::get('db');
		
 		$dates=$this->_getParam('search_date');
		
 		$aColumns = array('pp_id','pp_request_by','pp_title','pp_request_date','pp_amount_paid','package_points');
		$sIndexColumn = 'pp_id';
		$sTable = 'package_purchased';
		
		
 		
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
		
		$aColumns2 = array('pp_id','user_name','pp_title','pp_request_date','pp_amount_paid','package_points');
		
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR ";
				$sWhere .= "LOWER(".$aColumns[$i].") LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR LOWER(CONCAT(user_first_name,' ',user_last_name)) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR "; // NEW CODE
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
		
		if($dates!='')
			{
				$sWhere.=" and pp_request_date='".$dates."' ";
			}
			
		//$sOrder ="order by user_name asc";
 		/*SELECT * FROM `package_purchased` left join users on users.user_id = package_purchased.pp_request_by*/
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)).",
		 CONCAT(user_first_name,' ',user_last_name) as user_name
		 FROM  $sTable left join users on users.user_id= package_purchased.pp_request_by
		 $sWhere $sOrder $sLimit";
		// echo $sQuery;die;
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
		//prn($qry);
 
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
  			/*$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';*/
			$row[]=$row1['user_name'];
			$row[]=$row1['pp_title'];
			$row[]=$row1['pp_request_date'];
			$row[]=PRICE_SYMBOL.' '.$row1['pp_amount_paid'];
			
			$row[]=$row1['package_points'];
					
		/*	$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/Packages/addpackages?cp_id='.$row1['pp_id'].'" class="btn btn-xs green"> View <i class="fa fa-search"></i></a>';*/
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
/* end get provider pay history*/
		 public function editpackagepageAction(){

				global $mySession; 
					
				$this->view->pageHeading = "Page Description";
				
				$this->view->pageDescription = "Page Description";
				
				$this->view->pageIcon = "fa fa-file-tex";
				 
				$form = new Application_Form_PackageForm();
				
				$form->packagepagedesc();
					$pageData = $this->modelStatic->Super_Get('Package_page',"1",'fetch');
					if(!($pageData)){ 
						$mySession->infoMsg = "No Such page Exists in the database...!";
						$this->_redirect('/privatepanel/packages/editpackagepage');
					}
					else 
						$form->populate($pageData);
			
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
							$is_insert = $this->modelStatic->add("Package_page",$data_to_insert,"package_page_id='".$data_to_insert['package_page_id']."'");
						
							$mySession->successMsg  = "Page Description updated successfully";
						
						if($is_insert->success){
							$this->_redirect("privatepanel/packages/editpackagepage");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	 
	 }



}