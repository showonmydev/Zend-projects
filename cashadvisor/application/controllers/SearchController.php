<?php

class SearchController extends Zend_Controller_Action
{
	public function init(){	
		$this->modelsuper = new Application_Model_SuperModel();
		$this->modelStatic = new Application_Model_Static();
		$this->modelProject = new Application_Model_Project();
		
   	}
	
 	public function indexAction(){
		$service_type='';$post_code=$post_code1='';
		$serv_cat='';
		
		$arrayJoin = array(
				'0'=>array("zips","zip=user_zip_code","left",array('*')),
			//	'1'=>array("reviews","review_sp=user_id","left",array('review_ratings','review_title')),
				//'1'=>array("reviews","review_sp=user_id","inner",array('totalReviews'=> new Zend_Db_Expr('ifnull(count(DISTINCT(review_id)),0)'))),
		);

		$serv_cat='';
		$where = '';
		
		if($this->getRequest()->isPost()){
			$data_post = $this->getRequest()->getPost();
			
			if(isset($data_post['service_type']) && $data_post['service_type']!=''){
				$service_type=$data_post['service_type'];

/*DIRECT SQL SEARCH PROVIDER FROM user_services TABLE*/
				
			/*$SQLQuery = $this->modelStatic->getAdapter()->query("SELECT group_concat(distinct(us_user_id)) as service_user FROM `services` INNER JOIN `user_services` ON (us_service_id=service_id or us_service_parent_id=service_id or us_service_sub_parent_id=service_id) WHERE (service_name like '%".$data_post['service_type']."%')")->fetch();
			*/
/*END DIRECT SQL SEARCH PROVIDER FROM user_services TABLE*/	
		
			$extra1= array(
					//'fields'=>array('service_id'),
			);
			$joinArr1 = array(
						'0'=>array("user_services","us_service_id=service_id || us_service_parent_id=service_id || us_service_sub_parent_id=service_id","Inner",array('service_user'=>new Zend_Db_Expr('GROUP_CONCAT(distinct(user_services.us_user_id))'))),
						);
			$whereServiceName = "service_name like '%".$data_post['service_type']."%'";			
			$SQLQuery=$this->modelStatic->Super_Get("services",$whereServiceName,"fetch",$extra1,$joinArr1);
			
	$service_type=$data_post['service_type'];

		}
		
		
		if(isset($data_post['service_type']) && $data_post['service_type']!='' && $SQLQuery['service_user']!=''){
				$where="(user_type='service_provider' and user_status='1' and user_id IN(".$SQLQuery['service_user']."))";
		}
		else{
			if(isset($data_post['post_code']) && $data_post['post_code']!=''){
				 $where="(user_type='service_provider' and user_status='1')";
			}else{
				 $where="(user_type='service_provider' and user_status='1' and user_id='0')";
				}
		}
				
			if(isset($data_post['post_code']) && $data_post['post_code']!=''){
				$post_code1=$data_post['post_code'];
				$state = $city = $data_post['post_code'];
				$post_code = explode(',',$post_code1);
				$zip = $post_code[0];
				$where2 = $where1= ' and (user_zip_code like "%'.$zip.'%" or state like "%'.$state.'%" or  city like "%'.$city.'%"  or  zip like "%'.$zip.'%" )';
				if(count($post_code)==3){
					$zip1 = $this->modelStatic->Super_Get("zips","zip='".$post_code[0]."'","fetch");
					$state1 = $this->modelStatic->Super_Get("zips","state='".$post_code[0]."'","fetch");
					$city1 = $this->modelStatic->Super_Get("zips","city='".$post_code[0]."'","fetch");
					$zip2 = $this->modelStatic->Super_Get("zips","zip='".$post_code[1]."'","fetch");
					$state2 = $this->modelStatic->Super_Get("zips","state='".$post_code[1]."'","fetch");
					$city2 = $this->modelStatic->Super_Get("zips","city='".$post_code[1]."'","fetch");
					$zip3 = $this->modelStatic->Super_Get("zips","zip='".$post_code[2]."'","fetch");
					$state3 = $this->modelStatic->Super_Get("zips","state='".$post_code[2]."'","fetch");
					$city3 = $this->modelStatic->Super_Get("zips","city='".$post_code[2]."'","fetch");

					if(isset($zip1) || isset($zip2) || isset($zip3)){
						
						if(isset($zip1) && $zip1!=''){
							$where2 = ' and ( user_zip_code like "%'.$zip1['zip'].'%")';
						}
						if(isset($zip2) && $zip2!=''){
							$where2 = ' and ( user_zip_code like "%'.$zip2['zip'].'%")';
						}
						if(isset($zip3) && $zip3!=''){
							$where2 = ' and ( user_zip_code like "%'.$zip3['zip'].'%")';
						}
					}
					if(isset($state1) || isset($state2) || isset($state3)){
						if(isset($state1) && $state1!=''){
							$where2.= ' and ( state like "%'.$state1['state'].'%")';
						}
						if(isset($state2) && $state2!=''){
							$where2.= ' and ( state like "%'.$state2['state'].'%")';
						}
						if(isset($state3) && $state3!=''){
							$where2.= ' and ( state like "%'.$state3['state'].'%")';
						}
					}

					if(isset($city1) || isset($city2) || isset($city3)){
						if(isset($city1) && $city1!=''){
							$where2.= ' and ( city like "%'.$city1['city'].'%")';
						}
						if(isset($city2) && $city2!=''){
							$where2.= ' and ( city like "%'.$city2['city'].'%")';
						}
						if(isset($city3) && $city3!=''){
							$where2.= ' and ( city like "%'.$city3['city'].'%")';
						}
					}
					$where1 = $where2;
				} else if(count($post_code)==2){
					$zip1 = $this->modelStatic->Super_Get("zips","zip='".$post_code[0]."'","fetch");
					$state1 = $this->modelStatic->Super_Get("zips","state='".$post_code[0]."'","fetch");
					$city1 = $this->modelStatic->Super_Get("zips","city='".$post_code[0]."'","fetch");
					$zip2 = $this->modelStatic->Super_Get("zips","zip='".$post_code[1]."'","fetch");
					$state2 = $this->modelStatic->Super_Get("zips","state='".$post_code[1]."'","fetch");
					$city2 = $this->modelStatic->Super_Get("zips","city='".$post_code[1]."'","fetch");
					if(isset($zip1) || isset($zip2)){
						if(isset($zip1) && $zip1!=''){
							$where2 = ' and ( user_zip_code like "'.$zip1['zip'].'%")';
						}
						if(isset($zip2) && $zip2!=''){
							$where2 = ' and ( user_zip_code like "'.$zip2['zip'].'%")';
						}
					}
					
					if(isset($state1) || isset($state2)){
						if(isset($state1) && $state1!=''){
							$where2.= ' and ( state like "'.$state1['state'].'%")';
						}
						if(isset($state2) && $state2!=''){
							$where2.= ' and ( state like "%'.$state2['state'].'%")';
						}
					}
					if(isset($city1) || isset($city2)){
						if(isset($city1) && $city1!=''){
							$where2.= ' and ( city like "'.$city1['city'].'%")';
						}
						if(isset($city2) && $city2!=''){
							$where2.= ' and ( city like "%'.$city2['city'].'%")';
						}
					}
					$where1 = $where2;
				}
				
				$where.= $where1;
			}

			$ExtraArray = array(
							'fields'=>array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image','user_business_name','user_id'),
						);
			$getResult=$this->modelStatic->Super_Get("users",$where,"fetchAll",$ExtraArray,$arrayJoin);
			//prd($getResult);
		}else {
			$extra= array(
					'group'=>'us_user_id',
					'fields'=>array('us_user_id as user_id'),
			);
			$joinArr = array(
						'0'=>array("users","user_id=us_user_id","right",array('CONCAT(user_first_name," ",user_last_name) as user_name','user_image','user_business_name')),
						'1'=>array("zips","zip=user_zip_code","left",array('*')),
					//	'2'=>array("reviews","review_sp=us_user_id","left",array('review_ratings','review_title')),
						);
						
			$getResult=$this->modelStatic->Super_Get("user_services","1","fetchAll",$extra,$joinArr);
			}
	///	prd($getResult);
		$this->view->provider_data = $getResult;
		$this->view->service_type = $service_type;
		$this->view->post_code = $post_code1;
		$this->view->serv_cat=$serv_cat;
		}
	
	public function homesearchAction(){
			$params = $this->_request->getParams();
			$pjfcID =$this->getRequest()->getParam("pjfcID");
			$this->view->showjobPostModel = $pjfcID;

			$extra = array(
				'fields'=>array('MainServiceIDArray'=>new Zend_Db_Expr('GROUP_CONCAT(service_name)'))
				);
			$GetMainServices = $this->modelStatic->Super_Get("services","service_parent_id='0' && service_sub_parent_id='0'","fetch",$extra);
			
			$MainServiceList = (explode(",",$GetMainServices['MainServiceIDArray']));
			
			if(isset($params['services']) && $params['services']!=''){ 
				$SearchedService=$params['services'];
				if(in_array($SearchedService, $MainServiceList)){
					$this->_helper->getHelper("Redirector")->gotoRoute(array("service_name"=>$SearchedService),'front_seeallcategory');
				}
				else{
					$extra1= array(
						'fields'=>array('*')
					);
					$whereServiceName = "service_name like '%".$params['services']."%' && service_parent_id!='0' && service_sub_parent_id='0'";			
					$HomePageSearchQuery=$this->modelStatic->Super_Get("services",$whereServiceName,"fetchAll",$extra1);
					$this->view->ResultData = $HomePageSearchQuery;
				}
			}
			
			foreach(range('a','z') as $val){
					$allSubcat[$val] =	$this->modelStatic->Super_Get('services',"service_sub_parent_id!=0  and service_name like '".$val."%'","fetchAll",array('fields'=>array('service_name','service_id'),'order'=>'service_name'));	
			}
			$this->view->allSubcat = $allSubcat;
			$SearchedService = $params['services'];
			$this->view->Searchedservice = $SearchedService;
			
		//prd($GetMainServices);
		}	
		
	public function allzipcodesAction(){
		$zip = $this->getRequest()->getParam('zip');
		/*$infoall = $this->modelStatic->Super_Get('zips',"zip like '".$zip."%' or state like '".$zip."%' or city like '".$zip."%'","fetchAll",array('limit'=>'10'));*/
		
			$infoall = $this->modelStatic->Super_Get('zips',"1","fetchAll",array("fields"=>array("REPLACE(city,' ','') as new_city","zips.*"),"limit"=>'10',"having"=>"new_city like '".$zip."%' or city like '".$zip."%' or zip like '".$zip."%' or state like '".$zip."%'"));
		
		
		print_r(json_encode($infoall));
		exit();
		
	}	
	
// view pro profile
	public function providerprofileAction(){
			global $objSession ; 
			$user_id =$this->getRequest()->getParam("user_id");
			$this->view->SP_id = $user_id;
			
			$IswantReview = $this->getRequest()->getParam("feedback");
			$this->view->IswantReview = $IswantReview;
			
			
			$user_details=$this->modelStatic->Super_Get("users","user_id='".$user_id."'","fetch");
			$this->view->user_details = $user_details;
			
			$extra= array();
			$join =  array(
						'0'=>array("services","service_id=us_service_sub_parent_id","left",array('service_name')),
						);
			$user_services=$this->modelStatic->Super_Get("user_services","us_user_id='".$user_id."'","fetchAll",$extra,$join);
			$this->view->user_services = $user_services;
			
			$extraReview = array();
			$joinReview = array(
						'0'=>array("users as client","client.user_id=review_client","left",array('CONCAT(client.user_first_name," ",client.user_last_name) as user_name','user_image')),
						'1'=>array("users as pro","pro.user_id='".$user_id."'","left",array('pro.user_first_name as proName')),
			);
			$ReviewData=$this->modelStatic->Super_Get("reviews","review_sp='".$user_id."'","fetchAll",$extraReview,$joinReview);
			$this->view->ReviewData = $ReviewData;
			
			$TotalRate = '';
			foreach($ReviewData as $eachReview){
				$TotalRate += $eachReview['review_ratings'];
			}
			
			if(count($ReviewData)!=0){
				$AvegargeRate = $TotalRate/count($ReviewData);
			}else{
				$AvegargeRate = 0;
				}
			$this->view->AvegargeRate = $AvegargeRate;
			
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