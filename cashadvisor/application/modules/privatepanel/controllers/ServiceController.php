<?php
class Privatepanel_ServiceController extends Zend_Controller_Action
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
 		$this->view->pageHeading = "Manage Services ";
		$this->view->pageDescription = "manage all Services ";
		$this->view->pageIcon = "fa fa-sitemap";
		
 	}
 

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
  			/*$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';*/
			
			$row[]=$row1['service_name'];
 			 
			/*$status = $row1['service_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['service_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';*/
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/edithomecategories?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;
			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
	
// edit home services  
	 public function edithomecategoriesAction(){
					global $mySession; 
					$service_id = $this->getRequest()->getParam('service_id');
					$form = new Application_Form_ServiceForm();
					$form->homecat($service_id);
					$this->view->pageHeading = "Edit Home Page Categories";
					$this->view->pageDescription = "Edit Home Page Categories";
					$this->view->pageIcon = "fa fa-sitemap";
					
					$editserviceData = $this->modelStatic->Super_Get('home_category',"home_service_parent_id='".$service_id."'",'fetch');
					$checkedVal = array();
					if(!($editserviceData)){ 
						$mySession->infoMsg = "No Such service Exists in the database...!";
						$this->_redirect('/privatepanel/service/homecategories');
						}
					else{
						$checkedVal = array_filter(explode(',',$editserviceData['home_service_sub_parent_id']));
					//prn($checkedVal);
					}
					if(count($checkedVal)<1){
						$getSubCat = $this->modelStatic->Super_Get('services','service_parent_id="'.$service_id.'" and service_sub_parent_id = "0"',"fetchAll",array('limit'=>'9'));
						$checkedVal = array_column($getSubCat,'service_id'); 
						//prn($checkedVal);
					}
					
					$editserviceData['service_parent_id']= $checkedVal;
				//	prd($editserviceData);
					$form->populate($editserviceData);	
					
					if($this->getRequest()->isPost()) {
						$data_form = $this->getRequest()->getPost();
						if($form->isValid($data_form)){
							$data_to_insert = $form->getValues();
							//prd($data_to_insert);
							$checkValue = implode(',',$data_to_insert['service_parent_id']);
							//prn($checkValue);
							$insertedData = array(
									'home_service_parent_id' => $service_id,
									'home_service_sub_parent_id' => $checkValue
								);
							//prd($insertedData);
							if(!empty($editserviceData)){
								$is_insert = $this->modelStatic->add("home_category",$insertedData,"home_service_parent_id='".$service_id."'");
								$mySession->successMsg  = "service successfully updated ";
							}else{
								$mySession->successMsg  = "Please Check Information Again ... !";
							}
							
							if($is_insert->success){
								$this->_redirect("privatepanel/service/homecategories");
							}
							$mySession->errorMsg  = $is_insert->message;
						}else{
							$mySession->errorMsg = " Please Check Information Again ... ! ";
						}
					 }
					 $this->view->form =$form;
					 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
				
		 
		 
		 }
// end edit home services ............................



 
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
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/editservices?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
 
 
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
							$is_insert = $this->modelStatic->add("services",$data_to_insert);
							$mySession->successMsg  = "service successfully added ";
						if($is_insert->success){
							$home['home_service_parent_id']=$is_insert->inserted_id;
							$home['home_service_sub_parent_id']='';
							
							$insert_into_home = $this->modelStatic->add("home_category",$home);
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
				
				$form = new Application_Form_ServiceForm();
				$form->services();
				$form->service_image->setRequired(false);
				$this->view->pageHeading = "Edit Service";
				$this->view->pageDescription = "edit Service";
				$this->view->pageIcon = "fa fa-sitemap";
				
				$editserviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
				$this->view->service_image = $editserviceData['service_image'];
				//prn($editserviceData['service_image']);
				if(!($editserviceData)){ 
					$mySession->infoMsg = "No Such service Exists in the database...!";
					$this->_redirect('/privatepanel/service');
					}
				$form->populate($editserviceData);	
				//prn($editserviceData);
				
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
// edit service image						
						if(isset($_FILES['service_image']['name']) && !empty($_FILES['service_image']['name']))
						{// echo "image selectecd";
							 unlink(SERVICE_IMAGES_PATH."/".$editserviceData['service_image']);
							  unlink(SERVICE_IMAGES_PATH."/60/".$editserviceData['service_image']);
							 $is_uploaded = $this->_handle_service_image();
							 $data_to_insert['service_image'] = $is_uploaded;
							 //prd($is_uploaded);
							
						}
						else
						{	//echo "image not  selectecd";
							 $is_uploaded = $editserviceData['service_image'];
							 $data_to_insert['service_image'] = $is_uploaded;
							  //prn($is_uploaded);
						}
// end edit service image						
						if(!empty($editserviceData)){
							$is_insert = $this->modelStatic->add("services",$data_to_insert,"service_id='".$service_id."'");
							$mySession->successMsg  = "service successfully updated ";
						}else{
							$mySession->successMsg  = "Please Check Information Again ... !";
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
			$remove_from_home_cat = $this->modelUser->getAdapter()->delete('home_category',"home_service_parent_id IN (".$services.")");
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
				
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						$is_uploaded = $this->_handle_service_image();
						if($is_uploaded!=''){
							$data_to_insert['service_image'] = $is_uploaded;
						}
				$is_insert = $this->modelStatic->add("services",$data_to_insert);
				$mySession->successMsg  = "category successfully added ";
						if($is_insert->success){
							  // insert category into home category table
								$get_home_cat = $this->modelStatic->Super_Get('home_category',"home_service_parent_id ='".$data_to_insert['service_parent_id']."'",'fetch');
								//prn($get_home_cat);
								if($get_home_cat['home_service_sub_parent_id']=='')
								{ //echo "blank" ; die;
									$insert_home['home_service_sub_parent_id'] =  $is_insert->inserted_id;
								$insert_cat_into_home_table = $this->modelStatic->add("home_category",$insert_home,"home_service_parent_id='".$data_to_insert['service_parent_id']."'");
									} //else{ echo "NOOO"; die;}
							
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

// edit services category
 public function editservicecategoryAction(){

				global $mySession; 
				$service_id = $this->getRequest()->getParam('service_id');
				
				$form = new Application_Form_ServiceForm();
				$form->servicecat();
				$form->service_image->setRequired(false);
				$this->view->pageHeading = "Edit Service category";
				$this->view->pageDescription = "edit Service category";
				$this->view->pageIcon = "fa fa-sitemap";
				
				$editserviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
					$this->view->service_image = $editserviceData['service_image'];
				if(!($editserviceData)){ 
					$mySession->infoMsg = "No Such service Exists in the database...!";
					$this->_redirect('/privatepanel/service');
					}
				$form->populate($editserviceData);	
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
// edit service image						
						if(isset($_FILES['service_image']['name']) && !empty($_FILES['service_image']['name']))
						{
							 unlink(SERVICE_IMAGES_PATH."/".$editserviceData['service_image']);
							  unlink(SERVICE_IMAGES_PATH."/60/".$editserviceData['service_image']);
							 $is_uploaded = $this->_handle_service_image();
							 $data_to_insert['service_image'] = $is_uploaded;
						}
						else
						{	
							 $is_uploaded = $editserviceData['service_image'];
							 $data_to_insert['service_image'] = $is_uploaded;
						}
// end edit service image						
						if(!empty($editserviceData)){
							$is_insert = $this->modelStatic->add("services",$data_to_insert,"service_id='".$service_id."'");
							$mySession->successMsg  = "Service category successfully updated ";
						}else{
							$mySession->successMsg  = "Please Check Information Again ... !";
						}
						
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
// end  edit services category

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
						
						//prd($data_to_insert);
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
			//	$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR ";
				
				$sWhere .= "LOWER(".$aColumns[$i].") LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR LOWER(parent.service_name) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR "; // NEW CODE
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
		// echo $sQuery;die;
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
					
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/editservicecategory?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
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
		$columsArray = $aColumns;
		unset($columsArray[0]);
		unset($columsArray[2]);
		unset($columsArray[3]);

		$columsArray=array_values($columsArray) ;
		//prn($columsArray);
		if ( isset($_GET['sSearch']) and $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($columsArray) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			//	$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR ";
// search when alais in single table services				
				$sWhere .= "LOWER(".$columsArray[$i].") LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR LOWER(t1.service_name) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR  LOWER(t2.service_name) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR "; // NEW CODE
				//$sWhere .= "".$aColumns[$i]." LIKE '%".trim(addslashes($_GET["sSearch"]))."%' OR "; // NEW CODE
				// LOWER(service_price) LIKE '%".addslashes(trim(strtolower($_GET["sSearch"])))."%' OR
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		/* Individual column filtering */

		for ( $i=0 ; $i<count($columsArray) ; $i++ )
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
				$sWhere .= "".$columsArray[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
		
		
		if($sWhere== ""){
			$sWhere =" WHERE services.service_sub_parent_id != 0";
		}else{
			$sWhere.=" AND services.service_sub_parent_id != 0";
		}

		$sQuery ="SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)).",
				t1.service_name as main_category,t2.service_name as main_service
				 FROM  $sTable join services as t1 on t1.service_id = services.service_sub_parent_id 
				 join services as t2 on t2.service_id = t1.service_parent_id 
				 $sWhere $sOrder $sLimit"; 
				//prd($sQuery);	
		 
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

//  category form

	 public function categoryformAction()
		  {   
		 	 global $mySession; 
			$this->view->pageHeading = "Categories Form";
			$this->view->pageDescription = "Manage Categories Form ";
			$this->view->pageIcon = "fa fa-cog";
		  }
		  
	public function getcategoryformAction(){
		
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
			$sWhere =" WHERE services.service_sub_parent_id != 0";
		}else{
			$sWhere.=" AND services.service_sub_parent_id != 0";
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
			$row[]=$row1['service_name'];
 			 
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/viewcategoryform?service_id='.$row1['service_id'].'" class="btn btn-xs purple"> view <i class="fa fa-pencil"></i></a>';		
			/*$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/service/addformcat?service_id='.$row1['service_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';*/
			
  			$output['aaData'][] = $row;
			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}



//add edit category form  
 	public function addformcatAction(){
		
		global $mySession; 
					$cat_form_id = $this->getRequest()->getParam('c_form_id');
					$service_id = $this->getRequest()->getParam('service_id');
					$this->view->category_id = $service_id;
					

				if($cat_form_id!='' && $cat_form_id!='/d+'){
						$this->view->pageHeading = "edit form to category";
						$this->view->pageDescription = "edit form to category";
						$this->view->pageIcon = "fa fa-sitemap";
					
						$editCategoryForm = $this->modelStatic->Super_Get('category_form',"c_form_id='".$cat_form_id."'",'fetch');
						$this->view->editCategoryForm = $editCategoryForm;
						
						$edit_options = $this->modelStatic->Super_Get('category_form_options',"category_fom_id='".$cat_form_id."'",'fetchAll'); 
						$this->view->edit_options = $edit_options;
						
						if(!($editCategoryForm)){ 
							$mySession->infoMsg = "No Such service Exists in the database...!";
							$this->_redirect('/privatepanel/service/viewcategoryform');
						}
						$form = new Application_Form_ServiceForm();
						$form->catform($service_id,$cat_form_id);
						$form->populate($editCategoryForm);	
						$this->view->cat_form_id = $cat_form_id;
						
				}
				 else{
						$this->view->pageHeading = "Add form to category";
						$this->view->pageDescription = "add form to category";
						$this->view->pageIcon = "fa fa-sitemap";
						$form = new Application_Form_ServiceForm();
						$form->catform($service_id);
						$this->view->cat_form_id = '';
						
				 }
						
										
				if($service_id!='' && $service_id!='/d+'){
					$serviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
					if(!($serviceData)){ 
						$mySession->infoMsg = "No Such service Exists in the database...!";
						$this->_redirect('/privatepanel/service/viewcategoryform');
					}
				}
				
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
						if(isset($data_form['field']) && !empty($data_form['field'])){
							if(($data_form['c_form_field_type']!= '0' && $data_form['c_form_field_type']!='1' && $data_form['c_form_field_type']!='4')){
								$total_options = count($data_form['field']);
							}
							else{
								$total_options= 0 ;	
							}
						}
						else{
							$total_options= 0 ;	
								if($cat_form_id!='' && $cat_form_id!='/d+'){
									$remove_options = $this->modelUser->getAdapter()->delete('category_form_options',"category_fom_id='".$cat_form_id."'");
								}
							
							}
							
						if(isset($data_form['c_other']) && !empty($data_form['c_other'])){
							$c_other = 1;
							}else{
								$c_other = 0;
								}	
							
						$data_to_insert['category_id']= $data_form['category_id'];
						$data_to_insert['c_form_field_name']= $data_form['c_form_field_name'];
						$data_to_insert['c_form_field_type']= $data_form['c_form_field_type'];
						$data_to_insert['c_required_optional']= $data_form['c_required_optional'];
						$data_to_insert['total_options']= $total_options;
						$data_to_insert['c_other']= $c_other;
						
					// add options value to array for insert into DB
					if($total_options!=0){
						for($i=1;$i<=$total_options;$i++){
								$options_data[$i-1]= $data_form['field'][$i-1];
							}
							//prd($options_data);
						}
					if($cat_form_id!='' && $cat_form_id!='/d+'){
						$is_update_cat_form = $this->modelStatic->add("category_form",$data_to_insert,"c_form_id='".$cat_form_id."'");
						
						if($is_update_cat_form->success){
							$remove_options = $this->modelUser->getAdapter()->delete('category_form_options',"category_fom_id='".$cat_form_id."'");
							foreach($options_data as $options){
									$option_insert['category_fom_id'] = $cat_form_id;
									$option_insert['option_text'] = $options;
									$is_insert_options = $this->modelStatic->add("category_form_options",$option_insert);
							}
							
								$mySession->successMsg  = "category form updated successfully ";
								$this->_redirect("privatepanel/service/viewcategoryform?service_id=".$service_id);
								$mySession->errorMsg  = $is_insert->message;
						}
						else{
						$this->_redirect("privatepanel/service/viewcategoryform?service_id=".$service_id);
						$mySession->errorMsg = " Please Check Information Again ... ! ";
						}
					}
					
					else{
						$is_insert = $this->modelStatic->add("category_form",$data_to_insert);
						if($is_insert->success){
							//prd($is_insert);
						if($total_options!=0){	
							foreach($options_data as $options){
									$option_insert['category_fom_id'] = $is_insert->inserted_id;
									$option_insert['option_text'] = $options;
									$is_insert_options = $this->modelStatic->add("category_form_options",$option_insert);
							}
						}
						//echo $service_id;die;
						$mySession->successMsg  = "category form added successfully ";
						$this->_redirect("privatepanel/service/viewcategoryform?service_id=".$service_id);
						$mySession->errorMsg  = $is_insert->message;
					} 
				 	 	else{
							$this->_redirect("privatepanel/service/viewcategoryform?service_id=".$service_id);
							$mySession->errorMsg = " Please Check Information Again ... ! ";
				 		}
					}
				}
				 $this->view->form =$form;
	 }

// end edit category form ............................

// delete category form  
 	public function deleteformcatAction(){
			global $mySession;
			$cat_form_id = $this->_getParam('c_form_id');
			$delete_cat_form = $this->modelsuper->Super_Delete('category_form',"c_form_id='".$cat_form_id."'");
			if($delete_cat_form->success){
				$mySession->successMsg  = "category form deleted successfully ";
			}else {
					$mySession->errorMsg = " Please Check Information Again ... ! ";
				}

			exit();
 	}

// end delete category form ............................


// view category form  
 	public function viewcategoryformAction(){
				global $mySession; 
				
				
				$service_id = $this->getRequest()->getParam('service_id');
				$this->modelStatic = new Application_Model_Static();
				$Category = $this->modelStatic->Super_Get('services','service_id= "'.$service_id.'"',"fetch");
				//prd($Category);
				$this->view->pageHeading = $Category['service_name']." "."form";
				//$this->view->pageDescription = $Category['service_name']."form";
				$this->view->pageIcon = "fa fa-sitemap";
				$form = new Application_Form_ProjectForm();
				$form->postnewjob();
				
				$this->view->category_id = $service_id;
				
				$IsJobPosted = $this->modelStatic->Super_Get('job',"job_cat_id='".$service_id."'",'fetchAll',array('fields'=>'group_concat(job_ques_id) as job_quess_id'));
				$this->view->IsJobPosted = $IsJobPosted;
				
				
				
				$array_job_ids=explode(",",$IsJobPosted[0]['job_quess_id']);
				
				if(isset($IsJobPosted[0]['job_quess_id']) && !empty($IsJobPosted[0]['job_quess_id']))
				{
				$array_job_ids_unique=array_unique($array_job_ids);
				
				$this->view->array_job_ids_unique=$array_job_ids_unique;
				}else{
					$array_job_ids_unique = '';
				$this->view->array_job_ids_unique=$array_job_ids_unique;
					}
	//prd($this->view->array_job_ids_unique);
				
				if($service_id!='' && $service_id!='/d+'){
					$serviceData = $this->modelStatic->Super_Get('services',"service_id='".$service_id."'",'fetch');
					if(!($serviceData)){ 
						$mySession->infoMsg = "No Such service Exists in the database...!";
						$this->_redirect('/privatepanel/service');
					}
				}
				// prd($form);
				$cat_form = $this->modelProject->getcategoryform($service_id);
				$this->view->category_form = $cat_form;
				
				 $this->view->form =$form;
	 
	 }
// view add category form ............................



}