<?php
class Privatepanel_BlogController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelBlog = new Application_Model_Blog();
		$this->modelStatic  = new Application_Model_Static();
		$this->view->pageIcon = "fa  fa-users";
		$this->modelsuper = new Application_Model_SuperModel();
		$this->modelProject = new Application_Model_Project();
    }
 	
	
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage Blog";
		$this->view->pageDescription = "manage all blog";
		$this->view->pageIcon = "fa fa-rss";
		
 	}
	
/*GET BLOG*/	
	 public function getblogAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('blog_id','blog_title','blog_tag','blog_url','blog_modified_on','blog_cat_id','blog_content','blog_image','blog_category_title');
		$sIndexColumn = 'blog_id';
		$sTable = 'blogs';
 		
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
		
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		 FROM  $sTable 
		 join blog_categories on bc_id = blog_cat_id
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
			$row[]='<img src="'.HTTP_BLOG_IMAGES_PATH.'/60/'.$row1['blog_image'].'" />';
			$row[]=$row1['blog_title'];
			$row[]=$row1['blog_category_title'];
			$row[] = $row1['blog_tag'];
			$row[] = $row1['blog_url'];
			$row[]=$row1['blog_modified_on'];
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/blog/addblog?blog_id='.$row1['blog_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			$row[] =  '<a target="_blank" href="'.$this->view->url(array('blog_url'=>$row1['blog_url'],'isadmin'=>'adminview'),'front_blog').'" class="btn btn-xs yellow"> view <i class="fa fa-eye"></i></a>';
  			$output['aaData'][] = $row;
			$j++;
		}
		echo json_encode( $output );
		exit();
  	}
/* end GET BLOG*/

// get blog url
 	public function blogurlAction()
		 {
		  $blog_url = $this->_getParam('blog_url');
		  $blog_id = $this->_getParam('blog_id');

		 $blog_url=encodeBlogUrl($blog_url);
		  $where='blog_url="'.$blog_url.'"';	 
		
		  if(isset($blog_id) && $blog_id>0)
		  {
		   $where.=' and blog_id!="'.$blog_id.'" and blog_url="'.$blog_url.'"'; 
		  }
		
		  $check_data=$this->modelsuper->Super_Get("blogs",$where,"fetch");
		 
		  if(count($check_data)>0 && !empty($check_data))
		  {
		   echo json_encode("Blog url already exists, try another one");
		  }
		  else
		  {
		   echo json_encode("true");
		  }
		  exit();
	 }
// ADD BLOG  
	public function addblogAction(){
				global $mySession; 
				$this->view->pageHeading = "Add Blog";
				$this->view->pageDescription = "add blog";
				$this->view->pageIcon = "fa fa-tags";
				 
				$form = new Application_Form_BlogForm();
				
				$form->addblog();
				
				// prd($form);
				$BlogId = $this->getRequest()->getParam('blog_id');
				$this->view->blog_id = $BlogId;
				
				if($BlogId==''){
					 $form->blog_image->setRequired(true);
					 $this->view->pageHeading = "Add Blog";
				     $this->view->pageDescription = "add blog";
				     $this->view->pageIcon = "fa fa-tags";
					}
				
				if($BlogId!='' && $BlogId!='/d+'){
					$BlogData = $this->modelStatic->Super_Get('blogs',"blog_id='".$BlogId."'",'fetch');
					$this->view->blog_image = $BlogData['blog_image'];
					if(!($BlogData)){ 
						$mySession->infoMsg = "No Such Blog  Exists in the database...!";
						$this->_redirect('/privatepanel/blog');
					}
					else {
						
						$this->view->pageHeading = "Edit Blog";
						$this->view->pageDescription = "Edit blog";
						$this->view->pageIcon = "fa fa-tags";
						$form->populate($BlogData);
					}
				}
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						if(!empty($BlogData)){
// edit blog image						
						if(isset($_FILES['blog_image']['name']) && !empty($_FILES['blog_image']['name']))
						{
							 unlink(BLOG_IMAGES_PATH."/".$BlogData['blog_image']);
							  unlink(BLOG_IMAGES_PATH."/60/".$BlogData['blog_image']);
							 $is_uploaded = $this->_handle_blog_image();
							 $UpdateBlog['blog_image'] = $is_uploaded;
							
						}
						else
						{	
							 $is_uploaded = $BlogData['blog_image'];
							 $UpdateBlog['blog_image'] = $is_uploaded;
						}
						
// end edit blog image						
						$UpdateBlog['blog_title'] = $data_to_insert['blog_title'];
						$UpdateBlog['blog_cat_id'] = $data_to_insert['blog_cat_id'];
						$UpdateBlog['blog_content'] = $data_to_insert['blog_content'];
						$UpdateBlog['blog_tag'] = $data_to_insert['blog_tag'];
						$UpdateBlog['blog_url'] = encodeBlogUrl($data_to_insert['blog_url']);
						$UpdateBlog['blog_modified_on'] =date('Y-m-d H:i:s');
							
							
							$is_insert = $this->modelStatic->add("blogs",$UpdateBlog,"blog_id='".$BlogId."'");
							$mySession->successMsg  = "Blog successfully updated ";
							
						}else{
// add blog image			
			
							$is_uploaded = $this->_handle_blog_image();
							//prd($is_uploaded);
								if($is_uploaded!=''){
									$InsertBlog['blog_image'] = $is_uploaded;
								}
								
// end add blog image						
							$InsertBlog['blog_title'] = $data_to_insert['blog_title'];
							$InsertBlog['blog_cat_id'] = $data_to_insert['blog_cat_id'];
							$InsertBlog['blog_content'] = $data_to_insert['blog_content'];
							$InsertBlog['blog_tag'] = $data_to_insert['blog_tag'];
					    	$InsertBlog['blog_url'] = encodeBlogUrl($data_to_insert['blog_url']);
							$InsertBlog['blog_modified_on'] =date('Y-m-d H:i:s');
						//	prd($InsertBlog);
							$is_insert = $this->modelStatic->add("blogs",$InsertBlog);
							
							$mySession->successMsg  = "Blog  successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/blog");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
				
	 
	 
	 }
// end ADD BLOG ............................

//  REMOVE BLOG
	public function  removeblogAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['blogs']) && count($formData['blogs'])) {
			$blogs = implode(",",$formData['blogs']) ;
  			$removed = $this->modelUser->getAdapter()->delete('blogs',"blog_id IN (".$blogs.")");
 			$mySession->successMsg="Blog Deleted Successfully";
 		}
  		$this->_redirect("privatepanel/blog");
 	}
// end REMOVE BLOG...........................

// view blog
	public function viewblogAction(){
		$blogID = $this->getRequest()->getParam('blog_id');
		$this->view->blogID =$blogID;
		
// view full blog		 
		$joinArrUrl = array(
				'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
		);

		$GetFullBlog = $this->modelStatic->Super_Get("blogs","blog_id = '".$blogID."'","fetch",array(),$joinArrUrl);
		$this->view->GetFullBlog =$GetFullBlog;
		}

	
		public function blogcatAction(){
			global $mySession; 
			$this->view->pageHeading = "Manage Blog Category";
			$this->view->pageDescription = "manage all blog category";
			$this->view->pageIcon = "fa fa-tags";
			
		}
/*GET BLOG Category*/	
	   	public function getblogcatAction(){
		
		$this->dbObj = Zend_Registry::get('db');
 
 		$aColumns = array('bc_id','blog_category_title','blog_category_modified_on');
		$sIndexColumn = 'bc_id';
		$sTable = 'blog_categories';
 		
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
			$row[]=$row1['blog_category_title'];
			$row[]=$row1['blog_category_modified_on'];
			$row[] =  '<a href="'.SITE_HTTP_URL.'/privatepanel/blog/addblogcat?bc_id='.$row1['bc_id'].'" class="btn btn-xs green"> Edit <i class="fa fa-pencil"></i></a>';
			
  			$output['aaData'][] = $row;

			$j++;
	
		}
		
		echo json_encode( $output );
		exit();
  	}
/* end GET BLOG Category*/
	
// ADD BLOG Category  
		public function addblogcatAction(){

				global $mySession; 
				$this->view->pageHeading = "Add Blog Category";
				$this->view->pageDescription = "add blog category";
				$this->view->pageIcon = "fa fa-tags";
				 
				$form = new Application_Form_BlogForm();
				
				$form->addblogcat();
				// prd($form);
				$blogCatId = $this->getRequest()->getParam('bc_id');
				if($blogCatId!='' && $blogCatId!='/d+'){
					$blogCatData = $this->modelStatic->Super_Get('blog_categories',"bc_id='".$blogCatId."'",'fetch');
					if(!($blogCatData)){ 
						$this->view->pageHeading = "Add Blog Category";
						$this->view->pageDescription = "add blog category";
						$this->view->pageIcon = "fa fa-tags";
						$mySession->infoMsg = "No Such Blog Category Exists in the database...!";
						$this->_redirect('/privatepanel/blog/blogcat');
					}
					else {
						$this->view->pageHeading = "Add Blog Category";
						$this->view->pageDescription = "add blog category";
						$this->view->pageIcon = "fa fa-tags";
						
						$form->populate($blogCatData);
					}
				}
					
				if($this->getRequest()->isPost()) {
					$data_form = $this->getRequest()->getPost();
					if($form->isValid($data_form)){
						$data_to_insert = $form->getValues();
						$UpdateBlogCat['blog_category_title'] = $data_to_insert['blog_category_title'];
						$UpdateBlogCat['blog_category_modified_on'] =date('Y-m-d H:i:s');
						if(!empty($blogCatData)){
							$is_insert = $this->modelStatic->add("blog_categories",$UpdateBlogCat,"bc_id='".$blogCatId."'");
						
							$mySession->successMsg  = "Blog category successfully updated ";
						}else{
							$InsertBlogCat['blog_category_title'] = $data_to_insert['blog_category_title'];
							$InsertBlogCat['blog_category_modified_on'] =date('Y-m-d H:i:s');
							$is_insert = $this->modelStatic->add("blog_categories",$InsertBlogCat);
							$mySession->successMsg  = "Blog Category successfully added ";
						}
						
						if($is_insert->success){
							$this->_redirect("privatepanel/blog/blogcat");
						}
						$mySession->errorMsg  = $is_insert->message;
					}else{
						$mySession->errorMsg = " Please Check Information Again ... ! ";
					}
				 }
				 $this->view->form =$form;
				 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
				
	 
	 
	 }
// end ADD BLOG Category ............................

//  REMOVE BLOG Category
		public function  removeblogcatAction(){
		global $mySession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$formData = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() &&  isset($formData['blog_categories']) && count($formData['blog_categories'])) {
			$blog_categories = implode(",",$formData['blog_categories']) ;
  			$removed = $this->modelUser->getAdapter()->delete('blog_categories',"bc_id IN (".$blog_categories.")");
 			$mySession->successMsg="Blog Category Deleted Successfuly";
 		}
  		$this->_redirect("privatepanel/blog/blogcat");
 	}
// end REMOVE BLOG Category...........................


/*handle_blog_image*/
	private function _handle_blog_image(){
		$adapter = new Zend_File_Transfer_Adapter_Http();
		//get Img Detail
		$image = $adapter->getFileInfo('blog_image');
		// get image orignal name
		$video_extension = $image['blog_image']['name'];
		// get image name array name + extn
		$extension = explode('.',$image['blog_image']['name']); 
		// get only exten
		$extension = array_pop($extension);
		// give a new temp name to image
		$name_for_image = md5(rand(1,999).time()).".".$extension;
		// save image in folder with temp new name
		rename(BLOG_IMAGES_PATH .'/'.$video_extension ,  BLOG_IMAGES_PATH .'/'.$name_for_image);
		
		// get temp named img
		$source = BLOG_IMAGES_PATH.'/'.$name_for_image;
		// give temp named img an another name
		$newName = "Img_".time()."-".rand(1,100000).".".$extension;
		// give final new named img in folder
		$destination = BLOG_IMAGES_PATH.'/'.$newName;
		// decrease img size in KB 
		$d = compress($source,$destination,70);
		// delete temp named img from folder
		unlink($source);
		
		// save final new named img in folder
		$thumb_config = array("source_path"=>BLOG_IMAGES_PATH,"name"=> $newName);
		
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>BLOG_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>BLOG_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>BLOG_IMAGES_PATH."/400","crop"=>true ,"size"=>400,"ratio"=>false)));

	//	prn($name_for_image);
		return $newName ;
		
  	}



}