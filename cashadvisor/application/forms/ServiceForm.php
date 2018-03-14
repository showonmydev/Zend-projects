<?php
class Application_Form_ServiceForm extends Twitter_Bootstrap_Form_Vertical
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
	
	
// add services form
	public function services(){
 		$this->addElement('text', 'service_name', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Service Name " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		
		  $this->addElement('file','service_image',array(
		       //'class' => 'required' ,
			   "required"=>true,
			   'id'=>'service_image',
			   "label"=>"Service Image",
			   "placeholder" => " Service Image ",
			   "accept"=>"image/*"
			   ));
			   
		 $this->service_image->setDestination(SERVICE_IMAGES_PATH)
		 ->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
		 ->addValidator('Size', false, IMAGE_VALID_SIZE);
   		 $this->service_image->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		 
 		$this->submitButton();
  	}
// end add services form.........................


// add form for sub category
	public function catform($id=false,$form_id=false){
		$modelService = new Application_Model_Services();
		$ifImageQuestion=$modelService->isalreadyimagequestion($id);  
 		$this->addElement('text', 'c_form_field_name', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"label" => "Field Name " ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		
		$this->addElement('radio', 'c_required_optional', array (
			'class' => 'required',
			"required"=>true,
			'multiOptions'=>array(
			'0' => 'Optional',
			'1' => 'Required',
			),
			"label" => "Field is required or optional",
			"title"=>"Please select field type either required or optional",
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select field type either required or optional")),
							),
		));
		
		if($form_id==''){
			
			if($ifImageQuestion==''){
				$fieldTypeArray = array(
						'0' => 'Text',
						'1' => 'Text Area',
						'2'=> 'Radio Box',
						'3'=>"Check Box",
						'4'=>"Image Upload",
					);
			}else {
					$fieldTypeArray = array(
						'0' => 'Text',
						'1' => 'Text Area',
						'2'=> 'Radio Box',
						'3'=>"Check Box",
					);
			}
		}else{
			$fieldTypeArray = array(
						'0' => 'Text',
						'1' => 'Text Area',
						'2'=> 'Radio Box',
						'3'=>"Check Box",
						'4'=>"Image Upload",
					);
		}
		
		$this->addElement('radio', 'c_form_field_type', array (
			'class' => 'required',
			"required"=>true,
			'multiOptions'=>$fieldTypeArray,
			"label" => "Select field input type",
			'onchange' => 'showhideoptionDiv(this.value)',
			"title"=>"Select field input type",
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select field input type")),
							),
		));
		
		$this->addElement('hidden', 'total_options', array (
			"required" => TRUE,
			'class' => 'form-control ',
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
 							),
 		));
		
			$this->addElement('radio', 'c_other', array (
			'class' => 'required',
			"required"=>true,
			'multiOptions'=>array(
			'0' => 'No',
			'1' => 'Yes',
			),
			"label" => "Need Other option",
			"title"=>"Please select Other option yes or no.",
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select Other option yes or no")),
							),
		));
		 		 
 		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'button',
				'onclick'=>'checkvalid()',
 				'label'=>'<i class="fa fa-check"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
  	}
// end add form for sub category.........................




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
				"label" => "Select Service " ,
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
		));
		
		
		  $this->addElement('file','service_image',array(
		       //'class' => 'required' ,
			   "required"=>true,
			   'id'=>'service_image',
			   "label"=>"Service Image",
			   "placeholder" => " Service Image ",
			   "accept"=>"image/*"
			   ));
			   
		 $this->service_image->setDestination(SERVICE_IMAGES_PATH)
		 ->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
		 ->addValidator('Size', false, IMAGE_VALID_SIZE);
   		 $this->service_image->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		 
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
			"label" => "Category Credit " ,
			"maxlength"=> "7",
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


// edit home category
	public function homecat($id= false){
			$modelService = new Application_Model_Services();
			  $service_category=$modelService->categorylist($id);  
			  
		$this->addElement('multiCheckbox', 'service_parent_id', array (
				'class' => 'form-control' ,
				//'multiple'=>true,
				"multioptions"=>$service_category,
				
		));
 		$this->submitButton();
  	}
// end edit home category.................................................................



	
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
