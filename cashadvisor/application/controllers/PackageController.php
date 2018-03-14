<?php
class PackageController extends Zend_Controller_Action
{
    public function init(){
        $this->modelStatic = new Application_Model_Static();
        $this->modelUser = new Application_Model_User();
        $this->modelSuper = new Application_Model_SuperModel();
        $this->SuperModel = new Application_Model_SuperModel();
        $this->modelProject = new Application_Model_Project();

    }
// package show
    public function indexAction(){
        if($this->view->user->user_type=='service_provider'){
            $job_id = $this->getRequest()->getParam('job_id');
            $this->view->job_id=$job_id;


            $extra=array(
                'order'=>"cp_price desc"
            );
            $qry=$this->modelStatic->Super_Get("credit_package",1,"fetchAll",$extra);
            $this->view->package_details = $qry;
            //get page desc
            $page_desc=$this->modelStatic->Super_Get("Package_page",1,"fetch");
            $this->view->page_desc = $page_desc;
            //prd($page_desc);
// open form for credit card details
            $form = new Application_Form_PackageForm();
            $form->creditcardform();
            $this->view->form=$form;
            //prd($qry);
// history
            /*$purchased_packages=$this->modelStatic->Super_Get("package_purchased","pp_request_by='".$this->view->user->user_id."'","fetchAll");
            $this->view->package_history = $purchased_packages;
            prd($purchased_packages);*/

            $purchased_packages=$this->modelStatic->Super_Get("package_purchased","pp_request_by='".$this->view->user->user_id."'","fetchAll",array('order'=>"pp_id desc",'limit'=>1));
            $this->view->package_history = $purchased_packages;
            //prd($purchased_packages);
        }else {
            $this->_redirect("/profile/");
        }
    }
// open model
    public function paymodelAction()
    {
        $package_id = $this->getRequest()->getParam('current_package_id');
        $extra=array(
            'fields'=>"cp_price"
        );
        $qry1=$this->modelStatic->Super_Get("credit_package","cp_id='".$package_id."'","fetch",$extra);
        print_r($qry1['cp_price']);
        //prn($qry1);
        exit();
    }

// PAYMENT
    public function payAction(){
        global $objSession ;
        $job_id = $this->getRequest()->getParam('job_id');
        $this->view->job_id=$job_id;
        //prd($job_id);

        $this->view->pageHeading = "Featured request";
        $this->view->show = "Featured request";

        $business_id=$this->_getParam('business_id');
        $this->view->business_id=$business_id;

        $form = new Application_Form_PackageForm();
        $form->creditcardform();


        if($this->getRequest()->isPost()){/* begin : isPost() */
            $posted_data = $this->getRequest()->getPost();
            //,array("fields"=>"cp_price")
            $getPackage=$this->modelSuper->Super_Get("credit_package","cp_id='".$posted_data['package_id']."'","fetch");
            if(isset($posted_data)){ /* Begin : isValid()  */

                $data = $form->getValues();

                require_once ROOT_PATH.'/public/Braintree/lib/Braintree.php';
                //
                Braintree_Configuration::environment(BRAINTREE_MODE);
                Braintree_Configuration::merchantId(BRAINTREE_MERCHANT_ID);
                Braintree_Configuration::publicKey(BRAINTREE_PUBLIC_KEY);
                Braintree_Configuration::privateKey(BRAINTREE_PRIVATE_KEY);
                //
                $nonce = $_POST['payment_method_nonce'];
                $site_configs=$this->view->site_configs;
                $amount = $getPackage['cp_price'];
                $orderId = 'INVOICE_'.time();
                //

                if (isset($nonce)) {
                    $result = Braintree_Transaction::sale(array(
                        'orderId' => $orderId,
                        'amount' => $amount,
                        'paymentMethodNonce' => $nonce,
                        'options' => array(
                            'submitForSettlement' => true
                        )
                    ));


                    if ($result->success) { // insert values into package_purchased table

                        $insert_data['pp_request_by']=$this->view->user->user_id;
                        $insert_data['pp_request_date']=date("Y-m-d");
                        $insert_data['pp_pay_status']=1;
                        $insert_data['package_desc']="You have purchased a plan";
                        $insert_data['pp_amount_paid']=$getPackage['cp_price'];
                        $insert_data['pp_title']=$getPackage['cp_title'];
                        $insert_data['package_id']=$getPackage['cp_id'];
                        $insert_data['package_points']=$getPackage['cp_points'];
                        $insert_data['pp_payment_method']="BrainTree";
                        $isInserted=$this->SuperModel->Super_Insert("package_purchased",$insert_data);

                        // insert values into wallet table

                        $insert_points['wallet_user_id']=$this->view->user->user_id;
                        $insert_points['wallet_date']=date("Y-m-d");
                        $insert_points['wallet_point_status']=1;
                        $insert_points['wallet_user_point']=$getPackage['cp_points'];
                        $isInserted=$this->SuperModel->Super_Insert("wallet",$insert_points);
                        //exit ("Payment of $amount completed successfully using Braintree");
                    }

                    else
                    {
                        if($job_id!=''){
                            $result->message =  ucwords(str_replace("_", "", $result->message));
                            $objSession->errorMsg = $result->message;
                            $this->_redirect("/sendQuote".$job_id);
                        }else{
                            $result->message =  ucwords(str_replace("_", "", $result->message));
                            $objSession->errorMsg = $result->message;
                            $this->_redirect("/package");
                        }
                    }

                }
                else
                {
                    if($job_id!=''){
                        $objSession->errorMsg = "Payment Failed. Please Try Again";
                        $this->_redirect("/sendQuote".$job_id);
                    }else{
                        $objSession->errorMsg = "Payment Failed. Please Try Again";
                        $this->_redirect("/package");
                    }
                }



                if(is_object($isInserted)){

                    if($isInserted->success){
                        if($job_id!='\+d'){
                            $objSession->successMsg ="You can send proposal now.";
                            $this->_redirect("/sendQuote/".$job_id);
                        }else{
                            $objSession->successMsg ="Your payment is successful.";
                            $this->_redirect("/package/");

                        }
                    }



                    if($isInserted->error){

                        if(isset($isInserted->exception)){

                        }

                        $objSession->errorMsg = $isInserted->message;
                    }

                }

                else{
                    $objSession->errorMsg = " Please Check Information again ";
                }

            }
            else{

                $objSession->errorMsg = " Please Check Information Again..! ";
                exit($objSession->errorMsg);
            }

        }


        $this->view->form = $form;
    }
// END payment

// Package Purchase History
    public function packagepurchasehistoryAction(){
        $this->view->show = "wallet" ;
        $purchased_packages=$this->modelStatic->Super_Get("package_purchased","pp_request_by='".$this->view->user->user_id."'","fetchAll");
        $this->view->package_history = $purchased_packages;

        $Credit_IN_wallet = $this->modelProject->getcredits($this->view->user->user_id);
        $this->view->wallet = $Credit_IN_wallet;

    }
// end package purchased History

    public function payOLDAction(){

        global $objSession;

        require ROOT_PATH.'/private/authorizedNet/vendor/autoload.php';

        if($this->getRequest()->isPost()){
            $posted_data = $this->getRequest()->getPost();
            prn($posted_data);
            $extra=array(
                'fields'=>"cp_price"
            );
            $getPackagePrice=$this->modelStatic->Super_Get("credit_package","cp_id='".$posted_data['package_id']."'","fetch",$extra);
            prd($getPackagePrice);


            $site_configs = $this->view->site_configs;
            $PAYMENT_SESSION = new Zend_Session_Namespace('PAYMENT');

            $authNamespace = new Zend_Session_Namespace('Default');
            $billingData=$authNamespace->billingData;
            $payment_method=$billingData['payment_method'];

            $shipping_user=explode(' ',$billingData['shipping_user_name']);


            //$invoice_number=$data_order[0]['order_invoice'];
            //$invoice_number=str_replace('.','',$invoice_number);


            $invoice_number = 'invoice'.$data_order[0]['order_id'];

            //define("AUTHORIZENET_LOG_FILE", "phplog");

            // Common setup for API credentials
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($this->view->site_configs['api_login_id']);
            $merchantAuthentication->setTransactionKey($this->view->site_configs['transaction_key']);
            $refId = 'ref' . time();

            // Create the payment data for a credit card
            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($billingData['card_number']);
            $creditCard->setExpirationDate($billingData['user_credit_card_expire_year'].'-'.$billingData['user_credit_card_expire_month']);
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setCreditCard($creditCard);



            // Order info
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($invoice_number);
            $order->setDescription("Order Receive By ".$billingData['user_first_name'].' '.$billingData['user_last_name']);

            // Line Item Info

            /*	foreach($PAYMENT_SESSION->paypal_cart_info['product'] as $key=>$value){
                    $lineitem = new AnetAPI\LineItemType();
                    $lineitem->setName($value['product_name']);
                    $lineitem->setQuantity("1");
                    $lineitem->setUnitPrice($value['product_price']);
                    $lineitem->setTaxable(false);
                }*/

            // Customer info
            $customer = new AnetAPI\CustomerDataType();
            $customer->setId($billingData['user_id']);
            $customer->setEmail($billingData['user_email']);

            // PO Number
            $ponumber =$billingData['user_id'];
            //Ship To Info
            $shipto = new AnetAPI\NameAndAddressType();
            $shipto->setFirstName($shipping_user[0]);
            $shipto->setLastName($shipping_user[1]);
            $shipto->setAddress($billingData['shipping_user_address1']);
            $shipto->setCity($billingData['shipping_user_city']);
            $shipto->setState($billingData['shipping_user_state']);
            $shipto->setZip($billingData['shipping_user_zipcode']);
            $shipto->setCountry("USA");

            // Bill To
            $billto = new AnetAPI\CustomerAddressType();
            $billto->setFirstName($billingData['user_first_name']);
            $billto->setLastName($billingData['user_last_name']);
            $billto->setAddress($billingData['user_address']);
            $billto->setCity($billingData['user_city']);
            $billto->setState($billingData['user_state']);
            $billto->setZip($billingData['user_zipcode']);
            $billto->setCountry("USA");

            //create a transaction
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($Amount_val);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setOrder($order);
            //$transactionRequestType->addToLineItems($lineitem);
            $transactionRequestType->setPoNumber($ponumber);
            $transactionRequestType->setCustomer($customer);
            $transactionRequestType->setBillTo($billto);
            $transactionRequestType->setShipTo($shipto);

            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId( $refId);
            $request->setTransactionRequest( $transactionRequestType);



            $controller = new AnetController\CreateTransactionController($request);
            //$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::PRODUCTION);

            //$response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
            $response = $controller->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
            prd($tresponse);
            if ($response != null)
            {

                $tresponse = $response->getTransactionResponse();

                if (($tresponse != null) && ($tresponse->getResponseCode()=="1") )
                {
                    //echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                    //echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";

                    $modelOrder = new Application_Model_Order();
                    $data_order = $modelOrder->getOrder("order_invoice = '".$PAYMENT_SESSION->order_invoice."'");
                    /*payment success*/

                }
                else
                {
                    $objSession->errorMsg = "Please try again. Credit card invalid";
                    $this->redirect('cart/checkout');
                }
            }
            else
            {
                $objSession->errorMsg = "Please try again. Credit card invalid";
                $this->redirect('cart/checkout');
            }



            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }

    }

}