<?php
class Privatepanel_AjaxController extends Zend_Controller_Action
{
 
    public function init(){ 
		
	}
	
 
  
  
	public function setstatusAction() {
		 
		 $params = $this->getRequest()->getParams() ;
 		 
 		 $db=Zend_Registry::get("db") ;
		 
		 $data = array(			
					 
					"category"=>array(
					'table'      => "category",
					'field_id'      => "category_id",
					'field_status'      => 'category_status'),
					
					"department"=>array(
					'table'      => "department",
					'field_id'      => "department_id",
					'field_status'      => 'department_status'),
					
					"users"=>array(
					'table'      => "users",
					'field_id'      => "user_id",
					'field_status'      => 'user_status'),
					
					"slider_images"=>array(
					'table'      => "slider_images",
					'field_id'      => "slider_image_id",
					'field_status'      => 'slider_image_status'),
					
					"job"=>array(
					'table'      => "job",
					'field_id'      => "job_id",
					'field_status'      => 'job_enable_disable'),
					
				 
					
  			);
			
		 	if(isset($data[$params['type']])){
				$update_data=array($data[$params['type']]['field_status']=>$params['status']) ;
				try{
					$updated = $db->update($data[$params['type']]['table'], $update_data, $data[$params['type']]['field_id'].' = '.$params['id']);	
					echo json_encode(array("success"=>true,"error"=>false,"message"=> " Status of ".ucwords(str_replace("_"," ",$data[$params['type']]['field_status']))." Successfully Updated "  ));
				}catch(Zend_Exception $e){
					echo json_encode(array("success"=>false,"error"=>true,"exception"=>true,"exception_code"=>$e->getCode(),"message"=>$e->getMessage() ));
				}
			}else{
				echo json_encode(array("success"=>false,"error"=>true,"exception"=>false,"message"=>"Table Not Defined for the Current Request" ));
			}
			

				
			
 			exit();
	}
	
 	
}

