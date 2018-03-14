<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	

 	protected function _initLoaderResources()
    {
		
        $this->getResourceLoader()->addResourceType('controller', 'controllers/', 'Controller');
    }
	
	protected function _initAutoloader()
 	{
 	   new Zend_Application_Module_Autoloader(array(
 	      'namespace' => 'Application',
 	      'basePath'  => APPLICATION_PATH,
 	   ));
 	}
	
	    protected function _initHelperPath() {
        $view = $this->bootstrap('view')->getResource('view');
        $view->setHelperPath(APPLICATION_PATH . '/views/helpers', 'Application_View_Helper');
    }
	
	protected function _initDoctype()
	{
		$this->bootstrap('view');
 		$view = $this->getResource('view');
  		$view->setEncoding('UTF-8');
		$view->doctype('HTML5');
 		$view->headMeta()->appendHttpEquiv('Content-Type',  'text/html;charset=utf-8');
	}
	

	
	protected function _initDB() {
		
		$dbConfig = new Zend_Config_Ini(ROOT_PATH.'/private/db.ini',APPLICATION_ENV);
		$dbConfig =$dbConfig->resources->db;
	 
       	$dbAdapter = Zend_Db::factory($dbConfig->adapter, array(
            'host'     => $dbConfig->params->hostname,
            'username' => $dbConfig->params->username,
            'password' => $dbConfig->params->password,
            'dbname'   => $dbConfig->params->dbname
         ));
 		
		//$dbAdapter->exec("SET time_zone='".$dbConfig->params->timezone."'");
		
        Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

        Zend_Registry::set('db', $dbAdapter);
		 
 		
		Zend_Session::start();
		global $objSession;
		$objSession = new Zend_Session_Namespace('default');
    }
 	
	protected function _initAppKeysToRegistry(){
		$appkeys = new Zend_Config_Ini(ROOT_PATH . '/private/appkeys.ini');
		Zend_Registry::set('keys', $appkeys);
	}
	

	public function _initPlugins(){ // Add Plugin path
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Application_Plugin_SetLayout());
	}


		protected  function _initApplication(){   
	 
 			$this->FrontController=Zend_Controller_Front::getInstance();
			$this->FrontController->setControllerDirectory(array(
				'default' => '../application/controllers',
				'privatepanel'    => '../application/privatepanel/controllers'
			));
			
			// $this->FrontController->setDefaultControllerName('login'); 
			//	$this->FrontController->throwExceptions(false);
		
			$registry = Zend_Registry::getInstance();
			$registry->set("flash_error",false);
			
		 	// Add a 'foo' module directory:
			// $this->FrontController->setParam('prefixDefaultModule', true);
			// $this->FrontController->setDefaultModule('publisher');
			// $this->FrontController->setDefaultAction("index") ;
			// $this->FrontController->addControllerDirectory('../modules/foo/controllers', 'foo');
			
	 	
	}
	
	
	public function _initRouter()
        {
            $this->FrontController = Zend_Controller_Front::getInstance();
            $this->router = $this->FrontController->getRouter();
            $this->appRoutes = array ();
        }
	
	
	/* Site Routers */
	protected function _initSiteRouters(){
		$this->appRoutes['front_page'] = new Zend_Controller_Router_Route('index', array ('module' => 'default','controller' => 'index','action' => 'index'));
		$this->appRoutes['forgeotpassowrd'] = new Zend_Controller_Router_Route('forgeotpassowrd', array ('module' => 'default','controller' => 'index','action' => 'index'));
				/* Fixed Front Redirects */
		$this->appRoutes['front_login'] = new Zend_Controller_Router_Route('login/:if_job_post/:pjfcID', array('module' => 'default','controller' => 'user','action' => 'login','if_job_post'=>'\+s','pjfcID'=>'\+d'));
		
		$this->appRoutes['front_changepassword'] = new Zend_Controller_Router_Route('change-password', array ('module' => 'default','controller' => 'user','action' => 'changepassword'));
		$this->appRoutes['front_logout'] = new Zend_Controller_Router_Route('logout', array ('module' => 'default','controller' => 'user','action' => 'logout'));
		$this->appRoutes['front_purchase'] = new Zend_Controller_Router_Route('purchase/:job_id', array ('module' => 'default','controller' => 'package','action' => 'index','job_id'=>'\+d'));
		$this->appRoutes['package_purchased_history'] = new Zend_Controller_Router_Route('package_history', array ('module' => 'default','controller' => 'package','action' => 'packagepurchasehistory'));
		$this->appRoutes['pay_amount'] = new Zend_Controller_Router_Route('pay/:job_id', array ('module' => 'default','controller' => 'package','action' => 'pay','job_id'=>'\+d'));
		
		$this->appRoutes['pay_by_card'] = new Zend_Controller_Router_Route('cardPayment/:job_id', array ('module' => 'default','controller' => 'project','action' => 'paybycard','job_id'=>'\+d'));

		$this->appRoutes['front_project'] = new Zend_Controller_Router_Route('projects/:job_id', array ('module' => 'default','controller' => 'project','action' => 'index','job_id'=>'\+d')); 
		$this->appRoutes['front_addnewproject'] = new Zend_Controller_Router_Route('postjob', array ('module' => 'default','controller' => 'project','action' => 'addnewproject'));
		
		$this->appRoutes['front_newjob'] = new Zend_Controller_Router_Route('newjob', array ('module' => 'default','controller' => 'project','action' => 'newjob'));
		
		$this->appRoutes['front_editproject'] = new Zend_Controller_Router_Route('editjob/:job_id', array ('module' => 'default','controller' => 'project','action' =>'editproject','job_id'=>'\+d'));
		
		$this->appRoutes['front_viewquote'] = new Zend_Controller_Router_Route('QuoteRequest/:job_id/:quote_sender', array ('module' => 'default','controller' => 'project','action' => 'quoterequest','job_id'=>'\+d','quote_sender'=>'\+d'));
		
		//$this->appRoutes['front_allNotification'] = new Zend_Controller_Router_Route('JobRequests', array ('module' => 'default','controller' => 'project','action' => 'jobrequest'));		
		$this->appRoutes['front_allNotification'] = new Zend_Controller_Router_Route('JobRequests/:page', array ('module'=>'default','controller'=>'project','action'=>'jobrequest','page'=>''));
		
		$this->appRoutes['front_myJob'] = new Zend_Controller_Router_Route('MyJob/:jobtype/:page', array ('module'=>'default','controller'=>'project','action'=>'myjob','page'=>'1','jobtype'=>'inprogress'));
		
		$this->appRoutes['front_review'] = new Zend_Controller_Router_Route('reviews/:proposalID/', array ('module'=>'default','controller'=>'project','action'=>'postreview'));
		
		$this->appRoutes['front_NotifiClient'] = new Zend_Controller_Router_Route('QuoteRecived/:page', array ('module' => 'default','controller' => 'project','action' => 'receivedquote','page'=>''));

		$this->appRoutes['front_bid'] = new Zend_Controller_Router_Route('sendQuote/:job_id', array ('module' => 'default','controller' => 'project','action' => 'sendquote'));
		$this->appRoutes['front_view_proposal'] = new Zend_Controller_Router_Route('view-job-proposal/:job_id', array ('module' => 'default','controller' => 'project','action' => 'viewproposal'));
		$this->appRoutes['front_view_quotemessage'] = new Zend_Controller_Router_Route('Quote-Messages/:job_id', array ('module' => 'default','controller' => 'project','action' => 'quotemessage'));
		
		
		$this->appRoutes['front_searchproviders'] = new Zend_Controller_Router_Route('ServiceProviders', array ('module' => 'default','controller' => 'search','action' => 'index'));
		
		
		
		
		$this->appRoutes['front_register'] = new Zend_Controller_Router_Route('register', array('module' => 'default','controller' => 'user','action' => 'register'));
		$this->appRoutes['become_pro'] = new Zend_Controller_Router_Route('registerpro', array('module' => 'default','controller' => 'user','action' => 'registerpro'));
		
		$this->appRoutes['front_seeallservice'] = new Zend_Controller_Router_Route('Services',array('module'=>'default','controller'=>'index','action'=>'seeallservice'));
		
		$this->appRoutes['front_seeallcategory'] = new Zend_Controller_Router_Route('Service/:service_name/:pjfcID',array('module'=>'default','controller'=>'index','action'=>'seeallcategory','service_name'=>'\+s','pjfcID'=>'\+d'));
		
				//$this->appRoutes['front_seeallcategory'] = new Zend_Controller_Router_Route('Service/:service_id/:service_name',array('module'=>'default','controller'=>'index','action'=>'seeallcategory','service_id'=>'\+d','service_name'=>'\+s'));

		
		$this->appRoutes['front_seeallsubcategory'] = new Zend_Controller_Router_Route('Category/:service_id/:service_parent_id/:service_name/:pjfcID',array('module'=>'default','controller'=>'index','action'=>'seeallsubcategory','service_id'=>'\+d','service_parent_id'=>'\+d','service_name'=>'\s+','pjfcID'=>'\+d'));

		
		$this->appRoutes['front_forgotpassword'] = new Zend_Controller_Router_Route('forgot-password', array ('module' => 'default','controller' => 'user','action' => 'forgotpassword'));
		$this->appRoutes['facebook_signup'] = new Zend_Controller_Router_Route('social/fblogin', array ('module' => 'default','controller' => 'social','action' => 'fblogin'));
		$this->appRoutes['twitter_signup'] = new Zend_Controller_Router_Route('social/twitterlogin', array ('module' => 'default','controller' => 'social','action' => 'twitterlogin'));



		$this->appRoutes['about_us'] = new Zend_Controller_Router_Route('about-us', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'1'));
		
/*link direct static pages*/
		
		/*$this->appRoutes['about'] = new Zend_Controller_Router_Route('aboutUS', array ('module'=>'default','controller'=>'static','action'=>'aboutUS'));
		$this->appRoutes['how_it_work'] = new Zend_Controller_Router_Route('howitworks', array ('module'=>'default','controller'=>'static','action'=>'howitworks'));*/
		
/*end direct link*/
		$this->appRoutes['faq'] = new Zend_Controller_Router_Route('faq', array ('module'=>'default','controller'=>'static','action'=>'faq'));
		$this->appRoutes['privacy'] = new Zend_Controller_Router_Route('privacy', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'2'));
		$this->appRoutes['terms'] = new Zend_Controller_Router_Route('terms', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'21'));
		//$this->appRoutes['faq'] = new Zend_Controller_Router_Route('help', array ('module'=>'default','controller'=>'static','action'=>'faq'));
		$this->appRoutes['how_it_work'] = new Zend_Controller_Router_Route('how_it_work', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'3'));
	
		$this->appRoutes['contact_us'] = new Zend_Controller_Router_Route('contact-us', array ('module'=>'default','controller'=>'static','action'=>'contact'));
				
		$this->appRoutes['front_profile'] = new Zend_Controller_Router_Route('profile', array ('module'=>'default','controller'=>'profile','action'=>'index'));
		$this->appRoutes['front_profile_settings'] = new Zend_Controller_Router_Route('settings', array ('module'=>'default','controller'=>'profile','action'=>'accountsettings'));
		$this->appRoutes['front_user_accountpage'] = new Zend_Controller_Router_Route('user-account', array ('module'=>'default','controller'=>'profile','action'=>'accountpage'));
		$this->appRoutes['front_user_mainprofile'] = new Zend_Controller_Router_Route('Dashboard/:user_id', array ('module'=>'default','controller'=>'profile','action'=>'mainprofile','user_id'=>'\+d'));
		
		$this->appRoutes['front_proProfile'] = new Zend_Controller_Router_Route('pro-profile/:user_id', array ('module'=>'default','controller'=>'project','action'=>'providerprofile','user_id'=>'\+d'));

		
		$this->appRoutes['front_user_SPprofile'] = new Zend_Controller_Router_Route('provider-profile/:user_id/:feedback', array ('module'=>'default','controller'=>'search','action'=>'providerprofile','user_id'=>'\+d','feedback'=>'\+s'));

		
		
		$this->appRoutes['front_image'] = new Zend_Controller_Router_Route('change-avatar', array ('module'=>'default','controller'=>'profile','action'=>'image'));
		$this->appRoutes['front_image_crop'] = new Zend_Controller_Router_Route('crop-image', array ('module'=>'default','controller'=>'profile','action'=>'cropimage'));
		
		$this->appRoutes['change_password'] = new Zend_Controller_Router_Route('change-password', array ('module'=>'default','controller'=>'profile','action'=>'password'));
		$this->appRoutes['front_service_provider_services'] = new Zend_Controller_Router_Route('My-services', array ('module'=>'default','controller'=>'serviceprovider','action'=>'index'));
		$this->appRoutes['front_service_provider_other_info'] = new Zend_Controller_Router_Route('My-business-profile', array ('module'=>'default','controller'=>'serviceprovider','action'=>'otherinfo'));
		
		$this->appRoutes['front_blog'] = new Zend_Controller_Router_Route('blog/:blog_url/:isadmin/:page', array ('module' => 'default','controller' => 'blogs','action' => 'index','blog_url'=>'\s+','isadmin'=>'\s+','page'=>'1'));
		
		$this->appRoutes['front_blog_details'] = new Zend_Controller_Router_Route('full-blog/:blog_url', array ('module' => 'default','controller' => 'blog','action' => 'blogdetail','blog_url'=>'\s+')); 


		
		$this->appRoutes['user_cart'] = new Zend_Controller_Router_Route('my-cart', array ('module'=>'default','controller'=>'cart','action'=>'index'));
		
		//$this->appRoutes['search'] = new Zend_Controller_Router_Route('search', array ('module'=>'default','controller'=>'search','action'=>'index'));

		
		/* Routings For Product Categories  */
		
		$db = Zend_Registry::get('db');
		
    
	}
	
	

	protected function _initSetupRouting(){
		foreach ($this->appRoutes as $key => $cRouter)
		{
			$this->router->addRoute($key, $cRouter);
		}
		
/*			prd($this);*/
	}
	
	protected function _initTranslator()
	{
		
		$enLangData = require_once(ROOT_PATH.'/private/languages/en.php');
		$deLangData = require_once(ROOT_PATH.'/private/languages/fr.php');
 		$translate = new Zend_Translate(
			array(
				'adapter' => 'array',
				'content' => $enLangData,
				'locale'  => 'en',
			)
		);
		$translate->addTranslation(
			array(
				'content' => $deLangData,
				'locale'  => 'fr',
				'clear'   => true
			)
		);
		if(SITE_STAGE == "development"){
			$translate->setLocale('en');
		}else{
			$translate->setLocale('fr');
		}
		
		Zend_Registry::set('Zend_Translate', $translate);
		 
	}
	
	
  
}





/* ------------------------------------------- Functions ---------------------------------  */
function prepareQuery($args){
	$sql=$args[0];
	 $_sqlSplit = preg_split('/(\?|\:[a-zA-Z0-9_]+)/', $sql, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
$params=0;	
	 foreach ($_sqlSplit as $key => $val) {
            if ($val == '?') {
				$_sqlSplit[$key]=$args[1][$params];
				$params++;
			}
	 }
	 
$query=implode($_sqlSplit);	 
	return($query);
}
