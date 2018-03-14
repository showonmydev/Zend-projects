<?php
class Application_Form_StaticForm extends Twitter_Bootstrap_Form_Vertical
{
 
 	public function init(){
		
		$this->setMethod('post');
		
		$this->setAction(self::METHOD_POST);
		
		$this->setAttribs(array(
			'id' => 'validate',
			"role"=>"form",
			'class' => 'default_form  validate',
			"novalidate"=>"novalidate"
		));
  		
	}
	
	public function aboutteam(){
 		$this->addElement('text', 'ateam_member_name', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"maxlength"=> "50",
			"label" => "Member Name " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Member Name is required ")),
 							),
 		));
		
		$this->addElement('textarea', 'ateam_member_about', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "About Member " ,
			"rows"=>4, 
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" About Member is required ")),
 							),
 		));
		
		  $this->addElement('file','ateam_member_image',array(
			   'id'=>'ateam_member_image',
			   "label"=>"Team Member Image",
			   "placeholder" => "Team Member Image",
			   "accept"=>"image/*",
			   ));
			   
		 $this->ateam_member_image->setDestination(TEAM_MEMBER_IMAGES_PATH)
		 ->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
		 ->addValidator('Size', false, IMAGE_VALID_SIZE);
 		$this->submitButton();
  	}
	
// add packages form
/*	public function packages(){
 		$this->addElement('text', 'cp_price', array (
			'class' => 'form-control required',
			"label" => "Package Price(only digits greater than 0) " ,
			"onkeypress"=>"return checkprice(event)",
			"onchange"=>"return checkprices(this.value)",
			
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Price is required ")),
 							),
 		));
		$this->addElement('text', 'cp_points', array (
			"required" => TRUE,
			'class' => 'form-control required digits',
			"label" => "Package Points " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Price is required ")),
 							),
 		));

 		$this->submitButton();
  	}*/
// end add packages form.........................

	
// add services form
	/*public function services(){
 		$this->addElement('text', 'service_name', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Service Name " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
 		$this->submitButton();
  	}*/
// end add services form.........................



// add service category form
	/*public function servicecat(){
		$modelservices = new Application_Model_Services();
		
		$servicelist = $modelservices->servicesdata();
		//prd($servicelist);
					
 		$this->addElement('text', 'service_name', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Service Category Title " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		
		$this->addElement('select', 'service_parent_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$servicelist,
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
 		$this->submitButton();
  	}*/
// end add service category form.................................................................

// add sub category form
	/*public function subcategory(){
		$modelservices = new Application_Model_Services();
		$servicelist = $modelservices->servicesdata();
		$caterories = $modelservices->categorydata();
	//prd($caterories);

 		$this->addElement('text', 'service_name', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Service Sub Category Title " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		$this->addElement('select', 'service_parent_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$servicelist,
				"onchange"=>"getcategorylist(this.value)",
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
		$this->addElement('select', 'service_sub_parent_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$caterories,
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
 		$this->submitButton();
  	}*/
// end add sub category form.............................................................................



public function faq(){
		
		 ## --- FAQ Title ---##	
 		$this->addElement('text', 'faq_question', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Question" ,
			"maxlength"=> "100",
			"label" => "Question" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"This field is required.")),
 							),
 		));
	 

        ## ---- FAQ Content ---##
 		$this->addElement('textarea', 'faq_answer', array (
			"required" => TRUE,
			'class' => 'form-control required',
			'id' => 'faq_answer',
			'rows'=>'6',
			"label" => "Answer",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>"This field is required.")),
 							),
		));
		
		$this->addElement('radio', 'faq_for', array (
			'class' => 'required',
			"required"=>true,
			'multiOptions'=>array(
			'0' => 'Client',
			'1' => 'Service Provider',
			),
			"label" => "Field is required or optional",
			"title"=>"Please select FAQ for service provider or client",
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select field type either required or optional")),
							),
		));
 
 		$this->submitButton();
 
  	
			
	} 
 	/* 
	 *	Static Page Form
	 */
	public function page(){
		
 		
		 ## --- Page Title ---##	
 		$this->addElement('text', 'page_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Page Title" ,
			"label" => "Page Title <span class='required'>*</span>" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Page Title is required ")),
 							),
 		));
	 

        ## ---- Page Content ---##
 		$this->addElement('textarea', 'page_content', array (
			"required" => TRUE,
			'class' => 'form-control ckeditor',
			'id' => 'cleditor',
			'rows'=>'6',
			"label" => "Page Content <span class='required'>*</span>",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Page Content is required")),
 							),
		));
 
 		$this->submitButton();
 
  	}
	
	public function subscription(){
		
 		
		 ## --- Page Title ---##	
 		$this->addElement('text', 'subscription_plan_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Plan Title" ,
			"label" => "Plan Title <span class='required'>*</span>" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Plan Title is required ")),
 							),
 		));
	 

        ## ---- Page Content ---##
 		$this->addElement('textarea', 'subscription_plan_description', array (
			"required" => TRUE,
			'class' => 'form-control',
			'id' => 'cleditor',
			'rows'=>'6',
			"label" => "Plan Description <span class='required'>*</span>",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Plan Description is required")),
 							),
		));
		
		 ## --- Page Title ---##	
 		$this->addElement('text', 'subscription_plan_price', array (
			"required" => TRUE,
			'class' => 'form-control required number',
			"placeholder" => "Plan Price" ,
			"label" => "Plan Price <span class='required'>*</span> (Per month)" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Plan Price is required ")),
								array("Digits",true,array("messages"=>"Please enter numbers in Plan price")),
								
								 							),
 		));
 
 		$this->submitButton();
 
  	}

	
 	public function content_block(){
 		
 	 
   		$this->addElement('text', 'content_block_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Content Block Title" ,
			"label" => "Content Block Title" ,
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Content Block Title is required")),
 							),
 		));
		
		
 	 	

        ## ---- Page Content ---##
 		$this->addElement('textarea', 'content_block_content', array (
			"required" => TRUE,
			'class' => 'form-control ckeditor',
			'id' => 'cleditor',
			'rows'=>'9',
			"label" => "Content",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Content is required")),
 							),
		));
		
  		
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
  	} 		 
	
 
 	public function navigation(){
		
		$modelUser = new Application_Model_User();
		$modelNavigation = new Application_Model_Navigation();
		$modelContent = new Application_Model_Content();
		
 		
 		$this->addElement('text', 'menu_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Menu Title" ,
			"label" => "Menu Title" 
 		));
		
		
		/* Page Drop Down For the Menu */
		$parentPages = $modelNavigation->getParentMenuList();
  		$this->addElement('select', 'menu_parent_id', array(
							'class'      => 'form-control ',
  							'label'=>'Select Menu Parent',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $parentPages
		));
 		
		
		
		$getPageList = $modelContent->getPageList();
   		$this->addElement('select', 'menu_page_id', array(
					  'class'      => ' form-control required',
 					  'label'=>' Select Page For Menu  ',
					  'validators' => array('NotEmpty'),
					  "filters"    => array("StringTrim","StripTags","HtmlEntities"),
					  'multiOptions' => $getPageList
		));
		
		$post=  array("Header"=>"Show in Header","Footer"=>"Show in Footer","Both"=>"Show in Both Positions");		
		$this->addElement('select', 'menu_show', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Show on ',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $post
		));
		
  		$this->addElement('text', 'menu_permalink', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Menu Peramlink" ,
			"label" => "Menu Peramlink" 
 		));
 		

         
 		$status=  array("0"=>"NO Index","1"=>"Index");		
		$this->addElement('select', 'menu_index', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Index',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $status
		));
 		
		
		$status=  array("0"=>"No Follow","1"=>"Follow");		
		$this->addElement('select', 'menu_follow', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Follow',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $status
		));
		
		$status=  array("0"=>"Draft","1"=>"Publish");		
		$this->addElement('select', 'menu_status', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Status',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $status
		));
		
		
		$this->addElement('textarea', 'menu_meta_keywords', array (
			
			'class' => 'form-control',
 			'rows'=>'6',
			"label" => "Meta Keywords",
			"placeholder" => "Meta Keywords"
		));
		
		
		 $this->addElement('textarea', 'menu_meta_description', array (
 			'class' => 'form-control  ',
 			'rows'=>'6',
			"label" => "Meta Description",
			"placeholder" => "Meta Description"
		));
		
		
		 $this->addElement('textarea', 'menu_google_code', array (
 			'class' => 'form-control   ',
 			'rows'=>'6',
			"label" => "Google Analytics Code For Page",
			"placeholder" => "Google Analytics Code For Page"
		));
		
		
		$getRequestAdminList = $modelUser->getRequestAdminList();
  		$this->addElement('select', 'request_admin', array(
					  'class'      => ' form-control',
 					  'label'=>' Assign Request Admin ',
					  'validators' => array('NotEmpty'),
					  "filters"    => array("StringTrim","StripTags","HtmlEntities"),
					  'multiOptions' => $getRequestAdminList
		));
		
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
  
 	}
	
	
	/* 
		Site Configuration Form
	 */
	public function configuration($type=false){
		
		$modelStatic = new Application_Model_Static() ;
		
  		
		$fields = $modelStatic->getConfigs($type);
		
 		foreach($fields as $key=>$values){
			
 			if($values['config_key']=='miles_travel'){
					
					$this->addElement('text',$values['config_key'], array (
					"required" => TRUE,
					'class' => 'form-control required number',
					"placeholder" => $values['config_title'] ,
					"label" => $values['config_title'] ,
					"value"=>$values['config_value']
				));	
			}
			else if($values['config_key']=='job_users'){
					
					$this->addElement('text',$values['config_key'], array (
					"required" => TRUE,
					'class' => 'form-control required digits',
					"placeholder" => $values['config_title'] ,
					"label" => $values['config_title'] ,
					"value"=>$values['config_value']
				));	
			}
			else if(in_array($values['config_key'],array('explore_txt','hurry_text'))){
					
					$this->addElement('textarea',$values['config_key'], array (
					"required" => TRUE,
					'class' => 'form-control required ckeditor',
					"placeholder" => $values['config_title'] ,
					"label" => $values['config_title'] ,
					"value"=>$values['config_value']
				));	
			}
			else if(in_array($values['config_key'],array('footer_about','footer_address','footer_cpy'))){
					
					$this->addElement('textarea',$values['config_key'], array (
					"required" => TRUE,
					"rows"=>3,
					'class' => 'form-control required',
					"placeholder" => $values['config_title'] ,
					"label" => $values['config_title'] ,
					"value"=>$values['config_value']
				));	
			}
			else if(in_array($values['config_key'],array('google_analytic_script'))){
					
					$this->addElement('textarea',$values['config_key'], array (
					"required" => TRUE,
					"rows"=>3,
					'class' => 'form-control required',
					"placeholder" => $values['config_title'] ,
					"label" => $values['config_title'] ,
					"value"=>$values['config_value']
				));	
			}
			
			else
			{
				$this->addElement('text',$values['config_key'], array (
					"required" => TRUE,
					'class' => 'form-control required',
					"placeholder" => $values['config_title'] ,
					"label" => $values['config_title'] ,
					"value"=>$values['config_value']
				));	
			}
			
			
		}
		
		$this->submitButton();
		
	 
 	 
 	}
	
	
 	
	public function email_template(){
		
 		 
	 		## ---- EMAIL TEMPLETS TITEL  ---- ##
		$this->addElement('text', 'emailtemp_title', array (
			'class' => 'form-control required',
			"placeholder" => "Input Email Title",
			"label" => "Input Email Title",
			'validators' => array( array('NotEmpty', true, array ("messages" => "Please enter email title")))
		));


		## ---- EMAIL TEMPLETS SUBJECT  ---- ##
		$this->addElement('text', 'emailtemp_subject', array (
			'class' => 'form-control required',
			"placeholder" => "Input Email Subject",
			"label" => "Input Email Subject"
		));

	   ## ---- EMAIL TEMPLETS CONTENT  ---- ##
		$this->addElement('textarea', 'emailtemp_content', array (
			'class' => 'ckeditor form-control ',
			'id' => 'cleditor',
			"placeholder" => "Email Content Here " ,
			"label" => "Input Email Title",
		 
		));
		
		$this->submitButton();
		
 		
	}
	
	
	
	public function graphic_media(){
 		
		$this->addElement('text', 'media_title', array (
			'class' => 'form-control required',
			"placeholder" => " Title",
			"required"=>true,
			"label"=>' Title : <span class="required">*</span>',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" This field is required ")),
							),
		));
		
		
		$this->addElement('text', 'media_alt', array (
			'class' => 'form-control required',
			"placeholder" => " Alternate Text",
			"required"=>true,
			"label"=>' Alternate : <span class="required">*</span>',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" This field is required ")),
							),
		));
		
		
	 
		/* User Video Data */
 		$this->addElement('file', 'media_path', array (
			"placeholder" => " Upload  ",
			"id" => "media_path_image",
			"required"=>true,
 			"class" => "form-control",
			"label"=>"Upload Photo "
		));
		
 		$this->media_path->setDestination(MEDIA_IMAGES_PATH)
			->addValidator('Extension', false,"jpg,JPG,png,PNG,jpeg,JPEG")
			->addValidator('Size', false, "15MB");
		 
   		$this->submitButton();		
		
		
		
	}
	
	
	
	public function submitButton(){
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="fa fa-check"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	}
	
	
	
}

?>
