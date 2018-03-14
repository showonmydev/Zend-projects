<?php
class IndexController extends Zend_Controller_Action
{
	public function init(){	
		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
  	}

	
 	public function indexAction(){	
		
		global $objSession ; 
  		$this->view->pageHeading = "Home";
		
		
 		$modelSlider = new Application_Model_Slider();
		$this->view->page_slug="Home";
		$images = $modelSlider->fetchImages();
		$auth = Zend_Auth::getInstance(); 
		$this->view->slider_images = $images ;
		$this->view->show='home';
		
		$extra=array(
			'fields'=>"service_id,service_name"
			);
			
		$services=$this->modelSuper->Super_Get("services","service_parent_id=0 && service_sub_parent_id=0","fetchAll",$extra);
		$this->view->services = $services;
		
/*SEARCH QUERY*/		
		
		if($this->getRequest()->isPost()){
			$data_post = $this->getRequest()->getPost();
			
			if(isset($data_post['searchServices']) && $data_post['searchServices']!=''){
				$service_type=$data_post['searchServices'];

				
			$extra1= array(
					'fields'=>array('service_id','service_name','service_parent_id'),
			);
			$whereServiceName = "service_name like '%".$data_post['searchServices']."%'";			
			$SQLQuery=$this->modelSuper->Super_Get("services",$whereServiceName,"fetchAll",$extra1);
		//	prd($SQLQuery);
			}
		}
// recent Blog			
		$joinArr1 = array(
				'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
		);
		$RecentBlogs=$this->modelSuper->Super_Get("blogs","1","fetchAll",array('order'=>"blog_modified_on desc"),$joinArr1);
		$this->view->RecentBlogs = $RecentBlogs;
		
/*SEARCH QUERY*/
  	}
	
	public function getajaxcategoryAction(){	
		global $objSession ; 
		$this->_helper->layout->disableLayout();
		$service_id = $this->getRequest()->getParam('id');
		$getHomeCat = $this->modelSuper->Super_Get('home_category','home_service_parent_id="'.$service_id.'"',"fetch");
		$extra=array(
			'limit'=>9
			);
		$categories=$this->modelSuper->Super_Get("services","service_parent_id='".$service_id."' && service_sub_parent_id=0 and service_id IN (".$getHomeCat['home_service_sub_parent_id'].")","fetchAll",$extra);
		$this->view->categories = $categories;
  	}
	
	public function seeallserviceAction(){	
		global $objSession ; 
		$services=$this->modelSuper->Super_Get("services","service_parent_id=0 && service_sub_parent_id=0","fetchAll");
		$this->view->services = $services;
		//prd($allservices);
  	}
	
	public function seeallcategoryAction(){	
		global $objSession ; 
		/*decode Id code*/
		/*$service_id =siteIdDecode($this->getRequest()->getParam("service_id"),"category");*/
		
		$service_name =$this->getRequest()->getParam("service_name");
		$pjfcID =$this->getRequest()->getParam("pjfcID");
		$this->view->showjobPostModel = $pjfcID;
		$this->view->msn_for_job = $service_name;
		
		$MoveToservice = $this->modelSuper->Super_Get("services","service_name='".$service_name."'","fetch");
		$this->view->MoveToservice = $MoveToservice;
	//	prd($MoveToservice);
		
		$extra=array(
			'limit'=>12,
			'order'=>'service_name asc'
			);
	
		$categoriestop=$this->modelSuper->Super_Get("services","service_parent_id='".$MoveToservice['service_id']."' && service_sub_parent_id=0","fetchAll",$extra);
		$this->view->categoriestop = $categoriestop;
		//prn(count($categoriestop));
	
		$categorieslist = $this->modelSuper->getAdapter()->query("select * from services where (service_parent_id='".$MoveToservice['service_id']."' && service_sub_parent_id=0) order by service_name asc limit 12,100")->fetchAll();
		//$categorieslist=$this->modelSuper->Super_Get("services","service_parent_id='".$service_id."' && service_sub_parent_id=0","fetchAll");
		$this->view->categorieslist = $categorieslist;
		//prn($categories);
		
		
		$extra=array(
			'order'=>'service_name'
			);
	
		$cat_searchlist=$this->modelSuper->Super_Get("services","service_parent_id='".$MoveToservice['service_id']."' && service_sub_parent_id=0","fetchAll",$extra);
		$this->view->cat_searchlist = $cat_searchlist;
		
		
  	}


	public function seeallsubcategoryAction(){	
		global $objSession ; 
			/*$service_id =siteIdDecode($this->getRequest()->getParam("service_id"),"category");*/
			
		$service_id =$this->getRequest()->getParam("service_id");
		$service_parent_id =$this->getRequest()->getParam("service_parent_id");
		$pjfcID =$this->getRequest()->getParam("pjfcID");
		$this->view->showjobPostModel = $pjfcID;
		$this->view->sid_for_job = $service_id;
		$this->view->spid_for_job = $service_parent_id;
		
		$service_name = $this->modelSuper->Super_Get("services","service_id='".$service_id."'","fetch");
		$this->view->service_name = $service_name;
		
		$this->view->sn_for_job = $service_name['service_name'];
		
		$extra=array(
			'order'=>'service_name asc'
			);
	
		$subcategories=$this->modelSuper->Super_Get("services","service_parent_id='".$service_parent_id."' && service_sub_parent_id='".$service_id."'","fetchAll",$extra);
		$this->view->subcategories = $subcategories;
		
		
		if(isset($this->view->user->user_id))
		{
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
		}
		
		$extraaa=array(
			'order'=>'service_name',
			'fields'=>ltrim(rtrim('service_name'))
			);
	
		$sub_cat_searchlist=$this->modelSuper->Super_Get("services","service_parent_id='".$service_parent_id."' && service_sub_parent_id='".$service_id."'","fetchAll",$extraaa);
		$this->view->sub_cat_searchlist = $sub_cat_searchlist;
		
		
		
  	}

	public function noticeulAction(){
		
		$this->_helper->layout->disableLayout();
			
			if(isset($this->view->user->user_id)){
			$extra = array(
				'fields'=>array('totalMsg'=> new Zend_Db_Expr('ifnull(count(DISTINCT(notification_id)),0)')),
				'groub'=>'notification_id'
			);
			$total_massages=$this->modelSuper->Super_Get("notifications","notification_reciver='".$this->view->user->user_id."' and notification_status='0'","fetch",$extra);
			$this->view->total_massages = $total_massages;
			
			$extra_sender= array(
				'fields'=> array('notification_date','notification_message','notification_job_id','notification_type','notification_sender'),
				'order'=>'notification_date desc'
			);
			$joinArr = array(
				'0'=>array("users","user_id=notification_sender","left",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image')),
				'1'=>array("job","job_id=notification_job_id","left",array('job_cat_id')),
				'2'=>array("services","service_id=job_cat_id","left",array('service_name')),
				);
			$massages=$this->modelSuper->Super_Get("notifications","notification_reciver='".$this->view->user->user_id."' and notification_status='0' ","fetchAll",$extra_sender,$joinArr);
			$this->view->massages = $massages;

			}
		}
		
	public function commingsoonAction(){
		
		//if(isset($_POST) && !empty($_POST)){	
//			// take input from user
//				$isUserExist="select * from profile where email = '".$_POST['email']."'";
//				$res1 = $con->query($isUserExist);
//			// check is user allready exist	
//				if($res1->num_rows>0){ 
//				   $_SESSION['message']='User with this email already exist';
//				   header("Location:dob_validation.php");
//				}
//		}
		
		
		if($this->getRequest()->isPost()){
				$data =$this->getRequest()->getPost();
				//prn($data);
				$is_exist = $this->modelSuper->Super_get("comingsoon",'newsletter_email="'.$data['newsletter_email'].'"','fetch');
				//prn($is_exist);
							$data_to_insert  = $data;
							//prd($data_to_insert);
							$isInsert=$this->modelSuper->Super_Insert("comingsoon",$data_to_insert);
							//prd($isInsert);
							$modelEmail = new Application_Model_Email();
							$is_send_user =  $modelEmail->sendEmail("coming_soon_user",$data);
							$is_send_admin =  $modelEmail->sendEmail("coming_soon_admin",$data);
							//prd($is_send);
							$objSession->successMsg = "Mail Successfully Send ";
							//$this->_redirect("https://www.casaadvisor.com?success=1");
							$this->_redirect("http://192.168.0.99/CasaAdvisor?success=1");
			}
		
	}

	
	 public function checksubscriberemailAction()
	 {
		  $newsletter_email = $this->_getParam('newsletter_email');

		  $where='newsletter_email="'.$newsletter_email.'"';	 
		 
		  $check_data=$this->modelSuper->Super_Get("comingsoon",$where,"fetch");
		 
		  if(count($check_data)>0 && !empty($check_data))
		  {
		   echo json_encode("Subscriber email id already exists, try another one");
		  }
		  else
		  {
		   echo json_encode("true");
		  }
		  exit();
	 }
	
	
}


