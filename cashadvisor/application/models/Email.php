<?php
class Application_Model_Email extends Zend_Db_Table_Abstract
{
	protected $_name = 'email_templates';
	public $primary ="" , $modelStatic; 
	 
	
	
	public function init(){
		
   		$table_info = $this->info('primary');
		$this->primary = $table_info ['1'];
		$this->modelStatic = new Application_Model_Static();
 	}
	
	
	
 	
	/* 	Add / Update User Information 
	 *	@
	 *  Author  - zend
	 */
	 public function sendEmail($type = false ,$data = false){
		 
		  $config = array(   'auth' => 'login',
							'username' => 'developers@webdemo1.co.in',
							'password' => 'q1w2e3r4t5y6u7'
						);
		 
		 $transport = new Zend_Mail_Transport_Smtp('webdemo1.co.in', $config);
		 $userLogged = isLogged(true);
  		 $mail = new Zend_Mail();
		 $site_config = Zend_Registry::get("site_config");
		 
 		 $SenderName = ""; $SenderEmail = "";$ReceiverName = ""; $ReceiverEmail = "";
		 
		 $admin_info = $this->modelStatic->getAdapter()->select()->from("users")->where("user_id =1")->query()->fetch();
		 
  		 if(!$type){
			 return  (object) array("error"=>true , "success"=>false , "message"=>" Please Define Type of Email");
		}
		
		
 		 
 		switch($type){
			
			case  'reset_password' :  /* begin  : Reset Password Email */
				
				$template = $this->modelStatic->getTemplate('reset_password');
 				
				$ReceiverEmail = $data['user_email'];
				//prd($data);
				$ReceiverName =  $data['user_first_name'].' '.$data['user_last_name'];
				
				
				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
				if($data['user_type']=="1" or $data['user_type']=="2"){
 					$resetlink = SITE_HTTP_URL."/admin/resetpassword?key=".$data['pass_resetkey'];
 					//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
				
				}else{
					$resetlink = SITE_HTTP_URL."/user/resetpassword/key/".$data['pass_resetkey'];
	 				$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
				}
   				
				$MESSAGE = str_ireplace(array("{user_name}","{site_name}","{curr_year}","{verification_link}","{website_link}" ), array($ReceiverName ,$site_config['site_name'],date("Y"), $resetlink,APPLICATION_URL),$template['emailtemp_content']);
 				
				
   			break; /* end : Reset Password Email */
			
			
			
			case 'registration_email':/* begin : Registration Email */
				
  				 
				$template = $this->modelStatic->getTemplate('registration_email');
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_first_name'].' '.$data['user_last_name'];
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
 
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
				$MESSAGE = str_ireplace(array("{user_name}","{site_name}","{curr_year}","{verification_link}","{password}","{website_link}" ), array($ReceiverName,$site_config['site_name'],date("Y"),$resetlink,$data['password'],APPLICATION_URL),$template['emailtemp_content']);
				//prd($MESSAGE);
			break ;/* end : Registration Email */
			
			
			case 'social_registration_email':/* begin : Registration Email */
				
  				 
				$template = $this->modelStatic->getTemplate('social_registration_email');
 				
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_first_name'].' '.$data['user_last_name'];
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
 
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{user_name}","{site_name}","{curr_year}","{verification_link}","{website_link}" ), array($ReceiverName,$site_config['site_name'],date("Y"),$resetlink,APPLICATION_URL),$template['emailtemp_content']);
 				//prd($MESSAGE);
			break ;/* end : Registration Email */
			
		
			
 			/* Email For Verification of new Email Address */
			case 'email_verification': /* begin :  email_verification */
 				 
				$template = $this->modelStatic->getTemplate('email_verification');
 				
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_first_name'].' '.$data['user_last_name'];
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
   				$resetlink = SITE_HTTP_URL."/user/verifyemail/key/".$data['user_email_key'];
 				 
 				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
  				
				$MESSAGE = str_ireplace(array("{user_name}","{site_name}","{curr_year}","{verification_link}","{website_link}" ), array($ReceiverName,$site_config['site_name'],date("Y"),$resetlink,APPLICATION_URL),$template['emailtemp_content']);
   				//prd($MESSAGE);
  			break ;/* end : email_verification*/
			
			
			case 'coming_soon_user': /* begin :  coming_soon_user */
 				
				$template = $this->modelStatic->getTemplate('coming_soon_user');
 				
				echo $ReceiverEmail = $data['newsletter_email'];
				$ReceiverName = "Subscriber";
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
				$MESSAGE = str_ireplace(
											array("{site_name}","{SITE_TITLE}" ,"{website_link}" ,"{curr_year}"), 
											array($site_config['site_name'],$site_config['site_title'],APPLICATION_URL,date('Y')),$template['emailtemp_content']
									);
							
  			break ;/* end : coming_soon_user*/
			
			case 'coming_soon_admin': /* begin :  coming_soon_admin */
 				
				$template = $this->modelStatic->getTemplate('coming_soon_user');
 				
				echo $ReceiverEmail = $data['newsletter_email'];
				$ReceiverName = "Subscriber";
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
				$MESSAGE = str_ireplace(
											array("{site_name}","{SITE_TITLE}" ,"{website_link}" ,"{curr_year}"), 
											array($site_config['site_name'],$site_config['site_title'],APPLICATION_URL,date('Y')),$template['emailtemp_content']
									);
							
  			break ;/* end : coming_soon_admin*/
			
			
			case 'proposal_received': /* begin :  Quote Received by client from SP */
 				
				$template = $this->modelStatic->getTemplate('proposal_received');
 		//prd($data);
				
				$jobID = $data['job_id'];
			 	$ReceiverEmail = $data['client_email_id'];
				$ReceiverName = $data['Client_name'];
				$Job = $data['service_name'];
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_name']; 
				$ServiceProvider =  $data['ServiceProvider_name']; 
			//	prd($site_config);
			
				$MESSAGE = str_ireplace(
											array("{client_name}","{ServiceProvider_name}","{Job_title}","{site_name}","{SITE_TITLE}","{curr_year}","{website_link}"),
											array($ReceiverName,$ServiceProvider,$Job,$site_config['site_name'],$site_config['site_title'],date("Y"),APPLICATION_URL."QuoteRequest/".$jobID),$template['emailtemp_content']
									);					
			//prd($MESSAGE);			
  			break ;/* end : Quote Received by client from SP*/
			
			
			
				case 'new_job_posted': /* begin :  Client has posted a job */
 				
				$template = $this->modelStatic->getTemplate('new_job_posted');
			//	prd($template);
			 	$ReceiverEmail = $data['client_email_id'];
				$ReceiverName = $data['Client_name'];
				$Job = $data['service_name'];
 		//prd($ReceiverName);
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_name']; 
			//	prn($site_config);
			
				$MESSAGE = str_ireplace(
											array("{client_name}","{Job_title}","{site_name}","{SITE_TITLE}","{curr_year}","{website_link}"),
											array($ReceiverName,$Job,$site_config['site_name'],$site_config['site_title'],date("Y"),APPLICATION_URL."project"),$template['emailtemp_content']
									);					
		//prd($MESSAGE);			
  			break ;/* end : Client has posted a job*/
			
			
			case 'job_request_send_email':/* SP received new job request */
				
				$template = $this->modelStatic->getTemplate('job_request_send_email');
			//	prd($data);
			 	$ReceiverEmail = $data['ServiceProvider_email_id'];
				$ReceiverName = $data['ServiceProvider_name'];
				$Job = $data['service_name'];
				$ClientName = $data['Client_name'];
				$jobID = $data['job_id'];
 		//prd($ReceiverName);
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_name']; 
			//	prn($site_config);
			
				$MESSAGE = str_ireplace(
											array("{client_name}","{ServiceProvider_name}","{service_name}","{site_name}","{SITE_TITLE}","{curr_year}","{website_link}"),
											array($ClientName,$ReceiverName,$Job,$site_config['site_name'],$site_config['site_title'],date("Y"),APPLICATION_URL."sendQuote/".$jobID),$template['emailtemp_content']
				
					);
					
				//prd($MESSAGE);
			   break ; /* end : SP received new job request */
			   
			   
			//  case 'more_quote_accepted':/* Client request for more quote is accepted */
//				
//				$template = $this->modelStatic->getTemplate('job_request_send_email');
//			//	prd($data);
//			 	$ReceiverEmail = $data['ServiceProvider_email_id'];
//				$ReceiverName = $data['ServiceProvider_name'];
//				$Job = $data['service_name'];
//				$ClientName = $data['Client_name'];
// 		//prd($ReceiverName);
// 				$SenderEmail = $site_config['register_mail_id']; 
//				$SenderName = $site_config['site_name']; 
//			//	prn($site_config);
//			
//				$MESSAGE = str_ireplace(
//											array("{client_name}","{ServiceProvider_name}","{service_name}","{site_name}","{SITE_TITLE}","{curr_year}","{website_link}"),
//											array($ClientName,$ReceiverName,$Job,$site_config['site_name'],$site_config['site_title'],date("Y"),APPLICATION_URL."project"),$template['emailtemp_content']
//				
//					);
//					
//				//prd($MESSAGE);
//			   break ; /* end : Client request for more quote is accepted */

			
			
			case 'contact_us':{
				
				
 	 			$template = $this->modelStatic->getTemplate("contact_us_user");
				
	 			
 				$sender_email = $data['guest_email'];
				$sender_name = $data['guest_name'];
				$sender_phone = $data['guest_phone'];
				$message = $data['guest_message'];
				$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
		 
				$mail_content = str_ireplace(
										array("{site_name}","{curr_year}","{SITE_TITLE}" ,"{site_admin}","{guest_name}","{sender_email}","{sender_phone}","{sender_message}","{website_link}" ), 
										array($site_config['site_name'],date("Y"),$site_config['site_title'] ,$site_config['site_title'] ,$sender_name,$sender_email,$sender_phone,$message,APPLICATION_URL),
										$template['emailtemp_content']
									);
				//	prd($mail_content);				
				 
				
				$mail = new Zend_Mail();
				$mail->setBodyHtml($mail_content)
				->setFrom($site_config["register_mail_id"], $site_config['site_title'])
				->addTo($sender_email , $sender_name)
				->setSubject($subject);
		
				
					$mail->send() ;
				
				
				
				
				/* Mail To Admin  */		
				$template =$this->modelStatic->getTemplate("contact_us_admin");
						
				$mail_content = str_ireplace(
											array("{site_name}","{curr_year}", "{SITE_TITLE}" ,"{site_admin}","{guest_name}","{guest_email}","{guest_phone}","{guest_message}","{website_link}" ), 
											array($site_config['site_name'],date("Y"),	$site_config['site_title'] ,$site_config['site_title'] ,$sender_name,$sender_email,$sender_phone,$message,APPLICATION_URL),
										$template['emailtemp_content']
									);
				
			
			 
 				$mail = new Zend_Mail();
				$mail->setBodyHtml($mail_content)
					->setFrom($site_config["register_mail_id"], $site_config['site_title'])
					->addTo($site_config['register_mail_id'],$admin_info['user_first_name']." ".$admin_info['user_last_name'])
					->setSubject($subject);
				 
				
				if( $mail->send()){ 
				
				return true;} else {
						
					return false;}
				 
 				
			}
			break;

 			default:return  (object)array("error"=>true , "success"=>false , "message"=>" Please Define Proper Type for  Email");
		}
		 
		 
		 $mail->setBodyHtml($MESSAGE)
			 ->setFrom($SenderEmail, $SenderName)
			 ->addTo($ReceiverEmail,$ReceiverName)
			 ->setSubject($template['emailtemp_subject']);
   		
		
		
	  $mail->send();	
		
		//prd($mail);
		return (object)array("error"=>false , "success"=>true , "message"=>" Mail Successfully Sent");
		
		
		return (object)array("error"=>false , "success"=>true , "message"=>" Unable To Send Email ");	
  		 
	 }
	 
	 
	 
	 private function _registration(){
		 
	 }
 	
	   
	
}