<?php
class Application_Model_Services extends Zend_Db_Table_Abstract
{
	
		public function init(){
			$this->modelsuper = new Application_Model_SuperModel();
		}
		

		public function categorylist($id=false)
		{
			$data=$this->modelsuper->Super_Get("services","service_sub_parent_id='0' and service_parent_id='".$id."'","fetchAll");
			//prd($data);
			$ss = array_column($data,'service_name','service_id');
			return $ss;
		}
		
		public function isalreadyimagequestion($id=false)
		{
			$isQuesWithImage=$this->modelsuper->Super_Get("category_form","category_id='".$id."' and c_form_field_type='4'","fetch");
			return $isQuesWithImage;
		}

		
		public function servicesdata()
		{
			$servicesinfo=$this->modelsuper->Super_Get('services','service_parent_id=0 and service_status="1"','fetchAll');
			//$categoryinfo = "select * from services where service_parent_id=0"; 
			$serviceslist = array();
			 $serviceslist[0]['key']="";
			 $serviceslist[0]['value']="select services";
			
			$sk=1;
			foreach ($servicesinfo as $key=>$values) {		
				$serviceslist[$sk]['key'] = $values['service_id'];
				$serviceslist[$sk]['value'] = $values['service_name'];
				$sk++;
			}		
			return $serviceslist;
		
		}
		
		public function categorydata()
		{
			$categorylist = array();
			$categorylist[0]['key']="";
			$categorylist[0]['value']="select category";
			return $categorylist;
			exit;
		}
		public function subcategorydata()
		{
			$subcategorylist = array();
			$subcategorylist[0]['key']="";
			$subcategorylist[0]['value']="select sub category";
			return $subcategorylist;
			exit;
		}
}