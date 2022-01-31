<!DOCTYPE html>

<?php
// echo 'dsa';exit;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>
<!-- Load the PayPal JS SDK with your PayPal Client ID-->
<!-- Load the Braintree components -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.82.0/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.82.0/js/data-collector.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.82.0/js/paypal-checkout.min.js"></script>

<style>
.paypal-button-label-container  {
  display: none !important;
}
#error {
  display: none;
  color: red;
}
</style>

<div id="paypal-button"></div>
<div id="error">ERROR</div>


<script>
braintree.client.create({
  authorization: 'sandbox_q7dr2y96_zynjg7c9rd5c95z2'
}, function (clientErr, clientInstance) {
  if (clientErr) {
    console.error('Error creating client:', clientErr);
    return;
  }

// Start Pi4
  braintree.paypalCheckout.create({
    client: clientInstance
  }, function (paypalCheckoutErr, paypalCheckoutInstance) {

    paypalCheckoutInstance.loadPayPalSDK({
      components: 'buttons,messages',
      currency: 'AUD',
       dataAttributes: {
          amount: '150.00'
        },
      locale: 'en_AU',
      intent: 'capture',
    }, function () {
     var button =  paypal.Buttons({
        //validation

               onClick: function(data, actions) {
                // You must return a promise from onClick to do async validation
                return fetch('gp_validation.php?result=fail', {
                  method: 'post',
                  headers: {
                    'content-type': 'application/json'
                  }
                }).then(function(res) {
                  return res.json();
                }).then(function(data) {
                 // console.log(data);return false;
                  // If there is a validation error, reject, otherwise resolve
                  if (data.res == 'fail') {
                    $('#error').show();
                    console.log("Validation error");
                    return actions.reject();
                  } else {
                    return actions.resolve();
                  }
                });
              },
          style: {
              shape: 'rect',
              color: 'gold',
              layout: 'vertical',
             //label: 'donate',
              
            },
    fundingSource: paypal.FUNDING.PAYPAL,
       createOrder: function () {
          return paypalCheckoutInstance.createPayment({
            flow: 'checkout', // Required
            amount: '150.00', // Required
            currency: 'AUD', // Required, must match the currency passed in with loadPayPalSDK
            intent: 'capture', // Must match the intent passed in with loadPayPalSDK
          });
        },

        onApprove: function (data, actions) {
          return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
            console.log(payload.nonce);//return false;
            window.location.href = 'sale.php?nounce='+payload.nonce;
          });
        },
      });
    button.render('#paypal-button');

    });

  });

});
// end Pi4 




</script>
  </body>
</html>