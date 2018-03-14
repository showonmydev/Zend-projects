<?php
class Application_Form_User extends Twitter_Bootstrap_Form_Vertical
{
	
	public function init(){
 
  		$this->setMethod('post');
 		
		$this->setAttribs(array(
 			'class' => 'profile_form',
 			'novalidate'=>'novalidate',
			//"role"=>"form",
			'enctype'=>'multipart/form-data'
		));
  	}
	
		public function register(){
		$this->setAttribs(array(
 			'id' => 'register_form',
			'class' => 'profile_form',
			));
			
  		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'First Name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Last Name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
				
 		$this->loginElements();
 		
  		$this->addElement('password', 'user_rpassword', array(
 			"class"      => "form-control required input_class",
			"required"   => true,
 			"label"   => "Confirm Password",
			"ignore"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 50, 'messages'=>"Password must between 6 to 16 characters "))
							),
		));
		
		
 		
		$this->user_email->setAttrib("class","form-control required checkemail email input_class");
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);
		
		
		$validator = new Zend_Validate_Identical(array('token' =>"user_rpassword"));
		$validator->setMessage(" Password Mismatch ,please enter correct password");	
  		$this->user_password->addValidator($validator);
		
		$validator = new Zend_Validate_Identical(array('token' =>"user_password"));
		$validator->setMessage(" Password Mismatch ,please enter correct password");	
  		$this->user_rpassword->addValidator($validator);
		
		
 		$this->addElement('button', 'submitbtn', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Sign Up',
			'class'    => 'btn blue btn-primary hvr-inner-shadow btnfullwidth btn-lg site_button'
		));
		$this->submitbtn->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		$this->addElement('button', 'cancel', array(
			'ignore'   => true,
			'label'    => 'Cancel',
			'type'=>'cancel',
			'class'    => 'btn btn-lg btn-primary site_button'
		));
		$this->cancel->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		
		
 	}
	
	public function serviceprovider($id = false)
    {
		$modelStatic = new Application_Model_Static();
		$att=$modelStatic->PrepareSelectOptions_withdefault("services","service_id","service_name","service_parent_id='0'",'',''); 
		$this->addElement('select', 'type', array (
			'class' => 'form-control',
			"multioptions"=>$att,
			'label' => 'Service',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		));
	}
	
	public function registerpro($id = false)
    {
		
		
		$this->addElement('text', 'user_other_service', array (
			'class' => 'form-control required',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		
		$this->addElement('text', 'user_business_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Business name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Other service is required ")),
 								),
		));
	
		$this->addElement('text', 'user_business_website', array (
			'class' => 'form-control url',
			'label' => 'Website (with http:// or https://)', 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		$this->addElement('textarea', 'user_business_desc', array (
			'class' => 'form-control required',
			"required"=>true,
			'rows' => 2,
			'label' => 'Business Description',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		global $hearabout;
		$this->addElement('select', 'user_business_hear_about', array (
			'class' => 'form-control',
			"multioptions"=>$hearabout,
			'label' => 'How did you hear about us?',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		));
		
		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'First Name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Last Name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
		$this->addElement('password', 'user_password', array (
			'class' => 'form-control required',
			'label' => 'Password',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
		));
		
		$this->addElement('password', 'user_cpassword', array (
			'class' => 'form-control required',
			'label' => 'Confirm Password',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
		));
		
		$this->addElement('text', 'user_email', array (
			'class' => 'form-control email required',
			"required"=>true,
			'label' => 'Email Address',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
		$this->addElement('text', 'user_phone', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Phone Number',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
// state		
		$modelStatic = new Application_Model_Static();
		$state=$modelStatic->getOptionsForState();
		
		$this->addElement('select', 'user_state', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'State',
			"multioptions"=> $state,    
			"onchange" =>"getcity(this.value)", 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"State is required ")),
 								),
		));
		
//......
		$modelStatic = new Application_Model_Static();
		$n=$modelStatic->selectOptionsForZip();
		
		$this->addElement('select', 'user_city', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'City',
			"multioptions"=> $n,    
			'onchange' =>'zipcodeData(this.value)', 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
// zip code textbox		
		$this->addElement('text', 'user_zip_code', array (
				'class' => 'form-control required digits',
				"label" => "Zip Code" ,
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"zip code is required ")),
								),
			));
		
			$this->addElement('select', 'user_travel_range', array (
			'class' => 'form-control required',
			"required"=>true,
			"Onchange" => 'travelrange(this.value)',
			'label' => 'How far are you willing to travel?',  
			"multioptions"=>array(
					'5' => 	'5 miles',
					'10' => '10 miles',
					'15' => '15 miles',
					'20' => '20 miles',
					'30' => '30 miles',
					'40' => '40 miles',
					'50' => '50 miles',
				), 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
	
		
		$this->addElement('checkbox', 'user_protect_account', array (
			'class' => '',
			"value"=>1,
			'label' => "Create a password to protect my CasaAdvisor's account (optional)",
			
		));	
		
			$this->addElement('radio', 'user_travel_preference', array (
			'class' => 'required',
			"required"=>true,
			
			"multioptions"=>array(
						'1' => 'I travel to my customers',
						'2' => 'My customers travel to me',
					),
			"value"=>1,		
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
		$this->addElement('button', 'submitbtn', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Sign Up',
			'class'    => 'btn blue btn-primary bttnsubmit hvr-inner-shadow btnfullwidth btn-default btn btn-default site_button'
		));
		
		$this->submitbtn->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
    }
// other info

public function otherinfo($id = false)
    {
		
// b_name		
		$this->addElement('text', 'user_business_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Business name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Other service is required ")),
 								),
		));
// web_site	
		$this->addElement('text', 'user_business_website', array (
			'class' => 'form-control url',
				'label' => 'Website (with http:// or https://)',  
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
//desc		
		$this->addElement('textarea', 'user_business_desc', array (
			'class' => 'form-control required',
			"required"=>true,
			'rows' => 3,
			'label' => 'Business Description',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
// hear 		
		global $hearabout;
		$this->addElement('select', 'user_business_hear_about', array (
			'class' => 'form-control',
			"multioptions"=>$hearabout,
			'label' => 'How did you hear about us?',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		));
// how far		
			$this->addElement('select', 'user_travel_range', array (
			'class' => 'form-control required',
			"required"=>true,
			"Onchange" => 'travelrange(this.value)',
			'label' => 'How far are you willing to travel?',  
			"multioptions"=>array(
					'5' => 	'5 miles',
					'10' => '10 miles',
					'15' => '15 miles',
					'20' => '20 miles',
					'30' => '30 miles',
					'40' => '40 miles',
					'50' => '50 miles',
				), 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
	
// preference
			$this->addElement('radio', 'user_travel_preference', array (
			'class' => 'required',
			"required"=>true,
			'label'    => 'Travel Preference',
			
			"multioptions"=>array(
						'1' => 'I travel to my customers',
						'2' => 'My customers travel to me',
					),
			"value"=>1,		
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
// submit button		
		$this->addElement('button', 'submitbtn', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Save',
			'class'    => 'btn blue btn-primary bttnsubmit hvr-inner-shadow btnfullwidth btn-default btn btn-default site_button'
		));
		$this->submitbtn->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		/*$this->submitbtn->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');*/
    }


// end other info	
	
	

	public function  loginElements(){
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email input_class",
			'autocomplete'=>'on',
			"required"   => true,
			"label"   => "Email Address",
//			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
 		$this->addElement('password', 'user_password', array(
 			"class"      => "form-control required input_class",
			"required"   => true,
 			"label"   => "Password",
			//"label"   => "Password",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters "))
							),
		));
		
		
		
 		
		
	}
	
	
	public function  loginElements_new(){
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email input_class",
			'autocomplete'=>'on',
			"required"   => true,
			"label"   => "Email Address",
//			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
 		$this->addElement('password', 'user_password1', array(
 			"class"      => "form-control required input_class",
			"required"   => true,
 			"label"   => "Password",
			//"label"   => "Password",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
							),
		));
		
		
		
 		
		
	}
	
	
	public function twitter_email(){
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email ",
			'autocomplete'=>'on',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
		
		
		$this->user_email->setAttrib("class","form-control required checkemail email  ");
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);
		
		
		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Continue',
			'class'    => 'btn btn-lg btn-primary btn-block '
		));
		
		$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
	}
	
	
	
	
	
	/* Login Form */
	public function login(){
		
		$this->loginElements();
		
  
 		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Login',
			'class'    => 'btn btn-custom-dark row-fluid button-margin-form '
		));
  	}
	
	
	public function login_front($isAdmin = false){
		
		
	
 		
		
		if($isAdmin){
			$this->loginElements();
			$this->user_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			$this->user_password->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		}
		else
		{
			$this->loginElements_new();
		}
  
 		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'label'    => 'Sign In',
			'type'=>'submit',
			'class'    => 'btn btn-lg btn-primary site_button'
		));
		
		$this->addElement('button', 'cancel', array(
			'ignore'   => true,
			'label'    => 'Cancel',
			'type'=>'cancel',
			'class'    => 'btn btn-lg btn-primary site_button'
		));
		
		
	//	gcm($this->submit);
		
		//prd($this->submit->getAttrib('buttons'));
		$this->cancel->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');

	}
	
	
	
	public function forgotPassword(){
			
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email emailexists input_class",
			'autocomplete'=>'on',
			"required"   => true,
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address ")),
								array("Db_RecordExists" , true,array('table' => 'users','field' => 'user_email' ,"messages"=>"`%value%` is not registered , please enter valid email address "))
							),
		));
		
		
		//$this->user_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
 		
  		$this->addElement('button', 'btnsubmit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Reset password',
			'class'    => 'btn btn-lg btn-primary site_button'
		));
		
		$this->btnsubmit->setAttrib("type",'submit');
		$this->btnsubmit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		  
	}
	
 	
	public function contact_us(){
		
		
		$this->setMethod('post');
		
	/*	$this->setAction(self::METHOD_POST);*/
		
	/*	$this->setAttribs(array(
			'id' => 'validate',
			'class' => 'form-vertical',
 			'novalidate'=>'novalidate',
			'enctype'=>'multipart/form-data'
 		));*/
		
  	
		/*  Name  */	
		$this->addElement('text', 'guest_name', array(
				"class"      => "form-control top-element required",
				"required"   => true,
 				"placeholder"   => "Enter Your Full Name",
				"label"   => "Full Name",
				"filters"    => array("StringTrim",'StripTags'),
				'validators' => array( array('NotEmpty', true, array ("messages" => " Full Name cannot be empty ")))
  		));
	//	$this->guest_name->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');


  		/* Email */
 		$this->addElement('text', 'guest_email', array(
						'class' => 'form-control middle-element required email',
						'required'   => true,
						'placeholder'   => 'Enter Your Email Address',
						'label'   => 'Email Address',
						'filters'    => array('StringTrim','StripTags'),
						'validators' => array( array('NotEmpty', true, array ("messages" => "Email Address cannot be empty")), 'EmailAddress')
        ));
		
	//	$this->guest_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		$this->addElement('text', 'guest_phone', array(
						'class' => 'form-control  middle-element required phone_validate',
						'required'   => true,
						'placeholder'   => 'Enter Your Phone Number',
						'label'   => 'Phone Number',
						'filters'    => array('StringTrim','StripTags'),
						'validators' => array( array('NotEmpty', true, array ("messages" => "Phone Number cannot be empty")))
        ));
		
	//	$this->guest_phone->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
  
		
		/*  User Address   */	
		$this->addElement('textarea', 'guest_message', array(
				"class"      => "form-control bottom-element required",
 				"rows"=>5, 
				'required'   => true,
  				"placeholder"   => " Enter Message Here",
				"label"   => "Message ",
				"filters"    => array("StringTrim",'StripTags') ,
				'validators' => array( array('NotEmpty', true, array ("messages" => "Message field cannot be empty.")))
  		));

		//$this->guest_message->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Send',
			'class'    => 'btn btn-lg btn-primary btn-block site_button'
		));
		
		$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		
		
  	}
	
	
	
 
	public function profile(){
		
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'm-wrap span6 required',
			"placeholder" => "Admin First Name",
			"label" => "Admin First Name",
			'validators' => array( array('NotEmpty', true, array ("messages" => "Please enter first name")))
		));
		$this->user_first_name->addValidator("NotEmpty", true, array ("messages" => "Please enter first name"));
	
	
		$this->addElement('text', 'user_last_name', array (
			'class' => 'm-wrap span6 required',
			"placeholder" => "Admin Last Name",
			"label" => "Admin Last Name",
			 'validators' => array( array('NotEmpty', true, array ("messages" => "Please enter last name")))
		));
		$this->user_last_name->addValidator("NotEmpty", true, array ("messages" => "Please enter last name"));
	
 
 		##--------------- Admin Email Address -------##
		$this->addElement('text', 'user_email', array(
			'label'      => 'Email',
			'class' => 'm-wrap span6 required email',
			'required'   => true,
			'placeholder'   => 'Email Address',
			"placeholder" => "Email Address",
			'filters'    => array('StringTrim','StripTags'),
			'validators' => array('NotEmpty')
		));
		
		$this->user_email->addValidator('NotEmpty',true,array('messages' =>'Email is required.'))
		->addValidator('EmailAddress',true)->addErrorMessage('Please enter a valid Email-Id');
		
		
		##--------------- Admin  PaypaL Email Address -------##
		$this->addElement('text', 'user_paypal_email', array(
							'label'      => 'Paypal Email',
							'class' => 'm-wrap span6 required email',
							'required'   => true,
							'placeholder'   => 'Paypal Email Address',
							'filters'    => array('StringTrim','StripTags'),
							'validators' => array('NotEmpty')
		));
		$this->user_paypal_email->addValidator('NotEmpty',true,array('messages' =>'Payal Email is required.'))
		->addValidator('EmailAddress',true)->addErrorMessage('Please enter a valid Email-Id');
		
		
		$this->addElement('file', 'user_image', array (
							"placeholder" => " Please Select  Image ",
							"label" => " Please Select  Image ",
							"id" => "user_image",
							"class" => "default",
							));
							
							
  		$this->user_image->setDestination(ROOT_PATH.PROFILE_IMAGES);
		
		
		$this->submitBtn();
		
 		 
	 }
	 
public function profile_front($user_id=false){
		
		$this->setAttrib('id','user_profile');
	 $model = new Application_Model_SuperModel();
	 $user_data=array();
	 $user_data=$model->Super_Get("users","user_id='".$user_id."'","fetch");
 		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Salutation 
		 */
  	
  		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'First Name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Last Name',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
		$this->addElement('text', 'user_email', array (
			'class' => 'form-control email checkemail_exclude required input_class',
			"required"=>true,
			'label' => 'Email Address',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
 	
	
		$this->addElement('text', 'user_address', array(
 			'class' => 'form-control required ',
			'required'   => true,
			"label"=>"Address",
			"onchange"=>"deliveryAddress()",
			
			'placeholder'   => 'Address',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Address is Required ")),
 							),
		));
		
		//;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
// state		
		$modelStatic = new Application_Model_Static();
		$state=$modelStatic->getOptionsForState();
		
		$this->addElement('select', 'user_state', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'State',
			"multioptions"=> $state,    
			"onchange" =>"getcity(this.value)", 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"State is required ")),
 								),
		));
// City ......
		$modelStatic = new Application_Model_Static();
		$n=$modelStatic->selectOptionsForZip();
		//prd($n);
		$this->addElement('select', 'user_city', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'City',
			"multioptions"=> $n,    
			//'onchange' =>'zipcodeData(this.value)', 
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
// zip code textbox		
		$this->addElement('text', 'user_zip_code', array (
				'class' => 'form-control required',
				"label" => "Zip Code" ,
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"CVV is required ")),
								),
			));
// phone no........................
		$this->addElement('text', 'user_phone', array (
			'class' => 'form-control required',
			"required"=>true,
			'label' => 'Phone Number',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
  		 
		
		
		

		//;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Postal Code
		 */ //........................................................................................
		/* $this->addElement('text', 'user_postal_code', array(
 			'class' => 'form-control required ',
			'required'   => true,
			"label"=>"Postal Code",
			'placeholder'   => 'Postal Code',
  			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Postal Code  is Required ")),
 							),
		));*/
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	User State
		 */
		/* $country_arr=$model->PrepareSelectOptions_withdefault("countries","country_name","country_name","1","country_name","Select Country");
 		 $this->addElement('select', 'user_country', array(
 			'class' => 'form-control required',
			"multioptions"=>$country_arr,
			'required'   => true,
			"label"=>"Country",
 			"placeholder" => "Country",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Country is Required ")),
 							),
  		));
 		*/
	
   		$this->_submitButton(false,"profile_submit");
		
 		 
	 }
 	 
	public function profile_admin($user_id=false){
		
		$this->setAttrib('id','user_profile');
	 $model = new Application_Model_SuperModel();
	 $user_data=array();
	 $user_data=$model->Super_Get("users","user_id='".$user_id."'","fetch");
 		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Salutation 
		 */
  		$salutations=  array("0"=>"Mr","1"=>"Mrs","2"=>"Ms");		
 		$this->addElement('select', 'user_salutation', array(
							'class'      => 'form-control',
							//'required'   => true,	
 							'label'=>'Salutation',
							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $salutations
		));
  		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "First Name",
			"required"=>true,
			"label" => "First Name",
			
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" First Name is Required ")),
 							),
  		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "Last Name",
			"required"=>true,
			"label" => "Last Name",
			
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Last Name is Required ")),
 							),
		));
 	
 
 		/*	Form Element  - MaketPlace
		 *	Element Name -  Email Address
		 */
		/*$this->addElement('text', 'user_email', array(
 			'class' => 'form-control required checkemail_exclude email',
			'required'   => true,
 			'label'      => 'Email',
			"placeholder" => "Email Address",
  			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		));
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',
			 'exclude' => array(
				'field' => 'user_id',
				'value' => $user_id
        )

		));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);*/
			
 		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Address
		 */
		$this->addElement('text', 'user_address', array(
 			'class' => 'form-control required ',
			'required'   => true,
			"onchange"=>"deliveryAddress()",
			'label'      => 'Address',
			'placeholder'   => 'Address',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Address is Required ")),
 							),
		));
		
  		 
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	User State
		 */
		 
 		 $this->addElement('text', 'user_state', array(
 			'class' => 'form-control required',
			'required'   => true,
			'label'      => 'State',
			"onchange"=>"deliveryAddress()",
 			"placeholder" => "State",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" State is Required ")),
 							),
  		));
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Postal Code
		 */
		 $this->addElement('text', 'user_zip_code', array(
 			'class' => 'form-control required ',
			'required'   => true,
			'label'      => 'Postal Code',
			'placeholder'   => 'Postal Code',
  			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Postal Code  is Required ")),
 							),
		));
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	User State
		 */
		 $country_arr=$model->PrepareSelectOptions_withdefault("countries","country_name","country_name","1","country_name","Select Country");
 		 $this->addElement('select', 'user_country', array(
 			'class' => 'form-control required',
			"multioptions"=>$country_arr,
			'required'   => true,
			'label'      => 'Country',
 			"placeholder" => "Country",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Country is Required ")),
 							),
  		));
 		
	
   		$this->_submitButton(false,"profile_submit");
		
 		 
	 }
 	 
	 
	public function image(){
		
 		/*	Form Element  - MaketPlace
		 *	Element Name - 	Profile Image
		 */	
		$this->addElement('file', 'user_image', array (
							"placeholder" => " Profile Image ",
							"label" => " Profile Image ",
							"id" => "user_image",
							"class" => "default required",
							"accept"=>"image/*"
							));
							
		$this->user_image->setDestination(PROFILE_IMAGES_PATH)
			->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
			->addValidator('Size', false, IMAGE_VALID_SIZE);
   		$this->user_image->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		
		
	}
	 
 	 
 	
	/* Change Password Form  */
	public function changePassword($isAdmin = false){
		
		/* 
			Admin Old Passwork Form
		*/	
		
		$functionName="match_old_password_front";
		
		if($isAdmin){
			$functionName="match_old_password";
		}
	
		
 		$this->addElement('password', 'user_old_password', array(
 			"class"      => "form-control  required ",
			"required"   => true,
			"label"=>"Enter Old Password",
 			"placeholder"   => "Old Password",
			"ignore"=>true,
 			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true, array("messages"=>" Old Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),
								array("Callback" , true, array($functionName,'messages'=>"Old Password Mismatch,")),
							),
			));
			
			$this->resetPassword($isAdmin);
				if(!$isAdmin){
					$this->submit->setAttrib("class","btn site_button pull-right");
				}
         
	}

	
	
	/* Reset Password Form  */
 	public function resetPassword($isAdmin = false ){
		
  		$this->addElement('password', 'user_password', array(
 			"class"      => "form-control  required input_class",
			"required"   => true,
			"label"=>"New Password",
 			"placeholder"   => "New Password",
			"autocomplete" =>"off",
 			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),
								array("Identical" , true,array('token' => "user_rpassword", 'messages'=>"Password mismatch, please ender correct same password "))
							),
		));
		
 		$this->addElement('password', 'user_rpassword', array(
 			"class"      => "form-control  required input_class",
			"required"   => true,
			"label"=>"Confirm  New Password",
 			"placeholder"   => "Confirm Password",
			"ignore"=>true,
			"autocomplete" =>"off",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),
								array("Identical" , true,array('token' => "user_password", 'messages'=>"Password mismatch, please ender correct same password "))
							),
		));
		
   		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Change Password',
			'class'    => 'btn btn-lg btn-primary btn-block site_button'
		));
		
		
		if(!$isAdmin){
 			//$this->user_password->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			//$this->user_rpassword->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			
		}
		
		
 		
	}
	
	

	public function resetPassword1($isAdmin = false ){
		
  		$this->addElement('password', 'user_password', array(
 			"class"      => "form-control  required input_class",
			"required"   => true,
 			"label"   => "Enter Password",
			"autocomplete" =>"off",
 			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),
								array("Identical" , true,array('token' => "user_rpassword", 'messages'=>"Password mismatch, please ender correct same password "))
							),
		));
		
 		$this->addElement('password', 'user_rpassword', array(
 			"class"      => "form-control  required input_class",
			"required"   => true,
 			"label"   => "Re Type  Password",
			"ignore"=>true,
			"autocomplete" =>"off",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),
								array("Identical" , true,array('token' => "user_password", 'messages'=>"Password mismatch, please ender correct same password "))
							),
		));
		
   		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'RESET PASSWORD',
			'class'    => 'btn btn-lg btn-primary btn-block site_button'
		));
		
		
		if(!$isAdmin){
 			//$this->user_password->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			//$this->user_rpassword->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			
		}
		
		
 		
	}



	
 	 public function submitBtn($class=false){
		 
		 $this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save',
				'escape'=>false
		));
		
		
		
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => $class))	));
		
		
	 }
	 
 
	 
 
	 private function _submitButton(){
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary site_button  ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>' Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	}
	 
	 
	 
	
	
}