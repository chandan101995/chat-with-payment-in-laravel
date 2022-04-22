<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
        integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
</head>

<body>
    <!-- Replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=test&currency=USD&debug=true"></script>

    <div class="container">

        <div class='row'>
            <div class='col-md-4'>
                <h4>PayPal</h4>
                <!-- Set up a container element for the button -->
                <form action="{{route('paymany')}}" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="amount" value="1" />
                    <input type="submit" class="btn btn-success" value="Pay with PayPal" />
                </form>
            </div>
            <div class='col-md-4'>
                <h4>Stripe</h4>
                <script src='https://js.stripe.com/v2/' type='text/javascript'></script>
                <form accept-charset="UTF-8" action="/check" class="require-validation" data-cc-on-file="false"
                    data-stripe-publishable-key="{{env('STRIPE_KEY')}}"
                    id="payment-form" method="post">
                    @csrf
                    <div class='form-row'>
                        <div class='col-xs-12 form-group required'>
                            <label class='control-label'>Card Holder Name</label> <input class='form-control' size='4'
                                type='text' placeholder="Enter Card Holder Name">
                        </div>
                    </div>
                    <div class='form-row'>
                        <div class='col-xs-12 form-group card required'>
                            <label class='control-label'>Card Number</label> <input autocomplete='off'
                                class='form-control card-number' size='20' type='text' placeholder="Enter Card number">
                        </div>
                    </div>
                    <div class='form-row'>
                        <div class='col-xs-4 form-group cvc required'>
                            <label class='control-label'>CVC</label> <input autocomplete='off'
                                class='form-control card-cvc' placeholder='CVV' size='4' type='text'>
                        </div>
                        <div class='col-xs-4 form-group expiration required'>
                            <label class='control-label'>Expiration</label> <input
                                class='form-control card-expiry-month' placeholder='MM' size='2' type='text'>
                        </div>
                        <div class='col-xs-4 form-group expiration required'>
                            <label class='control-label'>YEAR</label> <input class='form-control card-expiry-year'
                                placeholder='YYYY' size='4' type='text'>
                        </div>
                    </div>
                    <div class='form-row'>
                        <div class='col-md-12 form-group'>
                            <button class='form-control btn btn-primary submit-button' type='submit'
                                style="margin-top: 10px;">Confirm</button>
                        </div>
                    </div>
                    <div class='form-row'>
                        <div class='col-md-12 error form-group hide'>
                            <div class='alert-danger alert'>Please correct the errors and try
                                again.</div>
                        </div>
                    </div>
                </form>
                @if (Session::has('success-message'))
                    <div class="alert alert-success col-md-12">
                        <div class="alert alert-success col-md-12">
                            {{Session::get('success-message') }}
                        </div>
                @endif
                @if (Session::has('fail-message'))
                    <div class="alert alert-danger col-md-12">
                        {{ Session::get('fail-message') }}
                    </div>
                @endif
            </div>
            <div class='col-md-4'>
                <h4>Razer Pay</h4>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script>
        $(function() {
            $('form.require-validation').bind('submit', function(e) {
                var $form = $(e.target).closest('form'),
                    inputSelector = ['input[type=email]', 'input[type=password]',
                        'input[type=text]', 'input[type=file]',
                        'textarea'
                    ].join(', '),
                    $inputs = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.error'),
                    valid = true;

                $errorMessage.addClass('hide');
                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('hide');
                        e.preventDefault(); // cancel on first error
                    }
                });
            });
        });

        $(function() {
            var $form = $("#payment-form");

            $form.on('submit', function(e) {
                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }
            });

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('.error')
                        .removeClass('hide')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                    // token contains id, last4, and card type
                    var token = response['id'];
                    // insert the token into the form so it gets submitted to the server
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    console.log(token);
                    $form.get(0).submit();
                }
            }
        })

        // paypal script
        
    </script>
</body>

</html>
