<?php
class Privatepanel_Bootstrap extends Zend_Application_Module_Bootstrap	
{
   
 	function _initApplication(){ 
	 
	}



	protected function _initNavigation() {
		// make sure the layout is loaded
		$this->bootstrap('layout');
		
		// get the view of the layout
		$layout = $this->getResource('layout');		
		$view = $layout->getView();
		
		//load the navigation xml
		$config = new Zend_Config_Xml(ROOT_PATH.'/private/navigation.xml','nav');
		
 	 
		// pass the navigation xml to the zend_navigation component
		$nav = new Zend_Navigation($config);
		
		
		
		// pass the zend_navigation component to the view of the layout 
		$view->navigation($nav);
		

	}
	
 
    /**
     * return the default bootstrap of the app
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    protected function _getBootstrap()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $bootstrap =  $frontController->getParam('bootstrap');	//deb($bootstrap);
        return $bootstrap;
    }
	
	public function _initSession(){
		
		Zend_Session::start();
		global $mySession;
		$mySession = new Zend_Session_Namespace('privatepanel');
		
	}
 
 
	public function _initRouter()
	{
		$this->FrontController = Zend_Controller_Front::getInstance();
		
		$this->router = $this->FrontController->getRouter();  
		$this->appRoutes=array();
		   
 	}

	
	
	protected  function _initSiteRouters(){	
		
		$this->appRoutes['admin_dashboard']= new Zend_Controller_Router_Route('/privatepanel',array('module'=>'privatepanel','controller'=>'index','action'=>'index'));
		$this->appRoutes['admin_profile']= new Zend_Controller_Router_Route('/privatepanel/profile',array('module'=>'privatepanel','controller'=>'profile','action'=>'index'));
		$this->appRoutes['admin_logout']= new Zend_Controller_Router_Route('/privatepanel/logout',array('module'=>'privatepanel','controller'=>'index','action'=>'logout'));

 		$this->appRoutes['admin_site_configs']= new Zend_Controller_Router_Route('/privatepanel/site-configurations',array('module'=>'privatepanel','controller'=>'static','action'=>'siteconfigs'));	
		$this->appRoutes['admin_email_templates']= new Zend_Controller_Router_Route('/privatepanel/email-templates',array('module'=>'privatepanel','controller'=>'static','action'=>'showmailtemplates'));	
		
		$this->appRoutes['admin_static_pages']= new Zend_Controller_Router_Route('/privatepanel/static-pages',array('module'=>'privatepanel','controller'=>'static','action'=>'index'));	
		//$this->appRoutes['admin_content_block']= new Zend_Controller_Router_Route('/admin/content-blocks',array('module'=>'admin','controller'=>'static','action'=>'contentblock'));	
		
		
		/* Pages */
		$this->appRoutes['admin_delete_page']= new Zend_Controller_Router_Route('/privatepanel/delete-pages',array('module'=>'privatepanel','controller'=>'static','action'=>'removepages'));
		$this->appRoutes['admin_delete_contentblocks']= new Zend_Controller_Router_Route('/privatepanel/delete-content-block',array('module'=>'privatepanel','controller'=>'static','action'=>'removeblock')); 		

		
		/* Graphic Media */
		$this->appRoutes['admin_graphic_media']= new Zend_Controller_Router_Route('/privatepanel/graphic-media',array('module'=>'privatepanel','controller'=>'static','action'=>'graphicmedia'));
 		$this->appRoutes['admin_add_graphic_media']= new Zend_Controller_Router_Route('/privatepanel/add-graphic-media',array('module'=>'privatepanel','controller'=>'static','action'=>'addgraphicmedia'));
		$this->appRoutes['admin_edit_graphic_media']= new Zend_Controller_Router_Route('/privatepanel/edit-graphic-media/:media_id',array('module'=>'privatepanel','controller'=>'static','action'=>'editgraphicmedia','media_id'=>'\d+'));
		$this->appRoutes['admin_delete_graphic_media']= new Zend_Controller_Router_Route('/privatepanel/delete-graphic-media/:media_id',array('module'=>'privatepanel','controller'=>'static','action'=>'deletegraphicmedia','media_id'=>'\d+'));
 
       /*Product*/
	  	$this->appRoutes['admin_product_category']= new Zend_Controller_Router_Route('/privatepanel/product-category',array('module'=>'privatepanel','controller'=>'product','action'=>'categories'));
 	  	$this->appRoutes['admin_product_colors']= new Zend_Controller_Router_Route('/privatepanel/product-colors',array('module'=>'privatepanel','controller'=>'product','action'=>'colors'));
 	  	$this->appRoutes['admin_product_design']= new Zend_Controller_Router_Route('/privatepanel/product-design',array('module'=>'privatepanel','controller'=>'product','action'=>'design'));
        $this->appRoutes['admin_product']= new Zend_Controller_Router_Route('/privatepanel/allproducts',array('module'=>'privatepanel','controller'=>'product','action'=>'index'));
	   /*Product*/
 	   
	   
	   /* Admin  Profile Controller Routings*/
	   $this->appRoutes['update_profile_admin'] = new Zend_Controller_Router_Route('/privatepanel/profile-update',array('module'=>'privatepanel','controller'=>'profile','action'=>'index'));
	   $this->appRoutes['update_image_admin'] = new Zend_Controller_Router_Route('/privatepanel/profile-image',array('module'=>'privatepanel','controller'=>'profile','action'=>'image'));
	   $this->appRoutes['update_password_admin'] = new Zend_Controller_Router_Route('/privatepanel/change-password',array('module'=>'privatepanel','controller'=>'profile','action'=>'password'));
	   $this->appRoutes['update_notification_admin'] = new Zend_Controller_Router_Route('/privatepanel/notification-settings',array('module'=>'privatepanel','controller'=>'profile','action'=>'notification'));
	    /*  End  */
		
	    /* Admin  User Controller Routings*/
		  $this->appRoutes['update_user_info'] = new Zend_Controller_Router_Route('/privatepanel/user/account/:user_id',array('module'=>'privatepanel','controller'=>'user','action'=>'account','user_id'=>'\+d'));
	    $this->appRoutes['update_user_pswd'] = new Zend_Controller_Router_Route('/privatepanel/user/password/:user_id',array('module'=>'privatepanel','controller'=>'user','action'=>'password','user_id'=>'\+d'));
	    $this->appRoutes['send_user_email_verify_mail'] = new Zend_Controller_Router_Route('/privatepanel/user/sendverification/:user_id',array('module'=>'privatepanel','controller'=>'user','action'=>'sendverification','user_id'=>'\+d'));
	   /*  End  */

	 /*edit category form*/
	   $this->appRoutes['admin_cat_add_form'] = new Zend_Controller_Router_Route('Add_Category_form/:service_id/:c_form_id', array ('module' => 'privatepanel','controller' => 'service','action' => 'addformcat','service_id'=>'\+d','c_form_id'=>'\+d'));
	   
	     $this->appRoutes['admin_cat_addaa_form'] = new Zend_Controller_Router_Route('Add_Category_form/:service_id', array ('module' => 'privatepanel','controller' => 'service','action' => 'addformcat','service_id'=>'\+d'));
		  $this->appRoutes['admin_cat_add_real_form'] = new Zend_Controller_Router_Route('Add_Category_form/:service_id', array ('module' => 'privatepanel','controller' => 'service','action' => 'addformcat','service_id'=>'\+d'));
	 /*  End edit category form */   
	   
 /* Manage service  by service controller*/
		$this->appRoutes['admin_services']= new Zend_Controller_Router_Route('/privatepanel/manage-service',array('module'=>'privatepanel','controller'=>'service',
		'action'=>'index'));
		/*$this->appRoutes['remove_services']= new Zend_Controller_Router_Route('/privatepanel/remove-services',array('module'=>BACKEND_NAME,'controller'=>'service',
		'action'=>'removeservices'));*/
	   
/*end manage service */	   
 		
		
	 
 
 
 
 
 			$this->appRoutes['admin_login']= new Zend_Controller_Router_Route('/privatepanel/login',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'login')
			);	
			
			$this->appRoutes['admin']= new Zend_Controller_Router_Route('/privatepanel',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'index')
			);	
			
			
			
			//forgotpassword
			$this->appRoutes['forgotpassword']= new Zend_Controller_Router_Route('/privatepanel/forgot-password',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'forgotpassword')
			);
			
			$this->appRoutes['resetpassword']= new Zend_Controller_Router_Route('/privatepanel/resetpassword',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'resetpassword')
			);
			
			$this->appRoutes['logout']= new Zend_Controller_Router_Route('/privatepanel/logout',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'logout')
			);
			$this->appRoutes['changepassword']= new Zend_Controller_Router_Route('/privatepanel/changepassword',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'changepassword')
			);
			
		 
			$this->appRoutes['editemailtemps']= new Zend_Controller_Router_Route('/privatepanel/static-content/edit/content_id/:content_id',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'pages',
                                            'action' => 'edit-template',"content_id")
			);
			
		
 			  
			
			/* End New Routers */
			
			
			
 			$this->appRoutes['login']= new Zend_Controller_Router_Route('/privatepanel/login',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'login')
			);	
			$this->appRoutes['logout']= new Zend_Controller_Router_Route('/privatepanel/logout',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'logout')
			);
			$this->appRoutes['changepassword']= new Zend_Controller_Router_Route('/privatepanel/changepassword',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'index',
                                            'action' => 'changepassword')
			);
			
		 
		 $this->appRoutes['editemailtemps']= new Zend_Controller_Router_Route('/privatepanel/static-content/edit/content_id/:content_id',
                                     array('module'     => 'privatepanel', 
									 		'controller' => 'pages',
                                            'action' => 'edit-template',"content_id")
			);
			
	}
	 protected  function _initSetupRouting(){	
			
			foreach($this->appRoutes as $key=>$cRouter){
			
				$this->router->addRoute( $key,  $cRouter );
			}
			
	}
	
	
    /**
     * return the bootstrap object for the active module
     * @return Offshoot_Application_Module_Bootstrap
     */
	 
    public function _getActiveBootstrap($activeModuleName)
    {
        $moduleList = $this->_getBootstrap()->getResource('modules');
        if (isset($moduleList[$activeModuleName])) {
        }
 
        return null;
    }



}








