<head>
    <!-- Other Tags -->
  
    <!-- Moyasar Styles -->
    <link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.14.0/moyasar.css" />
  
    <!-- Moyasar Scripts -->
    <script src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?version=4.8.0&features=fetch"></script>
    <script src="https://cdn.moyasar.com/mpf/1.14.0/moyasar.js"></script>
  
    <!-- Download CSS and JS files in case you want to test it locally, but use CDN in production -->
  </head>

  <form accept-charset="UTF-8" action="https://api.moyasar.com/v1/payments.html" method="POST">
    <input type="hidden" name="callback_url" value="{{url(route('callback',[1,1]))}}" />
    <input type="hidden" name="publishable_api_key" value="pk_test_1GqUFmQHujs3VCYQZXaS7srJ1fm7sZTCEBUsfChz" />
    <input type="hidden" name="amount" value="1000" />
    <input type="hidden" name="source[type]" value="creditcard" />
    <input type="hidden" name="description" value="Order id 1234 by guest" />

    <input type="text" placeholder="name" name="source[name]" />
    <input type="number" placeholder="number" name="source[number]" />
    <input type="number" placeholder="month" name="source[month]" />
    <input type="number" placeholder="year" name="source[year]" />
    <input type="number"  placeholder="cvc" name="source[cvc]" />
    <button type="submit">Pay</button>
  </form>

  {{-- <div class="mysr-form"></div>
  <script>
    Moyasar.init({
        element: '.mysr-form',
        amount: 1000,
        currency: 'SAR',
        description: 'Coffee Order #1',
        publishable_api_key: 'pk_test_1GqUFmQHujs3VCYQZXaS7srJ1fm7sZTCEBUsfChz',
        callback_url: 'https://moyasar.com/thanks', // You can use this for redirection after payment, if needed
        methods: ['creditcard'],
        on_completed: function (payment) {
            return new Promise(function (resolve, reject) {
                console.log('Payment completed:', payment);
                
                // Send the payment data to your backend via a POST request
                fetch('/callback', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // If using CSRF token
                    },
                    body: JSON.stringify({
                        payment_id: payment.id,
                        amount: payment.amount,
                        currency: payment.currency,
                        description: payment.description,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resolve({});
                    } else {
                        reject();
                    }
                })
                .catch(error => {
                    console.error('Error sending payment data:', error);
                    reject();
                });
            });
        },
    });
</script> --}}
