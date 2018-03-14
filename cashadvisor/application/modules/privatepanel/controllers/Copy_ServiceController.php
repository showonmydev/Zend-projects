<?php
class Privatepanel_ServiceController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelStatic  = new Application_Model_Static();
		$this->view->pageIcon = "fa  fa-users";
		$this->modelsuper = new Application_Model_SuperModel();
		
    }
 	
	
	
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage Services ";
		$this->view->pageDescription = "manage all Services ";
		$this->view->pageIcon = "fa fa-sitemap";
		
 	}
 
 /* Ajax Call For Get services */
  	public function gethomecategoriesAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('service_id','service_parent_id','service_name','service_status','service_image','service_icon');
		$sIndexColumn = 'service_id';
		$sTable = 'services';
 		
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
			$sWhere =" WHERE service_parent_id=0  ";
		}else{
			$sWhere.=" AND service_parent_id=0 ";
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
			
			$row[]=$row1['service_name'];
 			 
			$status = $row1['service_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['service_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/addservices?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;
			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
	
 /* End Ajax Call For Get services */	
 
 // home page services
 
   	public function getservicesAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('service_id','service_parent_id','service_name','service_status','service_image','service_icon');
		$sIndexColumn = 'service_id';
		$sTable = 'services';
 		
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
			$sWhere =" WHERE service_parent_id=0  ";
		}else{
			$sWhere.=" AND service_parent_id=0 ";
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
			$row[]='<img src="'.HTTP_SERVICE_IMAGES_PATH.'/60/'.$row1['service_image'].'" />';
			$row[]=$row1['service_name'];
 			 
			$status = $row1['service_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['service_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/addservices?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}

 
 // end home page services
 
 
// add services  
 public function addservicesAction(){

				global $mySession; 
				$this->view->pageHeading = "Add Service";
				$this->view->pageDescription = "add Service";
				$this->view->pageIcon = "fa fa-sitemap";
				$form = new Application_Form_ServiceForm();
				$form->services();
				$form->service_image->setRequired(true);
				$service_id = $this->getRequest()->getParam('service_id');
				if($service_id!='' && $service_id!='/d+'){
					$serviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
					if(!($serviceData)){ 
						$mySession->infoMsg = "No Such service Exists in the database...!";
						$this->_redirect('/privatepanel/service');
					}
					else 
					{
						$form->service_image->setRequired(false);
						$this->view->pageHeading = "Edit Service";
						$this->view->pageDescription = "edit Service";
						$this->view->pageIcon = "fa fa-sitemap";
						$form->populate($serviceData);
						//prn($serviceData);
					
						
								/*{    //echo"hello else"; die;
									 $is_uploaded = $serviceData['service_image'];
									 prn($is_uploaded);
								}
								*/
					}
				}
				
				// prd($form);
				
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
					//	prd($data_to_insert);
				
						$is_uploaded = $this->_handle_service_image();
						if($is_uploaded!=''){
							$data_to_insert['service_image'] = $is_uploaded;
							//prn($data_to_insert);
						}
				
					
						
						if(!empty($serviceData)){
							$is_insert = $this->modelStatic->add("services",$data_to_insert,"service_id='".$service_id."'");
							$mySession->successMsg  = "service successfully updated ";
						}else{
							$is_insert = $this->modelStatic->add("services",$data_to_insert);
							$mySession->successMsg  = "service successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/service");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
			
	 
	 
	 }
 
// end add services ............................

// edit services  
 public function editservicesAction(){

				global $mySession; 
				$service_id = $this->getRequest()->getParam('service_id');
				$form->service_image->setRequired(false);
				$this->view->pageHeading = "Edit Service";
				$this->view->pageDescription = "edit Service";
				$this->view->pageIcon = "fa fa-sitemap";
			
				$form = new Application_Form_ServiceForm();
				$form->services();
				$form->populate($serviceData);	
				
				$serviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
				if(!($serviceData)){ 
					$mySession->infoMsg = "No Such service Exists in the database...!";
					$this->_redirect('/privatepanel/service');
					}
				
				// prd($form);
				
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
					//	prd($data_to_insert);
				
						$is_uploaded = $this->_handle_service_image();
						if($is_uploaded!=''){
							$data_to_insert['service_image'] = $is_uploaded;
							//prn($data_to_insert);
						}
				
					
						
						if(!empty($serviceData)){
							$is_insert = $this->modelStatic->add("services",$data_to_insert,"service_id='".$service_id."'");
							$mySession->successMsg  = "service successfully updated ";
						}else{
							$is_insert = $this->modelStatic->add("services",$data_to_insert);
							$mySession->successMsg  = "service successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/service");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
			
	 
	 
	 }
 
// end edit services ............................


/*handle_service_image*/

	private function _handle_service_image(){
		
 		$adapter = new Zend_File_Transfer_Adapter_Http();
		$image = $adapter->getFileInfo('service_image');
   		$video_extension = $image['service_image']['name'];
 		$extension = explode('.',$image['service_image']['name']); 
 		$extension = array_pop($extension);
  		$name_for_image = md5(rand(1,999).time()).".".$extension;
  		rename(SERVICE_IMAGES_PATH .'/'.$video_extension ,  SERVICE_IMAGES_PATH .'/'.$name_for_image);
		
		$thumb_config = array("source_path"=>SERVICE_IMAGES_PATH,"name"=> $name_for_image);
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>SERVICE_IMAGES_PATH."/60","crop"=>true ,"width"=>60,"height"=>60,"ratio"=>false)));
		prn($name_for_image);
		return $name_for_image ;
		
  	}


 
// remove service

	public function  removeservicesAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['services']) && count($formData['services'])) {
			$services = implode(",",$formData['services']) ;
  			$removed = $this->modelUser->getAdapter()->delete('services',"service_id IN (".$services.")");
 			$mySession->successMsg="service Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/service");
 	}
// end remove services...........................

// add service category

 public function addservicecategoryAction(){

				global $mySession; 
					
				$this->view->pageHeading = "Add Service category";
				
				$this->view->pageDescription = "add Service category";
				
				$this->view->pageIcon = "fa fa-sitemap";
				 
				$form = new Application_Form_ServiceForm();
				
				$form->servicecat();
				// prd($form);
				$service_id = $this->getRequest()->getParam('service_id');
				if($service_id!='' && $service_id!='/d+'){
					$serviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
					if(!($serviceData)){
						$mySession->infoMsg = "No Such category Exists in the database...!";
						$this->_redirect('/privatepanel/service/servicecategory');
					}
					else
						$this->view->pageHeading = "Edit Service category";
				
						$this->view->pageDescription = "edit Service category";
				
						$this->view->pageIcon = "fa fa-sitemap";
						$form->populate($serviceData);
				}
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						//prd($data_to_insert);
						
						$is_uploaded = $this->_handle_service_image();
						if($is_uploaded!=''){
							$data_to_insert['service_image'] = $is_uploaded;
						}
						
						
						if(!empty($serviceData)){
							//echo "if";
							$is_insert = $this->modelStatic->add("services",$data_to_insert,"service_id='".$service_id."'");
							$mySession->successMsg  = "category successfully updated ";
						}else{
							//echo "else";
							$is_insert = $this->modelStatic->add("services",$data_to_insert);
							//prd($is_insert);
							$mySession->successMsg  = "category successfully added ";
						}
						//echo "none";
						if($is_insert->success){
							$this->_redirect("privatepanel/service/servicecategory");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
			
	 
	 
	 }
 
// end add service category...........................

// remove service category
	public function  removeservicescategoryAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['services']) && count($formData['services'])) {
			$services = implode(",",$formData['services']) ;
  			$removed = $this->modelUser->getAdapter()->delete('services',"service_id IN (".$services.")");
 			$mySession->successMsg="service Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/service/servicecategory");
 	}
// end remove services category...........................

// add service sub category

 public function addsubcategoryAction(){

				global $mySession; 
					
				$this->view->pageHeading = "Add Service Sub category";
				
				$this->view->pageDescription = "add Service sub category";
				
				$this->view->pageIcon = "fa fa-sitemap";
				 
				$form = new Application_Form_ServiceForm();
				
				$form->subcategory();
				// prd($form);
				$service_id = $this->getRequest()->getParam('service_id');
				if($service_id!='' && $service_id!='/d+'){
					$serviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
					$this->view->serviceData = $serviceData;
					if(!($serviceData)){
						$mySession->infoMsg = "No Such sub category Exists in the database...!";
						$this->_redirect('/privatepanel/service/servicesubcategory');
					}
					else
					
						$this->view->pageHeading = "Edit Service Sub category";
				
						$this->view->pageDescription = "edit Service sub category";
				
						$this->view->pageIcon = "fa fa-sitemap";
				 
						$form->populate($serviceData);
				}
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					$form->service_sub_parent_id->setRegisterInArrayValidator(false);
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						
						
						if(!empty($serviceData)){
							$is_insert = $this->modelStatic->add("services",$data_to_insert,"service_id='".$service_id."'");
							$mySession->successMsg  = " sub category successfully updated ";
						}else{
							$is_insert = $this->modelStatic->add("services",$data_to_insert);
							$mySession->successMsg  = "sub category successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/service/servicesubcategory");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->view->serviceID = $service_id;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
			
	 
	 
	 }
  
// end add service sub category............................

// remove service sub category
	public function  removesubcategoryAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['services']) && count($formData['services'])) {
			$services = implode(",",$formData['services']) ;
  			$removed = $this->modelUser->getAdapter()->delete('services',"service_id IN (".$services.")");
 			$mySession->successMsg="service Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/service/servicesubcategory");
 	}
// end remove services sub category...........................

 
 	public function servicecategoryAction()
	  {   
	  global $mySession; 
 		$this->view->pageHeading = "Services Categories ";
		$this->view->pageDescription = "Manage Service Categories ";
		$this->view->pageIcon = "fa fa-cog";
	  }
	  
	public function servicesubcategoryAction()
	  {   
	  global $mySession; 
 		$this->view->pageHeading = "Services Categories ";
		$this->view->pageDescription = "Manage Service Categories ";
		$this->view->pageIcon = "fa fa-cog";
	  }
	  
	public function homecategoriesAction()
	  {   
	  global $mySession; 
 		$this->view->pageHeading = "Home Page Categories ";
		$this->view->pageDescription = "Manage Home Page Categories ";
		$this->view->pageIcon = "fa fa-cog";
	  }


// get service category

	public function getservicecategoryAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array(
			'services.service_id',
			'services.service_parent_id',
			'services.service_sub_parent_id',
			'services.service_name',			
			'services.service_status',
			'services.service_image',
			
		);
			

		$sIndexColumn = 'service_id';
		$sTable = 'services';
 		
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
			$sWhere =" WHERE services.service_parent_id!= '0' and services.service_sub_parent_id='0'";
		}else{
			$sWhere.=" AND services.service_parent_id != '0' and services.service_sub_parent_id='0'";
		}
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." ,
			parent.service_name as parentcategory_name
			FROM  $sTable  as services
		 join services as parent on parent.service_id = services.service_parent_id
			$sWhere $sOrder $sLimit"; 
				
		 
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
			
			//$row[]=$row1['service_name'];
			$row[]='<img src="'.HTTP_SERVICE_IMAGES_PATH.'/60/'.$row1['service_image'].'" />';
			$row[]=trim(htmlentities($row1['service_name']));
			$row[]=$row1['parentcategory_name'];
 			 
			$status = $row1['service_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['service_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/addservicecategory?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;
			$j++;
	
		}
		
		
		echo json_encode( $output );
		exit();
  	}
	
// end get service category	.................................

// get service sub category

	public function getservicesubcategoryAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array(
			'services.service_id',
			'services.service_name',
			'services.service_parent_id',
			'services.service_sub_parent_id',
			'services.service_price'
		);
			

		$sIndexColumn = 'service_id';
		$sTable = 'services';
 		
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
			$sWhere =" WHERE services.service_sub_parent_id != 0";
		}else{
			$sWhere.=" AND services.service_sub_parent_id != 0";
		}
		
 		
		$sQuery ="SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)).",
				t1.service_name as main_category,t2.service_name as main_service
				 FROM  $sTable join services as t1 on t1.service_id = services.service_sub_parent_id join services as t2 on t2.service_id = t1.service_parent_id 
				 $sWhere $sOrder $sLimit"; 	
		 
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
		//prd($qry);
 
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
			
			//$row[]=$row1['service_name'];
			
			$row[]=trim(htmlentities($row1['service_name']));
			$row[]=$row1['service_price'];
			
			$row[]=$row1['main_category'];
			
			$row[]=$row1['main_service'];	
 			 
		
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/addsubcategory?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;
			$j++;
	
		}
		
		
		echo json_encode( $output );
		exit();
  	}
	
// end get service sub category	........................


// dropdown service list
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





}