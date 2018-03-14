<?php
  require_once '../public/Braintree/lib/Braintree.php';
   
  Braintree_Configuration::environment('sandbox');
  Braintree_Configuration::merchantId('dhyppgxv4pq6nhb5');
  Braintree_Configuration::publicKey('4p66f8zhx4xczzrc');
  Braintree_Configuration::privateKey('8c28ab40f5a8b5d5a6a8d13db322fd69');
   
  $btClientToken = Braintree_ClientToken::generate();


?>

<form id="checkout" method="post" action="pay.php">
Amount $ <input type="text" name="amount" value="2.34">
Card number: <input data-braintree-name="number">
Expiration Date (MM/YY): <input data-braintree-name="expiration_date">
CVV: <input data-braintree-name="cvv">
<input type="submit" id="submit" value="Pay">
</form>


<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<script>
  braintree.setup(
    "<?php echo $btClientToken; ?>", 
    "custom", {
      id: "checkout"
    });
</script>

