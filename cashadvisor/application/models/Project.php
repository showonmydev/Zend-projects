<?php

    class Application_Model_Project extends Zend_Db_Table_Abstract
    {
		public function init(){
			$this->modelsuper = new Application_Model_SuperModel();
		}
	

		
	public function  editJob($id=false){
		$extra= array();
		
		
		$joinArr = array(
					'0'=>array("job_details","detail_job_id=job_id","right",array('job_cat_form_id','job_cat_form_option','job_form_cat_other_option','job_form_other_option_text')),
					'1'=>array("services","service_id=job_cat_id","right",array('*')),
					);
		$Edit_JOB_details = $this->modelsuper->Super_Get('job_posted_client','job_id="'.$id.'"',"fetchAll",$extra,$joinArr);
		//prn($Edit_JOB_details);
		
		return $Edit_JOB_details;
	
		}
		
	public function  viewComment($id=false)
		{
			$where = "c_job_id='".$id."'";
			
			$res= $this->modelsuper->Super_Get("communication",$where,"fetchAll");
			//prd($res);
			return $res;
	
		}
	public function  getcategoryform($id=false)
		{
			$where = "category_id='".$id."'";
			$extra=array(
		     'order' => 'c_form_id DESC', 
			 );
			$res= $this->modelsuper->Super_Get("category_form",$where,"fetchAll",$extra);
			//prd($res);
			return $res;
			
		}
	
	public function userlocation($code=false){
		$sender_location = $this->modelsuper->Super_Get("zips","zip='".$code."'","fetch");
		return $sender_location;
			
			$distanceQuery = "(((ACOS(SIN(".$filter_lat." * PI() / 180)* SIN(user_lat * PI() / 180) + COS(".$filter_lat." * PI() / 180)  COS(user_lat * PI() / 180) * COS((".$filter_long."-user_long) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344))";
    // for KM -> 60  1.1515  1.609344, for MILES -> 60 * 1.1515
		}
	
	public function getcredits($user_id=false){
			$extra = array("fields"=>array("SUM(package_points) as total_points"));
			$purched=$this->modelsuper->Super_Get("package_purchased","pp_request_by='".$user_id."' and pp_pay_status='1'","fetch",$extra);
			
			$extraspend = array("fields"=>array("SUM(package_points) as total_points"));
			$spend=$this->modelsuper->Super_Get("package_purchased","pp_request_by='".$user_id."' and pp_pay_status='0'","fetch",$extraspend);
			
			$Credit_IN_wallet = $purched['total_points'] - $spend['total_points'];
			return $Credit_IN_wallet;
		}	
		
	
		
	}