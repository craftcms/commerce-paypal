function initCheckout() {
  // Because this might get executed before Paypal is loaded.
  if (typeof paypal === "undefined") {
    setTimeout(initCheckout, 200);
  } else {
    var $wrapper = $('.paypal-rest-form');
    var $form = $wrapper.parents('form');
    var paymentUrl = $wrapper.data('prepare');
    
    paypal.Button.render({
      
      env: $wrapper.data('env'),
      commit: true,
      
      payment: function() {
        
        // Copy over all the data to simulate a form being submitted
        var postData = {};
        var $formElements = $form.find('input[type=hidden]');
        
        for (var i = 0; i < $formElements.length; i++) {
          if ($formElements[i].name === 'action') {
            continue;
          }
          postData[$formElements[i].name] = $formElements.get(i).value;
        }
        
        return paypal.request.post(paymentUrl, postData).then(function(data) {
          if (data.error) {
            alert(data.error);
            
            return false;
          }
          
          return data.transactionId;
        });
      },
      
      onAuthorize: function(data) {
        return paypal.request.post(data.returnUrl).then(function(data) {
          window.location = data.url;
        });
      }
      
    }, '#paypal-button');
    
    $form.find('[type=submit]').remove();
    if ($('.modal').data('modal')) {
      $('.modal').data('modal').updateSizeAndPosition();
    }
  }
}

initCheckout();