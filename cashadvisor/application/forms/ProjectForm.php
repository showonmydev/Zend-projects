<style type="text/css">
.width_90{ width:90%; margin:0 auto;}
.p_0{ padding:5px !important;}
</style>
<?php
class Application_Form_ProjectForm extends Twitter_Bootstrap_Form_Vertical
{
 
 	public function init(){
		
		$this->setMethod('post');
		
		$this->setAction(self::METHOD_POST);
		
		$this->setAttribs(array(
			'id' => 'validate',
			"role"=>"form",
			'class' => 'profile_form  validate',
			"novalidate"=>"novalidate"
		));
  		
	}
//Review
public function review(){
		$this->addElement('text', 'review_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Review Title " ,
			"placeholder" => "Headline for your review",
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		
		$this->addElement('textarea', 'review_msz', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Write about your experience" ,
			"rows"=>"4",
			"placeholder" => "Describe the service you were provided, what went well, what could have been better?",
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Review message is required ")),
							),
		));
		
		$this->submitButton();
		$this->submitButton();
		$this->bttnsubmit->setLabel("Post Review");
		$this->bttnsubmit->setAttrib("class","site_button text-center");
	}	

	
	
//hire SP
	public function hireSP(){
			$this->addElement('text', 'hire_quote_client', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "Your hire quote price($):" ,
				"maxlength"=>"7",
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"Price is required ")),
								),
			));
			
			$this->submitButton();
			$this->bttnsubmit->setLabel("Hire");
			$this->bttnsubmit->setAttrib("class","site_button");
		}	
		
		
//more quote request by client
	public function moreQuoteClient(){
			$this->addElement('text', 'more_quote_client_request', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "How many more quote do you want?" ,
				"maxlength"=>"2",
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"more quote number is required ")),
								),
			));
			
			$this->submitButton();
			$this->bttnsubmit->setLabel("Request");
			$this->bttnsubmit->setAttrib("class","site_button");
		}	
		
//more quote request by Admin
	public function moreQuoteAdmin(){
			$this->addElement('text', 'more_quote_admin_allowed', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "How many more quote do you allow?" ,
				"maxlength"=>"2",
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"more quote number is required ")),
								),
			));
			
			$this->submitButton();
			$this->bttnsubmit->setLabel("Allow");
			$this->bttnsubmit->setAttrib("class","site_button");
		}	
		
		
	
// job page content

	public function jobpage(){
			$this->setAttribs(array(
 			'id' => 'jobpage_form',
			'class' => 'profile_form',
			));
		$this->addElement('text', 'job_page_how_step_heading', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Step Heading " ,
			"maxlength"=> "50",
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		
		$this->addElement('text', 'job_page_step_desc', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Step Description " ,
			"maxlength"=> "200",
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
			
		 $this->addElement('file','job_page_how_icon',array(
		       //'class' => 'required' ,
			   "required"=>true,
			   'id'=>'job_page_how_icon',
			   "label"=>"Step Icon",
			   "placeholder" => " Step Icon",
			   "accept"=>"image/*"
			   ));
			   
		 $this->job_page_how_icon->setDestination(JOB_PAGE_ICON_IMAGES_PATH)
		 ->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
		 ->addValidator('Size', false, IMAGE_VALID_SIZE);
   		 $this->job_page_how_icon->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		$this->addElement('hidden', 'job_page_id', array (
				
 		));
		$this->submitButton();
	}

	public function projectpagedesc(){
			$this->setAttribs(array(
 			'id' => 'package_form',
			'class' => 'profile_form',
			));
		$this->addElement('text', 'project_page_desc', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Page Description " ,
			"maxlength"=> "300",
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		$this->addElement('hidden', 'project_page_id', array (
				
 		));
		$this->submitButton();
	}


	
// add project form
	public function addnewproject(){
		
		$modelservices = new Application_Model_Services();
		$servicelist = $modelservices->servicesdata();
		$caterories = $modelservices->categorydata();
		$subcaterories = $modelservices->subcategorydata();
		//prn($servicelist);
		$this->addElement('select', 'service_parent_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$servicelist,
				"label" => "Job Service " ,
				"onchange"=>"getcategorylist(this.value)",
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
		$this->addElement('select', 'service_sub_parent_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$caterories,
				"label" => "Job Service Category" ,
				"onchange"=>"getsubcategorylist(this.value)",
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
		$this->addElement('select', 'service_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$subcaterories,
				"label" => "Job Service Sub Category" ,
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
		
		$this->addElement('button', 'save_and_continue', array (
				'class' => 'btn blue site_button  ',
				'ignore'=>true,
				'type'=>'button',
				'id' => 'save_and_continue',
				'onclick' => 'getcatId()',
 				'label'=>'Next',
				//'disabled'=>"disabled",
				'escape'=>false
		));
		$this->save_and_continue->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
	
		
 		$this->submitButton();
		$this->bttnsubmit->setLabel("Submit");
		$this->bttnsubmit->setAttrib("class","site_button");
  	}
	
	
	
	
	
	public function postnewjob(){
		
		/*$modelproject = new Application_Model_Project();
		$zipcodelist = $modelproject->zipcodelist();	*/
		global $job_deadline;
		$this->addElement('select', 'job_deadline', array (
				'class' => 'form-control required' ,
				"required"=>true,
				'id' => 'job_deadline',
				"multioptions"=>$job_deadline,
				// 'onchange' => 'Checkoption(this)',
				"label" => "When do you need the work to start",
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
		
		/*$this->addElement('textarea', 'job_deadline_other_option', array (
			'class' => 'form-control p_0 ',
			"required"=>true,
			'id' => 'job_deadline_other_option',
			 "rows"=>5, 
			'Placeholder'=> 'When would you like to schedule this for?',
			//"label" => "When do you need the work to start",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));*/
		
		$this->addElement('text', 'job_deadline_date', array (
				'class' => 'form-control',
				"placeholder" => "Select Date",
				"readonly"=>"readonly", 
				"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		));
		
		$this->addElement('text', 'job_phone_client', array (
			'class' => 'form-control required text-center User_job_Post_Form',
			"required"=>true,
			'label' => 'Phone Number',  
			'Placeholder'=> 'Phone Number with valid code (+1)',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
// zip code textbox		
		$this->addElement('radio', 'job_anything_else_know', array (
			'class' => 'required RedioCheckboxStyle User_job_Post_Form',
			"required"=>true,
			'id' => 'job_anything_else_know',
		//"onclick"=>"isElse()", 
			'multiOptions'=>array(
			'0' => 'Yes',
			'1' => 'No',
			),
			"label" => "Anything else service provider should know? ",
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select.")),
							),
		));
		
		$this->addElement('textarea', 'yes_else_know', array (
			'class' => 'form-control text-left p_0',
			"required"=>true,
			"rows"=>2, 
			'id' => 'yes_else_know',
			'Placeholder'=> 'Write here...',
			//"label" => "When do you need the work to start",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Business description is required ")),
 								),
		));
		
		$this->addElement('radio', 'how_receive_quote', array (
			'class' => 'required RedioCheckboxStyle User_job_Post_Form',
			"required"=>true,
			'multiOptions'=>array(
			'0' => 'By email only',
			'1' => 'By email and text message',
			),
			"label" => "How would you like to receive quotes?  ",
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select.")),
							),
		));
		
		
		//$this->how_receive_quote->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		$this->addElement('text', 'client_zip_code', array (
				'class' => 'form-control required text-center User_job_Post_Form',
				"label" => "Please confirm where you need the job to be done. " ,
				'Placeholder'=> 'Zip Code',
				/*'onchange'=> 'checkzipcode(this.value)',*/
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"zip code is required ")),
								),
			));
			
		 $job_budget=array(
			'1' => '0-100',
			'2' => '100-200',
			'3' => '200-500',
			'4' => '500-700',
			'5' => '700-1000',
			'6' => '1000-1500',
			'7' => '1500-2000',
			'8' => '2000-5000',
			'9' => '5000-7000',
			'10' => 'more than 7000',
			
		);
		$this->addElement('select', 'posted_job_budget', array (
			'class' => 'form-control',
			"multioptions"=>$job_budget,
			'label' => 'Job Budget',   
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		));
	}
// end add project form.........................

// comment form

		public function communication()
		{
			  $this->addElement('textarea','c_massage',array(
					   'class' => 'form-control required  user_text userChatMsgInput' ,
					   "required"=>true,
					   "rows"=>3, 
					   "label"=>"Write Message...",
					   "placeholder"=>"Enter your Message.....",
					   "filter"=> array("StringTrim","StripTags","HtmlEntities"),
						"validators"=> array(
										array("NotEmpty",true,array("messages"=>"Enter comment "))
								),		
			 ));
		// save btn
				$this->addElement('button', 'savebtn', array (
					'class' => 'btn-lg blue site_button ',
					'ignore'=>true,
					'type'=>'button',
					'label'=>'Send',
					'escape'=>false
			));
					$this->savebtn->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
			
		//cancel btn
				  $this->addElement('button','cancelbtn',array(
						   'class'=> 'btn sign_inn login_btnn',
						   'type' => 'reset',
						   'ignore'=>true,
						   'label'=>'Cancel',
						   'escape'=>false
				 ));
					  $this->cancelbtn->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions'))	));		 
		}
// proposal
		public function proposal()
		{
			$this->addElement('text', 'proposal_credit', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "Your price($)" ,
				"maxlength"=>"7",
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>"Price is required ")),
								),
			));
			  $this->addElement('textarea','proposal_description',array(
					   'class' => 'form-control required  user_text' ,
					   "required"=>true,
					   "rows"=>3, 
					   "placeholder"=>"write your message here.....",
					   "filter"=> array("StringTrim","StripTags","HtmlEntities"),
						"validators"=> array(
										array("NotEmpty",true,array("messages"=>"Proposal Description is Required "))
								),		
			 ));
		// save btn
			//$this->submitButton();
		//	$this->bttnsubmit->setLabel("Submit");
		//	$this->bttnsubmit->setAttrib("class","btn-lg site_button");
			
			$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-lg site_button sendProposalBtn',
				'ignore'=>true,
				'type'=>'button',
 				'label'=>'Submit',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	 
		}

// add service category form
	public function servicecat(){
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
  	}
// end add service category form.................................................................

// add sub category form
	public function subcategory(){
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
		$this->addElement('text', 'service_price', array (
			"required" => TRUE,
			'class' => 'form-control required digits',
			"label" => "Service Credit " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Price is required ")),
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
  	}
// end add sub category form.............................................................................

	
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
