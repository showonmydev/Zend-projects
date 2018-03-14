<?php
require_once '../public/Braintree/lib/Braintree.php';
Braintree_Configuration::environment('sandbox');
  Braintree_Configuration::merchantId('dhyppgxv4pq6nhb5');
  Braintree_Configuration::publicKey('4p66f8zhx4xczzrc');
  Braintree_Configuration::privateKey('8c28ab40f5a8b5d5a6a8d13db322fd69');
   
  $btClientToken = Braintree_ClientToken::generate();

  $nonce = $_POST['payment_method_nonce'];
  $amount = $_POST['amount'];
  $orderId = rand(100000,999999999);
  if (isset($nonce)) {
    $result = Braintree_Transaction::sale(array(
      'orderId' => $orderId,
      'amount' => $amount,
      'paymentMethodNonce' => $nonce,
      'options' => array(
        'submitForSettlement' => true
      )
    ));
  
    if ($result->success) {
      $txn = $result->transaction;
      echo '<p>For your order ID <code>' . $orderId . '</code>, ' . 
           'the Braintree Sandbox transaction ID is <code>' . $txn->id . '</code>.</p>';
    } 
    else {
       echo "Payment Failed<br/><br/><br/>";
       var_dump($result);
     }


  }
    else {
       echo "Payment Failed 2";
     }
?>