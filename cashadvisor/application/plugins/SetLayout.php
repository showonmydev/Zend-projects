<?php
class Application_Plugin_SetLayout extends Zend_Controller_Plugin_Abstract
{	
	
	protected $_defaultRole = 'all';
	protected $model = '';
	private $acl = '';
	public $roleArr =  array ("0" => "all");
	public $loggedRole = "";
	
	public $restricted = array("user"=>array("register" , "login" , "forgotpassword"));
	
	private $_site_assets  ,$_assets_path , $_view , $_logged_user = false , $view;
	
  
 	/* 	Set Document Type Layout 
	 *	@
	 *  Author  - zend
	 */
	protected function _initDoctype() {
	  
	  $this->bootstrap('view');
	  
	  $view = $this->getResource('view');
	  
	  $view->doctype('XHTML_STRICT');
	  
	  $view->setEncoding('UTF-8');
	}
	
	
  
  	/* 	Pre Dispatch Setting  
	 *	@
	 *  Author  - zend
	 */
    public function preDispatch(Zend_Controller_Request_Abstract $request){ 
		
		global $_site_assets_front_admin ,  $_site_assets_path_front_admin /* Admin / Front Site Assets */ , $_allowed_resources;
		
		$this->db = Zend_Registry::get("db");
		
 		$this->_site_assets = $_site_assets_front_admin ;
		$this->_assets_path = $_site_assets_path_front_admin;
		$this->_allowed_resources = $_allowed_resources ;
		  
		$layout = Zend_Layout::getMvcInstance();		 
		$this->view = $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
	
		$this->modelUser = new Application_Model_User();
		
  		
		/* Module Specific Settings  */
		
		switch($request->getModuleName()){
			
			case 'privatepanel': {/* Admin */
				Zend_Registry::get('Zend_Translate')->setLocale('en');	
 				$this->_set_identity($request);
 				
				$ErrorHandler = Zend_Controller_Front::getInstance()->getPlugin("Zend_Controller_Plugin_ErrorHandler");
 				$ErrorHandler->setErrorHandlerModule("privatepanel");
				
 				$this->_handleRedirects($request);

			 
				//$allContentBloks = $this->db->select()->from('content_block',array("content_block_title"))->query()->fetchAll();
				//$view->all_content_blocks = $allContentBloks ;
					
 			
 				$layout->setLayoutPath(APPLICATION_PATH.'/layouts/privatepanel/');
				
 				
			}/* End Admin */
			break;
			
			
			default:{/* Front  */
			
				$this->_set_identity($request);
   				
				$this->_handleRedirects($request);
				
 			}/* End Default Module */
			
 		}
		
 		$this->_getAssets($request);
		
	
		$this->loadSetting();
 	}
	
	
	
		
	
	
	/* 
	 *	Check User / Admin Identity and Assign user identity to respective views
	 *	@
	 *  Author  - zend
	 */
	private function _set_identity($request){
		
		if($request->getModuleName()=='default'){
 			
			$logged_identity = Zend_Auth::getInstance()->getInstance();
 			
			if($logged_identity->hasIdentity()){
				
				$logged_identity = $logged_identity->getIdentity();
				
				$user_info = (object) $this->modelUser->get($logged_identity->user_id);/**/
				
				if($user_info->user_status!='1' and $request->getActionName()!="logout"){
					$this->getResponse()->setRedirect(APPLICATION_URL . "/logout");//access denied	
				}
 
  				$this->view->user = $this->_logged_user =  $user_info;
				
				$auth = Zend_Auth::getInstance(); 
				
				$auth->getStorage()->write($user_info); //Now seession set is here
				
			}
			else{
				$this->view->user = $this->_logged_user =false;;
			}
		}else{
			
			$Admin_User = Zend_Session::namespaceGet(ADMIN_AUTH_NAMESPACE);
			
 			 
			if(isset($Admin_User['storage'])){
				
				$user_info = (object) $this->modelUser->get($Admin_User['storage']->user_id);
 				
				$auth   = Zend_Auth::getInstance();	
				
				$auth->setStorage(new Zend_Auth_Storage_Session(ADMIN_AUTH_NAMESPACE));
				
				$auth->getStorage()->write($user_info); 
				 
				$Admin_User['storage'] = $user_info ;
				
 				$this->view->user = $this->_logged_user = $Admin_User['storage'];
 			}else{
				$this->view->user = $this->_logged_user =false;
			}
		}
		
	}
	
	
 	
	/* 
	 *	Load CSS and Javascripts Front/Admin Module Specific
	 *	@
	 *  Author  - zend
	 */
 	private function _getAssets($request){
 		foreach($this->_site_assets  as $key=>$values){
 			if(isset($values[$request->getModuleName()][$this->_logged_user ?"user":"guest"]) and count($values[$request->getModuleName()][$this->_logged_user?"user":"guest"])){
 				foreach($values[$request->getModuleName()][$this->_logged_user?"user":"guest"] as $inner_key=>$inner_value){
 					if(is_array($inner_value)){/* Module specific Assets  */
						if(isset($inner_value[$request->getControllerName()])){
							if(isset($inner_value[$request->getControllerName()][$request->getActionName()])){
								foreach($inner_value[$request->getControllerName()][$request->getActionName()] as $moduleKey=>$moduleValue){
									if($key=='css'){	
										$this->view->headLink()->appendStylesheet($this->_assets_path[$key][$request->getModuleName()].$moduleValue);
									}else{
										$this->view->headScript()->appendFile($this->_assets_path[$key][$request->getModuleName()].$moduleValue);
									}
								}
							}
 						}
  					}else{
						if($key=='css'){
							$this->view->headLink()->appendStylesheet($this->_assets_path[$key][$request->getModuleName()].$inner_value);
						}else{
							$this->view->headScript()->appendFile($this->_assets_path[$key][$request->getModuleName()].$inner_value);
						}
					}
				}
			}
		}
		$this->view->headLink()->headLink(array('rel' => 'shortcut icon','href' => HTTP_IMG_PATH.'/fav-con.ico'),'APPEND');
  	}
	
	
	
	/* 	Handle Redirects For Admin and Front Module 
	 *	@
	 *  Author  - zend
	 */
	private function _handleRedirects($request){
		
		/* Return if Current Request is related to any public folder or related to any resource */
 		if($request->getControllerName()=="public"){
			return ;
		}
		
		if(!$this->_logged_user){
 			if(!in_array($request->getControllerName(),$this->_allowed_resources[$request->getModuleName()])){
				if(isset($this->_allowed_resources[$request->getModuleName()][$request->getControllerName()]) and is_array($this->_allowed_resources[$request->getModuleName()][$request->getControllerName()])){
					if(in_array($request->getActionName(),$this->_allowed_resources[$request->getModuleName()][$request->getControllerName()])){
						return ;							
					}
				}
				$site_name = explode("/",SITE_HTTP_URL);
				
				if($request->getModuleName()=='privatepanel'){
					$exploder = $request->getModuleName()=='privatepanel'?'privatepanel':array_pop($site_name);
					$exploder = $exploder=="privatepanel"?"/privatepanel":"";
					$redirect_url = explode($exploder,$_SERVER['REQUEST_URI']) ;
					
					$this->_response->setRedirect($request->getBaseUrl().$exploder .'/login?url='.urlencode("/".$exploder.$redirect_url[1]));
				}
				else{
					$exploder =''; 
					$exploder =$exploder;
					$redirect_url=explode($site_name['2'],$_SERVER['REQUEST_URI']);
					if($request->getControllerName()!='payment')
						$this->_response->setRedirect($request->getBaseUrl().$exploder .'/login?url='.urlencode("/".$exploder.$redirect_url[0]));
				}
			}
		}
		else
		{
 			if(!in_array($request->getControllerName(),$this->_allowed_resources[$request->getModuleName()])){
				if(isset($this->_allowed_resources[$request->getModuleName()][$request->getControllerName()]) and is_array($this->_allowed_resources[$request->getModuleName()][$request->getControllerName()])){
					if(in_array($request->getActionName(),$this->_allowed_resources[$request->getModuleName()][$request->getControllerName()])){
						return ;							
					}
				}
				$site_name = explode("/",SITE_HTTP_URL);
				
				$exploder =''; 
				$exploder =$exploder;
				$redirect_url=explode($site_name['2'],$_SERVER['REQUEST_URI']);
				if(($request->getControllerName()=='project' || $request->getControllerName()=='search' || ($request->getControllerName()=='profile' && $request->getActionName()!='accountpage')) && $this->_logged_user->user_email_verified!='1'){
					$this->_response->setRedirect($request->getBaseUrl().$exploder .'/user-account?key=1');
				}
			}
			//--------------------------------------
			
			global $_blocked_resources ;
			
			
			if(is_array($_blocked_resources[$this->_logged_user->user_type])){
				
				foreach($_blocked_resources[$this->_logged_user->user_type] as $key=>$value){
					
					if(is_int($key)){
						if($request->getControllerName()==$value){
							$this->_response->setRedirect($request->getBaseUrl()); // path
							break;
						}
					}elseif($key==$request->getControllerName()){
						
						if(!is_array($value)){
							if($request->getActionName()==$value){
								$this->_response->setRedirect($request->getBaseUrl()."/index");
								break;
							}	
						}else{
							foreach($value as $subValues){
								if($request->getActionName()==$subValues){
									$this->_response->setRedirect($request->getBaseUrl()."/index");
									break;
								}	
							}
						}
					}
										
				}
				
			}
			
			
			
			
		}
	}
	
 
 
  	
	
  	/* 	Load General Setting [Private Function]
	 *	@
	 *  Author  - zend
	 */
	private function loadSetting(){

  		/* Set Configs  */
 		$configuration = $this->db->query('SELECT * FROM config')->fetchAll();
		
 		foreach($configuration as $key=>$config){
			$config_data[$config['config_key']]= $config['config_value'] ;
			$config_groups[$config['config_group']][$config['config_key']]=$config['config_value'];	
		}
		
 		$this->site_configs = $config_data;
 		Zend_Registry::set("site_config",$config_data) ;
		
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;	
		
		 
		
		$view->current_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$view->current_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		
		
 		$view->site_configs=$config_data;  
		$errormessage = Zend_Registry::get("flash_error") ;
		
 		
		/* ADD UNIQUE IP ADDRESS TO DATABSE */
		   $visitorData = $this->db->query('SELECT * FROM site_visitors where visitor_ip_address="'.$_SERVER['REMOTE_ADDR'].'"')->fetchAll();
		   if(count($visitorData)==0){
			  $this->db->query('INSERT INTO `site_visitors`(`visitor_ip_address`,`visitor_date`) VALUES ("'.$_SERVER['REMOTE_ADDR'].'","'.date('Y-m-d').'")');
		   }
	}
 
 
 
	/* 	postDispatch Plugin  
	 *	@
	 *  Author - zend 
	 *	Description - Manage Site Meta and site title for the site 
	 */
   	public function postDispatch(Zend_Controller_Request_Abstract $request){	
	
 	
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		$view->headMeta()->appendName('viewport',"width=device-width, initial-scale=1.0");
		$view->headMeta()->appendName('keywords',$this->site_configs['meta_keyword']);
		$view->headMeta()->appendName('description',$this->site_configs['meta_description']);
		
  		$view->headTitle()->setSeparator(' | ');
		$view->headTitle($this->site_configs['site_title']);
 	
		if(isset($view->pageHeading) and !empty($view->pageHeading))
			$view->headTitle($view->pageHeading);
 		 
  	} 
	
 	
	
    
}
?>