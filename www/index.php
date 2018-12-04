<?php
session_start();
$_SESSION['id'] = base64_encode(session_id()); // Create token for test
?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta charset="utf-8" />
        <title>Wirecard Checkout</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="description" content="Wirecard Checkout"/>
        <meta name="keywords" content="" />
        <meta name="author" content="Vitor Cazelatto" />
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <style>
            /* Padding - just for asthetics on Bootsnipp.com */
            body { margin-top:20px; }

            /* CSS for Credit Card Payment form */
            .credit-card-box .panel-title {
                display: inline;
                font-weight: bold;
            }
            .credit-card-box .form-control.error {
                border-color: red;
                outline: 0;
                box-shadow: inset 0 1px 1px rgba(0,0,0,0.075),0 0 8px rgba(255,0,0,0.6);
            }
            .credit-card-box label.error {
                font-weight: bold;
                color: red;
                padding: 2px 8px;
                margin-top: 2px;
            }
            .credit-card-box .payment-errors {
                font-weight: bold;
                color: red;
                padding: 2px 8px;
                margin-top: 2px;
            }
            .credit-card-box label {
                display: block;
            }
            /* The old "center div vertically" hack */
            .credit-card-box .display-table {
                display: table;
            }
            .credit-card-box .display-tr {
                display: table-row;
            }
            .credit-card-box .display-td {
                display: table-cell;
                vertical-align: middle;
                width: 100%;
            }
            /* Just looks nicer */
            .credit-card-box .panel-heading img {
                min-width: 180px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <!-- CREDIT CARD FORM STARTS HERE -->
                    <div class="panel panel-default credit-card-box">
                        <div class="panel-heading display-table" style="background-color: #0a1421;border-color: #0a1421;">
                            <div class="row display-tr" >
                                <h3 class="panel-title display-td" style="color: #fff;">Payment Details</h3>
                                <div class="display-td" >                            
                                    <img class="img-responsive pull-right" src="https://wirecard.com.br/wp-content/themes/moip/home-static_files/branding.png" style="width: 120px !important;min-width: 120px !important;">
                                </div>
                            </div>                    
                        </div>
                        <div class="panel-body">
                            <form role="form" id="payment-form" method="POST" action="javascript:void(0);">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="couponCode">FULL NAME</label>
                                            <input type="text" class="form-control" name="buyerName" id="buyerName"/>
                                        </div>
                                    </div>                        
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="couponCode">CPF</label>
                                            <input type="text" class="form-control" name="buyerCpf" id="buyerCpf" />
                                        </div>
                                    </div>                        
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="couponCode">E-MAIL</label>
                                            <input type="text" class="form-control" name="buyerEmail" id="buyerEmail" />
                                        </div>
                                    </div>                        
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="couponCode">TYPE PAYMENT</label>
                                            <select id="paymentType" name="paymentType" class="form-control">
                                                <option value="">Select...</option>
                                                <option value="boleto">Boleto</option>
                                                <option value="creditCard">Credit Card</option>
                                            </select>
                                        </div>
                                    </div>                        
                                </div>
                                <div id="payment-content" style="display: none;">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label for="couponCode">FULL HOLDER NAME</label>
                                                <input type="text" class="form-control" name="cardName" id="cardName"/>
                                            </div>
                                        </div>                        
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label for="cardNumber">CARD NUMBER</label>
                                                <div class="input-group">
                                                    <input 
                                                        type="tel"
                                                        class="form-control"
													    id="cardNumber"
                                                        name="cardNumber"
                                                        placeholder="Valid Card Number"
                                                        autocomplete="cc-number"
                                                        required autofocus 
                                                        />
                                                    <span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
                                                </div>
                                            </div>                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-7 col-md-7">
                                            <div class="form-group">
                                                <label for="cardExpiry"><span class="hidden-xs">EXPIRATION</span><span class="visible-xs-inline">EXP</span> DATE</label>
                                                <input 
                                                    type="tel" 
                                                    class="form-control" 
                                                    name="cardExpiry"
                                                    placeholder="MM / YY"
                                                    autocomplete="cc-exp"
                                                    required 
                                                    />
                                            </div>
                                        </div>
                                        <div class="col-xs-5 col-md-5 pull-right">
                                            <div class="form-group">
                                                <label for="cardCVV">CVV CODE</label>
                                                <input 
                                                    type="tel" 
                                                    class="form-control"
                                                    name="cardCVV"
                                                    placeholder="CVV"
                                                    autocomplete="cc-csc"
                                                    required
                                                    />
                                                <small>Number behind the card</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-xs-12">
                                        <h4><b>Subtotal:</b> R$259,99</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <button class="subscribe btn btn-success btn-lg btn-block" type="button">Submit</button>
                                        <input type="hidden" name="token" id="token" value="<?php echo $_SESSION['id'];?>"/>
                                    </div>
                                </div>
                                <div class="row" style="display:none;">
                                    <div class="col-xs-12">
                                        <p class="payment-errors"></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>            
                    <!-- CREDIT CARD FORM ENDS HERE -->


                </div>            

                <div class="col-xs-12 col-md-8" style="font-size: 12pt; line-height: 2em;">
                    <p><h1>Wirecard Checkout:</h1>
                    <p>Built upon: 
                        <a href="https://docs.docker.com/">Docker</a>, 
						<a href="https://www.slimframework.com/" target="_blank">Slim Framework</a>,
						<a href="https://getbootstrap.com/" target="_blank">Bootstrap</a>, 
						<a href="https://jquery.com/" target="_blank">jQuery</a>, 
						<a href="http://robinherbots.github.io/jquery.inputmask/" target="_blank">Inputmask</a>, 
                        <a href="http://jqueryvalidation.org/" target="_blank">jQuery Validation Plugin</a>, 
                        <a href="https://github.com/stripe/jquery.payment" target="_blank">jQuery.payment library</a>.
                    </p>
                    <p>Result REST API:</p>
                    <textarea class="form-control" rows="14" style="max-height: 280px;resize: none;" id="rest-result"></textarea>
                    <label></label>
                </div>
            </div>
        </div>


        <script src="scripts/scripts.bundle.min.js?v=1.0"></script>
        <script type="text/javascript">
			GlobalToken = $('#token').val();
			
            $(function () {
				/* Brand Card */
				$('#cardNumber').focusout(function (){
					var el = $(this);
					if($.trim($("#cardNumber").val()).replace(/[^0-9]/g,"").length >= 16){
						$.ajax({
							type: 'POST',
							data: 'number=' + el.val() + '&token=' + GlobalToken,
							dataType: 'json',
							url: 'routes/rest.php/card-brand',
							success: function (data){
								$("#cardNumber").css("background","url(img/"+data.brand+".png) 98% 50% no-repeat rgb(255, 255, 255)");
							}
						});
					}else{
						$("#cardNumber").css("background","none");
					}
				});
				
				/* Mask CPF Buyer */
				$('#buyerCpf').inputmask("999.999.999-99",{placeholder: " ",clearMaskOnLostFocus: !0});
				
                create_token(); // Cria token do usuário

                var $form = $('#payment-form');
                $form.find('.subscribe').on('click', payWithStripe);

                $('#paymentType').on('change', function () {
                    if ($(this).val() == 'creditCard') {
                        $('#payment-content').show();
                    } else {
                        $('#payment-content').hide();
                    }
                });

                /* Submit form */
                function payWithStripe(e) {
                    e.preventDefault();

                    /* Abort if invalid form data */
                    if (!validator.form()) {
                        return;
                    }

                    /* Visual feedback */
                    $form.find('.subscribe').html('Validating <i class="fa fa-spinner fa-pulse"></i>').prop('disabled', true);
                    var data = $form.serialize();

                    var price = 259.99;
                    
                    $.ajax({
                        type: 'POST',
                        data: data + '&price=' + price,
                        dataType: 'json',
                        url: 'routes/rest.php/transaction',
                        success: function (data) {
                            $('#rest-result').append('Result:\n');
                            print_result_area(data);


                            $form.find('.subscribe').html('Submit').prop('disabled', false);
                            $('#payment-content').hide();
                            //$form[0].reset();

                            if (data.erro == false) {
                                $('#rest-result').append('Searching for customer purchase data... :\n');
                                info_client_pays(data.id);
                            }
                        }
                    });
                }

                /* Fancy restrictive input formatting via jQuery.payment library*/
                $('input[name=cardNumber]').payment('formatCardNumber');
                $('input[name=cardCVV]').payment('formatCardCVC');
                $('input[name=cardExpiry').payment('formatCardExpiry');

                $.validator.addMethod("cpf", function (value, element) {
                    value = jQuery.trim(value);

                    value = value.replace('.', '');
                    value = value.replace('.', '');
                    cpf = value.replace('-', '');
                    while (cpf.length < 11)
                        cpf = "0" + cpf;
                    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
                    var a = [];
                    var b = new Number;
                    var c = 11;
                    for (i = 0; i < 11; i++) {
                        a[i] = cpf.charAt(i);
                        if (i < 9)
                            b += (a[i] * --c);
                    }
                    if ((x = b % 11) < 2) {
                        a[9] = 0
                    } else {
                        a[9] = 11 - x
                    }
                    b = 0;
                    c = 11;
                    for (y = 0; y < 10; y++)
                        b += (a[y] * c--);
                    if ((x = b % 11) < 2) {
                        a[10] = 0;
                    } else {
                        a[10] = 11 - x;
                    }

                    var retorno = true;
                    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
                        retorno = false;

                    return this.optional(element) || retorno;

                }, "Invalid CPF.");

                /* Form validation using Stripe client-side validation helpers */
                jQuery.validator.addMethod("cardNumber", function (value, element) {
                    return this.optional(element) || Stripe.card.validateCardNumber(value);
                }, "Please specify a valid credit card number.");

                jQuery.validator.addMethod("cardExpiry", function (value, element) {
                    /* Parsing month/year uses jQuery.payment library */
                    value = $.payment.cardExpiryVal(value);
                    return this.optional(element) || Stripe.card.validateExpiry(value.month, value.year);
                }, "Invalid expiration date.");

                jQuery.validator.addMethod("cardCVV", function (value, element) {
                    return this.optional(element) || Stripe.card.validateCVC(value);
                }, "Invalid CVV.");

                jQuery.validator.addMethod("fullname", function (value, element) {
                    var retorno = false;
                    if (/\w+\s+\w+/.test($.trim(value)))
                        retorno = true;

                    return this.optional(element) || retorno;

                }, "Invalid name.");

                validator = $form.validate({
                    rules: {
                        paymentType: {
                            required: true
                        },
                        buyerName: {
                            required: true,
                            fullname: true
                        },
                        buyerCpf: {
                            required: true,
                            cpf: true
                        },
                        buyerEmail: {
                            required: true
                        },
                        cardName: {
                            required: true,
                            fullname: true
                        },
                        cardNumber: {
                            required: true,
                            cardNumber: true
                        },
                        cardExpiry: {
                            required: true,
                            cardExpiry: true
                        },
                        cardCVV: {
                            required: true,
                            cardCVV: true
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-control').removeClass('success').addClass('error');
                    },
                    unhighlight: function (element) {
                        $(element).closest('.form-control').removeClass('error').addClass('success');
                    },
                    errorPlacement: function (error, element) {
                        $(element).closest('.form-group').append(error);
                    }
                });

                paymentFormReady = function () {
                    if ($('#paymentType').val() == 0) {
                        return true;
                    } else {
                        if ($form.find('[name=cardNumber]').hasClass("success") &&
                                $form.find('[name=cardExpiry]').hasClass("success") &&
                                $form.find('[name=cardCVV]').val().length > 1) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }


                var readyInterval = setInterval(function () {
                    if (paymentFormReady()) {
                        $form.find('.subscribe').prop('disabled', false);
                        clearInterval(readyInterval);
                    }
                }, 250);
            });
	
			/* Create token user */
            function create_token() {
                $.ajax({
                    type: 'POST',
                    data: 'token=' + GlobalToken,
                    url: 'routes/rest.php/auth',
                    dataType: 'json',
                    success: function (data) {
                        $('#rest-result').append('Token:\n');
                        print_result_area(data);
                        $('#token_user').val(data.token);
						
						console.log(data);
                    }
                });
            }
	
			/* Notify customer payment
			@param int id => ID payment */
            function info_client_pays(id) {
                $.ajax({
                    type: 'POST',
                    data: 'id=' + id + '&token=' + GlobalToken,
                    dataType: 'json',
                    url: 'routes/rest.php/payment-order',
                    success: function (data) {
                        print_result_area(data);
                    }
                });
            }
	
			/* Print result API REST
			@param json data
			*/
            function print_result_area(data) {
                $('#rest-result').append(JSON.stringify(data, undefined, 4) + '\n\n');
            }
        </script>
    </body>
</html>