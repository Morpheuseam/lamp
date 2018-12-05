<?php
session_start();
$_SESSION['id'] = base64_encode(session_id()); // Create token for test
?>
<!DOCTYPE html>
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
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="scripts/style.min.css" />
    </head>
    <body>
        <!-- begin::container -->
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <!-- begin::payment details -->
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
                                        <input type="hidden" name="token" id="token" value="<?php echo $_SESSION['id']; ?>"/>
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
                    <!-- end::payment details -->
                </div>

                <!-- begin::Result Checkout API REST -->
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
                </div>
            </div>
        </div>
        <!-- end::container -->

        <!-- begin::scripts -->
        <script src="scripts/scripts.bundle.min.js?v=1.0"></script>
    </body>
</html>
