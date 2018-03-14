<?php
class SocialController extends Zend_Controller_Action
{
  	private $modelUser ,$modelContent; 
	 
	public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
		
   	}


	/*Social media sign up*/
	public function fbregisterAction(){
 		global $objSession; 
  		$facebook = new Facebook(array(
			'appId' => Zend_Registry::get("keys")->facebook->appId ,
			'secret' =>Zend_Registry::get("keys")->facebook->secret ,
			'cookie' => false
		));
		$pagetype=$this->_getParam('pagetype');
		$usertype=$this->_getParam('usertype');
		if(isset($usertype) and $usertype!='')
		{
			$objSession->usertype=$usertype;
		}
  		$user = $facebook->getUser();
 		$your_facebook_page=SITE_HTTP_URL.'/social/fbregister/pagetype/cancel';
		
 		if(!$user){
			/*$login_url = $facebook->getLoginUrl(array( 'scope' => 'email'));
  			
			header("Location: " . $login_url);*/
			  if($pagetype=='cancel'){
				$objSession->errorMsg ="Facebook Authentication Process Cancelled..Please try again..";
				$this->_redirect('user/register/user_type/'.$objSession->usertype);
			   }
			   else{
				$login_url = $facebook->getLoginUrl(array( 'scope' => 'email','redirect_uri'=>$your_facebook_page)); 
				header("Location: " . $login_url);
			   }
			   //exit; 
			 
		}else{
			
			try{ 
				 $user_profile =$facebook->api('/me', array('fields' => 'id,email,first_name,last_name,name'));
 			}catch(FacebookApiException $e){
				$objSession->errorMsg = $e->getMessage();
				$this->_redirect('user/register/user_type/'.$objSession->usertype);
				//$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
			}
 			
			/* for Already Exists */
 			
 			//$isExists = $this->modelUser->get(array("where"=>"user_oauth_provider='facebook' and user_oauth_id='".$user_profile['id']."'")) ;
			
			$isExists = $this->modelUser->get(array("where"=>"user_email='".$user_profile['email']."'")) ;
 		
			if(!$isExists){
				
				 $this->modelUser->getAdapter()->beginTransaction();
				  
				 $is_insert = $this->save_fb_data($user_profile);
				 
 				 if(is_object($is_insert) and $is_insert->error){
					$this->modelUser->getAdapter()->rollBack();
					$objSession->errorMsg = $is_insert->message ;
					$this->_redirect('user/register/user_type/'.$objSession->usertype);
 				}
				
				$this->modelUser->getAdapter()->commit();
				$isExists = $is_insert->data ;
				$this->write_auth($isExists);
				$objSession->successMsg = "Logged In Successfully . ";
				unset($objSession->usertype);
				$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
   			} 
			else
			{
				if($isExists['user_status']=='1')
				{
					$this->write_auth($isExists);
					$objSession->successMsg = "Logged In Successfully . ";
					unset($objSession->usertype);
					if($isExists["user_email_verified"]==0)
					{
						 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1'),$isExists['user_id']);
					}
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
				}
				else
				{
					$objSession->errorMsg = "User blocked, please contact customer support.";
					unset($objSession->usertype);
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
				}
			}
			//$this->_redirect("profile/summary");
		}
 		exit();
 	
	}
	
	public function fbloginAction(){
 		global $objSession; 
  		$facebook = new Facebook(array(
			'appId' => Zend_Registry::get("keys")->facebook->appId ,
			'secret' =>Zend_Registry::get("keys")->facebook->secret ,
			'cookie' => false
		));
		$pagetype=$this->_getParam('pagetype');
		$usertype=$this->_getParam('usertype');
		if(isset($usertype) and $usertype!='')
		{
			$objSession->usertype=$usertype;
		}
  		$user = $facebook->getUser();
 		$your_facebook_page=SITE_HTTP_URL.'/social/fblogin/pagetype/cancel';
		
 		if(!$user){
			/*$login_url = $facebook->getLoginUrl(array( 'scope' => 'email'));
  			
			header("Location: " . $login_url);*/
			  if($pagetype=='cancel'){
				$objSession->errorMsg ="Facebook Authentication Process Cancelled..Please try again..";
				$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
				//$this->_redirect('user/login');
			   }
			   else{
				$login_url = $facebook->getLoginUrl(array( 'scope' => 'email','redirect_uri'=>$your_facebook_page)); 
				header("Location: " . $login_url);
			   }
			   //exit; 
			 
		}else{
			
			try{ 
				$user_profile =$facebook->api('/me', array('fields' => 'id,email,first_name,last_name,name'));
 			}catch(FacebookApiException $e){
				$objSession->errorMsg = $e->getMessage();
				
				$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
			}
 			
			/* for Already Exists */
 			
 			//$isExists = $this->modelUser->get(array("where"=>"user_oauth_provider='facebook' and user_oauth_id='".$user_profile['id']."'")) ;
			
			$isExists = $this->modelUser->get(array("where"=>"user_email='".$user_profile['email']."'")) ;
 		
			if(!$isExists){
				
				   $objSession->errorMsg = "Email is invalid.";
				   unset($objSession->usertype);
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
   			} 
			else
			{
				if($isExists['user_status']=='1')
				{
					$this->write_auth($isExists);
					$objSession->successMsg = "Logged In Successfully . ";
					unset($objSession->usertype);
					if($isExists["user_email_verified"]==0)
					{
						 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1'),$isExists['user_id']);
					}
					//$data_for_user=array('user_last_login'=>date("Y-m-d H:i:s"));
				//	$kk=$this->modelSuper->Super_Insert('users',$data_for_user,'user_id="'.$isExists['user_id'].'"');
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
					
				}
				else
				{
					$objSession->errorMsg = "User blocked, please contact customer support.";
					unset($objSession->usertype);
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
				}
			}
			//$this->_redirect("profile/summary");
		}
 		exit();
 	
	}
 	
	/* Insert Data into Database When User is First Time Register Via Facebook */
 	private function save_fb_data($received = false){
		global $objSession ;
		
 		$generated_password = genratePassword($received['name']);
		
		$image_name = $this->receive_profile_image($received , "facebook");
		//prd($received);
		if(isset($objSession->usertype) and $objSession->usertype!='')
		{
			$type=$objSession->usertype;
			if($type == 'advisor'){		
				$tp='advisor';
			}
			else if($type == 'student'){		
				$tp='student';
			}
			$received['user_type']=$tp;
		}
		else
		{
			$received['user_type']='student';
		}

		$exp=explode("@",$received['email']);
 		$data_to_save = array(
			'user_oauth_id' =>$received['id'],
			'user_oauth_provider'=>'facebook',
			'user_login_type'=>'social',
 			'user_image'=>$image_name,
			'user_type'=>'user',
			'user_reset_status'=>'1',
			'user_status'=>'1',
			'user_password'=>md5($generated_password),
			'user_email'=>$received['email'],
			'user_email_verified'=>'1',
			'user_first_name'=>$received['first_name'],
			'user_last_name'=>$received['last_name'],
			'user_full_name'=>$received['first_name'].' '.$received['last_name'],
			'user_created'=>date("Y-m-d H:i:s"),
			//'user_last_login'=>date("Y-m-d H:i:s")
		);
		
 		$inserted = (array) $this->modelUser->add($data_to_save);
		//$isInserted1 = $this->modelUser->addfreeplan($inserted->inserted_id);
		$inserted['data'] = $data_to_save ;
			 
		return (object) $inserted ;

 	}
	
	public function googleloginAction(){
 		global $objSession; 
		$pagetype=$this->_getParam('pagetype');
		$usertype=$this->_getParam('usertype');
		if(isset($usertype) and $usertype!='')
		{
			$objSession->usertype=$usertype;
		}
		$loginyes=$this->_getParam('loginyes');
		if(isset($loginyes) and $loginyes!='')
		{
			$objSession->loginyes=$loginyes;
		}
		$google_client_id 		= '257670526797-cvi9335sbsk9b6c6nuepi1fetu18o5b8.apps.googleusercontent.com';
		$google_client_secret 	= 'PTx-P3_qHsh05eTMlmlz_moI';
		$google_redirect_url 	= APPLICATION_URL.'/social/googlelogin/pagetype/cancel';
		$google_developer_key 	= '257670526797-cvi9335sbsk9b6c6nuepi1fetu18o5b8@developer.gserviceaccount.com';
		
		require_once ROOT_PATH.'/private/google/src/Google_Client.php';
        require_once ROOT_PATH.'/private/google/src/contrib/Google_Oauth2Service.php';
		
  		$gClient = new Google_Client();
		$gClient->setApplicationName('Login to Verifood');
		//$gClient->setApplicationName('Login to http://aze.az/ru/');
		$gClient->setClientId($google_client_id);
		$gClient->setClientSecret($google_client_secret);
		$gClient->setRedirectUri($google_redirect_url);
		$gClient->setDeveloperKey($google_developer_key);
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		
		//If user wish to log out, we just unset Session variable
		if (isset($_REQUEST['reset'])) 
		{
		  unset($objSession->token);
		  $gClient->revokeToken();
		  $this->_redirect($google_redirect_url);
		 // header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
		}
		
		//Redirect user to google authentication page for code, if code is empty.
		//Code is required to aquire Access Token from google
		//Once we have access token, assign token to session variable
		//and we can redirect user back to page and login.
		if (isset($_GET['code'])) 
		{ 
			$gClient->authenticate($_GET['code']);
			$objSession->token= $gClient->getAccessToken(); 
			//header('Location: ' .$google_redirect_url);
			$this->_redirect($google_redirect_url);
			/*echo '<script>window.location="'.$google_redirect_url.'";</script>';*/
			return;
		}
		
		
		if (isset($objSession->token)) 
		{ 
			  $gClient->setAccessToken($objSession->token);
		}
		
		
		if ($gClient->getAccessToken()) 
		{
			  //Get user details if user is logged in
			  $user 				= $google_oauthV2->userinfo->get();
			  $user_id 				= $user['id'];
			  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
			  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
			  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
			  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
			  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
			  $objSession->token	= $gClient->getAccessToken();
		}
		else 
		{
			//get google login url
			 if($pagetype=='cancel'){
				$objSession->errorMsg ="Google Authentication Process Cancelled..Please try again..";
				if($objSession->loginyes==1)
				{
					$this->_redirect('user/login');
				 } else { 
				 	$this->_redirect('user/register/user_type/'.$objSession->usertype);
				}
				
			   }
			   else{
					$authUrl = $gClient->createAuthUrl();
					$this->_redirect($authUrl);
			   }
		}
		
		//HTML page start
		echo '<html xmlns="http://www.w3.org/1999/xhtml">';
		echo '<head>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		echo '<title>Login with Google Plus</title>';
		echo '</head>';
		echo '<body>';
		echo '<h1>Login with Google Plus</h1>';

		if(isset($authUrl)) //user is not logged in, show login button
		{
			    echo '<a class="login" href="'.$authUrl.'"><img src="images/google-login-button.png" /></a>';
		} 
		else // user logged in 
		{
			$isExists = $this->modelUser->get(array("where"=>"user_email='".$user['email']."'")) ;
 		
			if(!$isExists){
				if(isset($objSession->loginyes) and $objSession->loginyes!='')
				{
				   $objSession->errorMsg = "Email is invalid.";
				   unset($objSession->usertype);
				   unset($objSession->loginyes);
					$this->_redirect('login');
				}
				else
				{
					 $this->modelUser->getAdapter()->beginTransaction();
					// unset($objSession->fb_arr);
					
					 $is_insert = $this->save_google_data($user);
					 
					 if(is_object($is_insert) and $is_insert->error){
						$this->modelUser->getAdapter()->rollBack();
						$objSession->errorMsg = $is_insert->message ;
						$this->_redirect('login');
					}
					//  prd($is_insert);
					$this->modelUser->getAdapter()->commit();
					$isExists = $is_insert->data ;
					$this->write_auth($isExists);
					
					unset($objSession->usertype);
					unset($objSession->loginyes);
					$objSession->successMsg = "Logged In Successfully . ";
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
				}
   			} 
			else {
				
				if($isExists['user_status']=='1')
				{
					$this->write_auth($isExists);
					unset($objSession->usertype);
					unset($objSession->loginyes);
					if($isExists["user_email_verified"]==0)
					{
						 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1'),$isExists['user_id']);
					}
					//$data_for_user=array('user_last_login'=>date("Y-m-d H:i:s"));
				//	$kk=$this->modelSuper->Super_Insert('users',$data_for_user,'user_id="'.$isExists['user_id'].'"');
					$objSession->successMsg = "Logged In Successfully.";
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
				}
				else
				{
					$objSession->errorMsg = "User blocked, please contact customer support.";
					if($objSession->loginyes==1)
					{
						$this->_redirect('user/login');
					 } else { 
						$this->_redirect('user/register/user_type/'.$objSession->usertype);
					}
				}
			
			}
		}
		echo '</body></html>';
	}
	
	private function save_google_data($received = false){
		 global $objSession ;
  		$generated_password = genratePassword($received['name']);
		//prd($received);
		//$image_name = $this->receive_profile_image($received , "googleplus");
		if(isset($objSession->usertype) and $objSession->usertype!='')
		{
			$type=$objSession->usertype;
			if($type == 'advisor'){		
				$tp='advisor';
			}
			else if($type == 'student'){		
				$tp='student';
			}
			$received['user_type']=$tp;
		}
		else
		{
			$received['user_type']='student';
		}
 		 $exp1=explode("@",$received['email']);
		 $exp=explode(" ",$received['name']);
  		$data_to_save = array(
			'user_oauth_id' =>$received['id'],
			'user_oauth_provider'=>'google_plus',
			'user_login_type'=>'social',
 			//'user_image'=>$image_name,
			'user_type'=>'user',
			'user_reset_status'=>'1',
			'user_status'=>'1',
			'user_password'=>md5($generated_password),
			'user_email'=>$received['email'],
			'user_email_verified'=>'1',
			'user_first_name'=>$exp[0],
			'user_last_name'=>$exp[1],
			'user_full_name'=>$exp['0'].' '.$exp['1'],
			//'user_last_name'=>$received['name'],
			'user_created'=>date("Y-m-d H:i:s"),
			//'user_last_login'=>date("Y-m-d H:i:s")
		);
  		 
  		$inserted = $this->modelUser->add($data_to_save);
		$inserted ->data = $data_to_save ;
		//$objSession->fb_arr=$data_to_save;
		return $inserted;
 
 	}	
 	
	
	/* Twitter Login  */
	public function twitterloginAction(){
 		
		global $objSession;
		$usertype=$this->_getParam('usertype');
		if(isset($usertype) and $usertype!='')
		{
			$objSession->usertype=$usertype;
		}
		$TwitterOAuth = new TwitterOAuth(Zend_Registry::get("keys")->twitter->oauth_token,Zend_Registry::get("keys")->twitter->oauth_token_secret );   
		
		$oauth_verifier  = $this->_getParam('oauth_verifier');
		
 		if(empty($oauth_verifier)||!isset($_SESSION['socail_login'])){
			
   			$request_token = $TwitterOAuth->getRequestToken(APPLICATION_URL."/social/twitterlogin");
			
			$_SESSION['oauth_token'] = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	
  			if ($TwitterOAuth->http_code == 200){
				$_SESSION['socail_login'] = true ;
				$url = $TwitterOAuth->getAuthorizeURL($request_token['oauth_token']);
   				header("Location: $url");	
			}else{
				$objSession->error = " Twitter Configuration failed . ";
			 	$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
				
			}
  			
		}else{ /* Get Verifier */
			
			 
			$TwitterOAuth = new TwitterOAuth(Zend_Registry::get("keys")->twitter->oauth_token,Zend_Registry::get("keys")->twitter->oauth_token_secret,$_SESSION['oauth_token'],$_SESSION['oauth_token_secret']); 
   			
			$access_token = $TwitterOAuth->getAccessToken($oauth_verifier);			
  			
			$user_info = $TwitterOAuth->get('account/verify_credentials');
			
 			if (isset($user_info->error)){
				$objSession->errorMsg = $user_info->error;
				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
			} 
 
 			// $isExists = $this->modelUser->get(array("where"=>"user_oauth_provider='twitter' and user_oauth_id='".$user_info->id."'")) ;
			 
			 $isExists = $this->modelUser->get(array("where"=>"user_email='".$user_info->email."'")) ;
			 
			if(!empty($isExists)){
 				$this->write_auth($isExists);
 				//$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_profile");
				$this->_redirect("profile/summary");
 			}
			
			$objSession->twitter_login = true ;
			$objSession->twitter_data = $user_info ;
 			
 			/* Get User Email Addresss  */
			$this->_redirect("social/twitterhandler");
 		}
		 exit();
  	}
	
	/* Get Email Address From the User  */
	public function twitterhandlerAction(){
		
		global $objSession ;
		
		if(!isset($objSession->twitter_login)){
			$objSession->errorMsg = "Please Login "; 
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
		}
		
		$this->view->pageHeading = "Twitter Signin";
		
		
		$form = new Application_Form_Register();
		$form->twitter_email();
		 
		if($this->getRequest()->isPost()){
			
			$posted_values = $this->getRequest()->getPost();
			
			if($form->isValid($posted_values)){
				
				$form_data  = $form->getValues();
  
				$received_data  = (array) $objSession->twitter_data ;
				$received_data['email'] = $form_data['user_email'] ;
				
				$this->modelUser->getAdapter()->beginTransaction();
				
  				$is_insert = $this->save_twitter_data($received_data);

				unset($objSession->twitter_login);
				unset($objSession->twitter_data);
				
 				
				if(is_object($is_insert) and $is_insert->success){
					
					$this->modelUser->getAdapter()->commit();
					
					$this->write_auth($is_insert->data);
					
					$objSession->successMsg  = " Complete Your Profile Information ";
					unset($objSession->usertype);
	 				//$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_profile");
					$this->_redirect("profile/summary");
 				}
				
				$this->modelUser->getAdapter()->rollBack();
 				$objSession->errorMsg = " Enable to login... ! Please try again ";
 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
 			}
		}
		
 		$this->view->form = $form ;
 		$this->view->twitter_user = $objSession->twitter_data ;
 	}
	
 	
 	/* Insert Data into Database When User is First Time Register Via Twitter */
	private function save_twitter_data($received = false){
 		global $objSession ;
		
  		$generated_password = genratePassword($received['name']);
		
		$image_name = $this->receive_profile_image($received , "twitter");
 		if(isset($objSession->usertype) and $objSession->usertype!='')
		{
			$type=$objSession->usertype;
			if($type == 'U'){		
				$tp='user';
			}
			else if($type == 'T'){		
				$tp='freelancer';
			}
			$received['user_type']=$tp;
		}
		else
		{
			$received['user_type']='user';
		}
		$exp=explode(" ",$received['name']);
  		$data_to_save = array(
			'user_oauth_id' =>$received['id_str'],
			'user_oauth_provider'=>'twitter',
			'user_login_type'=>'social',
 			'user_image'=>$image_name,
			'user_type'=>'user',
			'user_reset_status'=>'1',
			'user_status'=>'1',
			'user_password'=>md5($generated_password),
			'user_email'=>$received['email'],
			'user_email_verified'=>'1',
			'user_first_name'=>$exp[0],
			'user_last_name'=>$exp[1],
			'user_created'=>date("Y-m-d H:i:s")
		);
  		
  		$inserted = $this->modelUser->add($data_to_save);
		//$isInserted1 = $this->modelUser->addfreeplan($inserted->inserted_id);
		 
		$inserted ->data = $data_to_save ;
		
		return $inserted;
 		
 	}
	
	public function authAction(){
		global $objSession;
		$usertype=$this->_getParam('usertype');
		if(isset($usertype) and $usertype!='')
		{
			$objSession->usertype=$usertype;
		}
		$loginyes=$this->_getParam('loginyes');
		if(isset($loginyes) and $loginyes!='')
		{
			$objSession->loginyes=$loginyes;
		}
		
		$config['base_url']             =   APPLICATION_URL.'/social/auth';
    	$config['callback_url']         =   APPLICATION_URL.'/social/linkdinlogin';
		$config['linkedin_access']      =   Zend_Registry::get("keys")->linkdin->appId ;
		$config['linkedin_secret']      =   Zend_Registry::get("keys")->linkdin->secret ;
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
		$linkedin->getRequestToken();
		$objSession->requestToken = serialize($linkedin->request_token);
		$this->_redirect($linkedin->generateAuthorizeUrl());
		exit();
	}
	
	public function linkdinloginAction(){
	
	global $objSession;
	
	$usertype=$this->_getParam('usertype');
	if(isset($usertype) and $usertype!='')
	{
		$objSession->usertype=$usertype;
	}
	$loginyes=$this->_getParam('loginyes');
	if(isset($loginyes) and $loginyes!='')
	{
		$objSession->loginyes=$loginyes;
	}
    $config['base_url']             =   APPLICATION_URL.'/social/auth';
    $config['callback_url']         =   APPLICATION_URL.'/social/linkdinlogin';
    $config['linkedin_access']      =   Zend_Registry::get("keys")->linkdin->appId ;
    $config['linkedin_secret']      =   Zend_Registry::get("keys")->linkdin->secret ;
	$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
	

   if (isset($_REQUEST['oauth_verifier'])){ 
       $objSession->oauth_verifier     = $_REQUEST['oauth_verifier'];
        $linkedin->request_token    =   unserialize($objSession->requestToken);
        $linkedin->oauth_verifier   =   $objSession->oauth_verifier;
        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);
        $objSession->oauth_access_token= serialize($linkedin->access_token);
        $this->_redirect($config['callback_url']);
        exit;
   }
   else{
        $linkedin->request_token    =   unserialize($objSession->requestTokenrequestToken);
        $linkedin->oauth_verifier   =   $objSession->oauth_verifier;
        $linkedin->access_token     =   unserialize($objSession->oauth_access_token);
   }
    # You now have a $linkedin->access_token and can make calls on behalf of the current member
    $xml_response = $linkedin->getProfile("~:(id,first-name,last-name,headline,picture-url,email-address)");
	
	$result=simplexml_load_string($xml_response);
	$data = xml2array($result, $out);
	
	if($data['status']!=404){
		$isExists = $this->modelUser->get(array("where"=>"user_email='".$data['email-address']."'")) ;
			if(!$isExists){
				if(isset($objSession->loginyes) and $objSession->loginyes!='')
				{
				   $objSession->errorMsg = "Email is invalid.";
				   unset($objSession->usertype);
				   unset($objSession->loginyes);
					echo '<script>window.opener.location="'.APPLICATION_URL.'/login";window.close();</script>';
					exit;
				}
				else
				{
					 $this->modelUser->getAdapter()->beginTransaction();
				  
					 $is_insert = $this->save_linkedin_data($data);
					 
					 if(is_object($is_insert) and $is_insert->error){
						$this->modelUser->getAdapter()->rollBack();
						$objSession->errorMsg = $is_insert->message ;
						echo '<script>window.opener.location="'.APPLICATION_URL.'/login";window.close();</script>';
						exit;
						//$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
					}
					
					$this->modelUser->getAdapter()->commit();
					$isExists = $is_insert->data ;
					
					$this->write_auth($isExists);
					
					$gotemailDataval['login_attempt'] = 0; 
					//$gotemailDataval['last_login']=date("Y-m-d H:i:s"); 
					$this->modelUser->update($gotemailDataval, 'user_id = '.$isExists['user_id']);
					
					unset($objSession->usertype);
					unset($objSession->loginyes);
					$objSession->successMsg = "Logged In Successfully . ";
					echo '<script>window.opener.location="'.APPLICATION_URL.'/profile/summary";window.close();</script>';
					exit;
				}
				
   			} 
			else
			{
				if($isExists['user_status']=='1')
				{
					$this->write_auth($isExists);
					unset($objSession->usertype);
					unset($objSession->loginyes);
					if($isExists["user_email_verified"]==0)
					{
						 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1'),$isExists['user_id']);
					}
					$objSession->successMsg = "Logged In Successfully.";
					echo '<script>window.opener.location="'.APPLICATION_URL.'/profile/summary";window.close();</script>';
					exit;
				}
				else
				{
					$objSession->errorMsg = "User blocked, please contact customer support.";
					echo '<script>window.opener.location="'.APPLICATION_URL.'/login";window.close();</script>';
					exit;
				}
			}
			
			//$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
			
	}
	else{
		$objSession->errorMsg = "There is a problem. Please Try Again";
		echo '<script>window.opener.location="'.APPLICATION_URL.'/login";window.close();</script>';
		exit;
		//$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
	}
	exit;
	}
	
	/* Insert Data into Database When User is First Time Register Via LinkedIn */
 	private function save_linkedin_data($received = false){
		global $objSession ;
 		$generated_password = genratePassword($received['name']);
		
		if(isset($objSession->usertype) and $objSession->usertype!='')
		{
			$type=$objSession->usertype;
			if($type == 'U'){		
				$tp='user';
			}
			else if($type == 'T'){		
				$tp='freelancer';
			}
			$received['user_type']=$tp;
		}
		else
		{
			$received['user_type']='user';
		}
		
		$data_to_save = array(
			'user_oauth_id' =>$received['id'],
			'user_oauth_provider'=>'linked_in',
			'user_login_type'=>'social',
			'user_type'=>'user',
			'user_reset_status'=>'1',
			'user_status'=>'1',
			'user_password'=>md5($generated_password),
			'user_email'=>$received['email-address'],
			'user_email_verified'=>'1',
			'user_first_name'=>$received['first-name'],
			'user_last_name'=>$received['last-name'],
			'user_created'=>date("Y-m-d H:i:s")
		);
  		
  		$inserted = $this->modelUser->add($data_to_save);
		 
		$inserted ->data = $data_to_save ;
		
		return $inserted;
 	}
	
	
	/* Code to Receive Profile Image  */
 	private function receive_profile_image($received , $provider){
		
 
		switch($provider){
			
			case 'facebook':
				$image_url ="https://graph.facebook.com/".$received['id']."/picture?width=400&height=400";
				$profile_image = time().'_'.$received['name'].'.png';
				
			 break;
			
			case 'twitter':
				 $image_url = str_replace("_normal","",$received['profile_image_url_https']);
				 $extension = getFileExtension($image_url);
  				 $profile_image=time().'_'.$received['screen_name'].'.'.$extension;
 			break;
			
			case 'googleplus': 
				$image_url ="https://graph.facebook.com/".$received['user_profile']['id']."/picture?width=400&height=400";
				$profile_image=time().'_'.$received['user_profile']['name'].'.png';
			break;
			
			
			default : "";
		}
 		
		
		$content = file_get_contents($image_url);
		file_put_contents(PROFILE_IMAGES_PATH.'/'.$profile_image,$content);
		
		
  		$thumb_config = array("source_path"=>PROFILE_IMAGES_PATH,"name"=> $profile_image);
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
		
		
		return $profile_image ;
	}
	
	
	/* 
		Set User Auth and make User Logged In
	*/
	private function write_auth($data){
		
		global $objSession; 
		
		$zend_auth = Zend_Auth::getInstance();
		
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$authAdapter->setTableName('users')->setIdentityColumn('user_email')->setCredentialColumn('user_password');

		$authAdapter->setIdentity($data['user_email']);

		$authAdapter->setCredential($data['user_password']);
		
 		$result = $zend_auth->authenticate($authAdapter);	

		if(!$result->isValid()){
 			$objSession->errorMsg = " Please Check Information again ";
			 $this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
  		} 
			
		$user = $authAdapter->getResultRowObject(null, 'user_password');
		$zend_auth->getStorage()->write($user);
 		return true ;
 	}
	
 
}

?>