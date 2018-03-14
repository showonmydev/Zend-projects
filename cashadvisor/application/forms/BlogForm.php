<?php
class Application_Form_BlogForm extends Twitter_Bootstrap_Form_Vertical
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
	
	public function addblog(){
		
		$modelblog = new Application_Model_Blog();
		
		$blogcategorylist = $modelblog->blogcategorylist();
		
			$this->addElement('text', 'blog_title', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "Blog Title " ,
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
								),
			));
			
			$this->addElement('select', 'blog_cat_id', array (
				'class' => 'form-control required' ,
				"required"=>true,
				"multioptions"=>$blogcategorylist,
				"label" => "Select Blog Category " ,
				"filters" => array("StringTrim","StripTags","HtmlEntities"),
				
			));
			
			$this->addElement('text', 'blog_url', array (
				"required" => TRUE,
				'class' => 'form-control required checkblogUrl',
				"label" => "Blog Url " ,
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
								),
			));
			
		 ## ---- EMAIL TEMPLETS CONTENT  ---- ##
			$this->addElement('textarea', 'blog_content', array (
				'class' => 'ckeditor form-control ',
				'id' => 'cleditor',
				"placeholder" => "Blog Content Here " ,
				"label" => "Input Blog Content Here",
			 
			));
			
			$this->addElement('text', 'blog_tag', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "Tagged with(comma separated) " ,
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
								),
			));

			
			$this->addElement('file','blog_image',array(
			  // 'class' => 'required' ,
			 //  "required"=>true,
			   'id'=>'blog_image',
			   "label"=>"Blog Image",
			   "placeholder" => " Blog Image ",
			   "accept"=>"image/*"
			));
			   
		 $this->blog_image->setDestination(BLOG_IMAGES_PATH)
		 ->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
		 ->addValidator('ImageSize', false, array('minwidth' => 700,'minheight' => 450))
		 ->addValidator('Size', false, IMAGE_VALID_SIZE);
   		 $this->blog_image->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');

			
			$this->submitButton();
			$this->bttnsubmit->setLabel("Submit");
		}
		
	public function addblogcat(){
		
			$this->addElement('text', 'blog_category_title', array (
				"required" => TRUE,
				'class' => 'form-control required',
				"label" => "Blog Category Title " ,
				"filters"    => array("StringTrim","StripTags"),
				"validators" =>  array(
									array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
								),
			));
			
			$this->submitButton();
			$this->bttnsubmit->setLabel("Submit");
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
	
	public function comment()
	{
		  $this->addElement('textarea','blog_comment',array(
				   'class' => 'form-control required  user_text' ,
				   "required"=>true,
				   "rows"=>3, 
				  // "label"=>"Post a Comment",
				   "placeholder"=>"Start the discussion…",
				   "filter"=> array("StringTrim","StripTags","HtmlEntities"),
					"validators"=> array(
									array("NotEmpty",true,array("messages"=>"Blog Description is Required "))
							),		
		 ));
		 
		  $this->addElement('button','saveblogBtn',array(
					   'class'=> 'btn  site_button',
					   'type' => 'button',
						'ignore'=>true,
					   'label'=>'Submit',
						'escape'=>false
			 ));
			  $this->saveblogBtn->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions'))	));
			  
		$this->addElement('button','cancelbtn',array(
					   'class'=> 'btn sign_inn login_btnn',
					   'type' => 'reset',
					   'ignore'=>true,
					   'label'=>'Cancel',
					   'escape'=>false
					   
			 ));
				  $this->cancelbtn->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions'))	));		 
	}

}

?>
