<?php
class Application_Form_PackageForm extends Twitter_Bootstrap_Form_Vertical
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

// add packages form
    public function packages(){
        $this->setAttribs(array(
            'id' => 'package_form',
            'class' => 'profile_form',
        ));
        $this->addElement('text', 'cp_title', array (
            "required" => TRUE,
            'class' => 'form-control required',
            "label" => "Package Name " ,
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
            ),
        ));
        $this->addElement('text', 'cp_price', array (
            'class' => 'form-control required digits',
            "label" => "Package Price" ,
            //"onkeypress"=>"return checkprice(event)",
            //"onchange"=>"check_value(this.value)",
            "maxlength"=> "7",
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>"Price is required ")),
            ),
        ));
        $this->addElement('text', 'cp_points', array (
            "required" => TRUE,
            'class' => 'form-control required digits',
            "label" => "Package Credits " ,
            "maxlength"=> "7",
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>"Price is required ")),
            ),
        ));
        $this->addElement('text', 'cp_desc', array (
            "required" => TRUE,
            'class' => 'form-control required',
            "label" => "Package Description " ,
            "maxlength"=> "30",
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
            ),
        ));
        $this->addElement('text', 'cp_sub_desc', array (
            "required" => TRUE,
            'class' => 'form-control required',
            "label" => "Package Sub Description ",
            "maxlength"=> "30",
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
            ),
        ));

        $this->submitButton();
    }
// end add packages form.........................


//Payment form
    public function creditcardform(){
        //for loop for mnth
        $mm = array();
        $mm[0]['key']="";
        $mm[0]['value']="MM";
        for($i=1;$i<13;$i++){
            $mm[$i] = $i;
        }
//for loop for year
        $yy = array();
        $yy[0]['key']="";
        $yy[0]['value']="YYYY";
        for($i=2016;$i<2051;$i++){
            $yy[$i] = $i;
        }

        $this->addElement('text', 'card_number', array (
            "required" => TRUE,
            'data-braintree-name' => 'number',
            'class' => 'form-control required creditcard',
            "label" => "Credit Card Number" ,
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
            ),
        ));
        $this->addElement('text', 'cvv', array (
            "required" => TRUE,
            'data-braintree-name' => 'cvv',
            'class' => 'form-control required',
            "label" => "CVV" ,
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>"CVV is required ")),
            ),
        ));

        $this->addElement('text', 'card_holder_name', array (
            'class' => 'form-control required',
            'data-braintree-name' => 'cardholder_name',
            "required"=>true,
            'label' => 'Card Holder Name',
            "filters"    => array("StringTrim","StripTags","HtmlEntities"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>"Card Holder Name is required ")),
            ),
        ));
//dob== mm
        $this->addElement('select', 'user_credit_card_expire_month', array (
            'class' => 'form-control ' ,
            "required"=>true,
            'data-braintree-name' => 'expiration_month',
            //"label" => "Choose a secret question",
            "multioptions"=>$mm,
            "filters" => array("StringTrim","StripTags","HtmlEntities"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>"Month is Required ")),
            ),
        ));
//dob == yy
        $this->addElement('select', 'user_credit_card_expire_year', array (
            'class' => 'form-control ' ,
            "required"=>true,
            'data-braintree-name' => 'expiration_year',
            //"label" => "Choose a secret question",
            "multioptions"=>$yy,
            "filters" => array("StringTrim","StripTags","HtmlEntities"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>"Year is Required ")),
            ),
        ));
        $this->submitButton();
        $this->bttnsubmit->setLabel("Pay");
        $this->bttnsubmit->setAttrib("class","btn-lg site_button");
    }
//Payment form.........................

    public function packagepagedesc(){
        $this->setAttribs(array(
            'id' => 'package_form',
            'class' => 'profile_form',
        ));
        $this->addElement('text', 'package_page_desc', array (
            "required" => TRUE,
            'class' => 'form-control required',
            "label" => "Page Description " ,
            "maxlength"=> "300",
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
            ),
        ));
        $this->addElement('hidden', 'package_page_id', array (

        ));
        $this->submitButton();
    }


    /*public function providerdistance(){
            $this->setAttribs(array(
             'id' => 'package_form',
            'class' => 'profile_form',
            ));
        $this->addElement('text', 'provider_distance_val', array (
            "required" => TRUE,
            'class' => 'form-control required',
            "label" => "Service provider service area dispance" ,
            "maxlength"=> "5",
            "filters"    => array("StringTrim","StripTags"),
            "validators" =>  array(
                                array("NotEmpty",true,array("messages"=>" Expertise Title is required ")),
                             ),
         ));
        $this->addElement('hidden', 'p_distance_id', array (

         ));
        $this->submitButton();
    }*/



// serach form
    public function serchpackages(){
        $this->setAttribs(array(
            'id' => 'search_form',
            'class' => 'profile_form',
        ));

        $dates=array(
            '1' => 'Date',
        );

        $this->addElement('text', 'pp_request_date', array (
            'class' => 'form-control',
            //"readonly"=>'readonly',
            "placeholder" => "Search by Date",
            "onchange"=>'refreshtable(this.value)',
            "filters"    => array("StringTrim","StripTags","HtmlEntities"),
        ));
        $this->submitButton();
        $this->bttnsubmit->setLabel("Search");

        $this->addElement('button', 'resetform', array(
            'ignore'   => true,
            'type'=>'button',
            'label'    => 'Clear',
            'class'    => 'btn btn-md purple'
        ));
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
