<?php
class ServiceproviderController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		$this->modelUser = new Application_Model_User();
			global $objSession ; 
		if($this->view->user->user_type!='service_provider')
		{
			$objSession->errorMsg="You are not allowed to access this page";
			$this->_redirect("profile");
		}
		
   	}
	
	public function indexAction(){	
 		global $objSession ; 
   		$content = $this->modelStatic->getPage(40); 
		$this->view->btn=1;
		
		$form = new Application_Form_User();
		
		$this->view->show = "change_service" ; 
		
		$form->serviceprovider($this->view->user->user_id);
		$x=array('type'=>$this->view->user->user_profession);
		$form->populate($x);
		$this->view->form=$form;
	
	}
	
	 public function updateserviceAction(){
		
		 global $objSession;
		 $this->view->show = "change_service" ; 
		$auth = Zend_Auth::getInstance(); 
		$all_added=$this->modelStatic->Super_Get("user_services",'us_user_id="'.$this->view->user->user_id.'"','fetch',array('fields'=>array('group_concat(us_service_sub_parent_id) as ids','group_concat(us_service_parent_id) as par_ids')));
		//prn($all_added);
		$this->view->all_added=$all_added;
			$type='';
			$request = $this->getRequest();
			$type_id='';
			if(isset($_GET['type']) && $_GET['type']!=''){
				$type = $_GET['type'];
				$type_id = $_GET['type'];
				$this->view->type_id=$type_id;
			}
			
			
			$getCategory = $this->modelStatic->Super_Get('services',"service_parent_id='".$type_id."'","fetchAll");
			$subCat = array();
			$i=0;
			foreach($getCategory as $value){
				
				$getSubCat = $this->modelStatic->Super_Get("services","service_sub_parent_id='".$value['service_id']."'","fetchAll");
				if($getSubCat){
					$subCat[$value['service_id'].'_'.$i] = $getSubCat;
					$i++;
				}
			}
			
			$form = new  Application_Form_User();
			$form->serviceprovider($type);	
			
				if ($this->getRequest()->isPost()){ // Post Form Data
		
				
 			$posted_data = $this->getRequest()->getPost();
			if($form->isValid($posted_data)){
						$data_to_insert  = $form->getValues();
						//prd($_POST);
						
						$data_to_insert['user_profession'] = $_POST['type'];
						unset($data_to_insert['type']);
						$isInsert=$this->modelStatic->Super_Insert("users",$data_to_insert,'user_id="'.$this->view->user->user_id.'"');
							//prd($isInsert);
						if($isInsert->success)
						{
							$isInsert=$this->modelStatic->Super_Delete("user_services",'us_user_id="'.$this->view->user->user_id.'"');
						
							$ServicesToInsert= array();
							foreach($_POST['service_cat'] as $val)
							{
								if(isset($_POST['service_sub_cat_'.$val]))
								{
									foreach($_POST['service_sub_cat_'.$val] as $values)
									{
										$ServicesToInsert['us_user_id'] =$this->view->user->user_id;
										$ServicesToInsert['us_service_id'] =$_POST['type'];
										$ServicesToInsert['us_service_parent_id'] = $val;
										$ServicesToInsert['us_service_sub_parent_id'] = $values;
										$n=$this->modelStatic->Super_Insert('user_services',$ServicesToInsert);
										//prn($n);
									}
								}
							}
							//prd("vbn");	
						
							$objSession->successMsg="Record updated successfully.";
						 $this->_redirect("serviceprovider");
						}
				}
			
			else
			{
			
			}
		}
			$this->view->Categories = $getCategory ;
			$this->view->subCategory = $subCat ;
			$this->view->form = $form ;
        
	 }
	
	
	public function otherinfoAction(){	
 		global $objSession ; 
		$form = new Application_Form_User();
		$this->view->show = "change_other_info" ; 
		$form->otherinfo();
		//prd($this->view->user->user_id);
		$form->populate((array)$this->view->user);
		
		
		if($this->getRequest()->isPost()){

		$data_post = $this->getRequest()->getPost();
		
		if($form->isValid($data_post)){
			$data_to_update = $form->getValues() ;
			
 			$is_update  = $this->modelStatic->Super_Insert("users",$data_to_update ,"user_id='". $this->view->user->user_id."'");
			/*$is_update=$this->modelStatic->Super_Insert("users",$data_to_update,'user_id="'.$this->view->user->user_id.'"');*/
			//prd($is_update);
			if(is_object($is_update) and $is_update->success){//prd($is_update->row_affected);
				if($is_update->row_affected > 0){
					$objSession->successMsg = " Profile Information Changed Successfully ";
				}else{//echo "h4re";die;
					$objSession->infoMsg = " New Information is Same as previous one ";
				}
				 $this->_redirect("serviceprovider/otherinfo");
			}
			
			$objSession->errorMsg  = $is_update->message; ;
			
		}else{
			$objSession->errorMsg = "Please Check Information Again ...!";	
		}
	}
		
		$this->view->form = $form;
		
	}

}