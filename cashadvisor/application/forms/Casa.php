<?php
class Application_Form_User extends Twitter_Bootstrap_Form_Vertical
{
	
	public function init(){
 
  		$this->setMethod('post');
 		
		$this->setAttribs(array(
 			'class' => 'profile_form',
 			'novalidate'=>'novalidate',
			"role"=>"form",
			'enctype'=>'multipart/form-data'
		));
		
		
  	}
	
		
public function casahomeslider(){
		 
	 $this->addElement('text','service',array(
		       'class' => 'form-control required' ,
			   "required"=>true,
			   "placeholder"=>"What service do you need?",
			   "filter"=> array("StringTrim","StripTags","HtmlEntities"),
			   "validators"=> array(
			                    array("NotEmpty",true,array("messages"=>"Enter service type"))
			                       ),
		 )); 
		 
		  $this->addElement('button','getstart',array(
		           'class'=> 'btn',
				   'type' => 'submit',
				    'ignore'=>true,
				   'label'=>'Get Started',
					'escape'=>false
				   
				  
		 ));
		  $this->savebtn->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions'))	));
	
     }
}