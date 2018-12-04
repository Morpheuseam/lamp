# How to run #

Dependencies:

  * Docker engine v1.13 or higher. Your OS provided package might be a little old, if you encounter problems, do upgrade. See [https://docs.docker.com/engine/installation](https://docs.docker.com/engine/installation)
  * Docker compose v1.12 or higher. See [docs.docker.com/compose/install](https://docs.docker.com/compose/install/)

Once you're done, simply `cd` to project and run `docker-compose up -d`. This will initialise and start all the containers, then leave them running in the background.

## Services exposed outside your environment ##

You can access application via **`http://localhost:8001`**.

Service|Address
------|---------
Apache|**host:** `localhost`; **port:** `8801`
MySQL 5.7|**host:** `localhost`; **port:** `8890`
PhpMyAdmin|**host:** `localhost`; **port:** `8000`

## Hosts within your environment ##

You'll need to configure application to use any services you enabled:

Service|Hostname|Port number
------|---------|-----------
php-fpm|php-fpm|9000

## Design and external plugins ##
Plugin|Version|Usage
------|---------|---------
Jquery|3.3.1|--
Slim Framework|3.0|API REST
Bootstrap|3.0|Stylesheet
jQuery.payment|1.7.1|Validate credit card
JQuery Validate|1.13.1|Validate form

## Checkout ##
You can access checkout via **`http://localhost:8001`**.

## API Endpoint ##
All calls are required to access token information for security reasons. You can create a sample token through the following URL. The call must be made to the URL below using the POST method.  
```
http://localhost:8001/routes/rest.php/auth?{credentials}
```

After obtaining the payment data, you must place the call to the transparent checkout service by sending the buyer and payment data to pay for it. The call must be made to the URL below using the POST method.  
```
http://localhost:8001/routes/rest.php/transaction?{credentials}
```
This URL is recorded as a payment purchase for the customer ID. The call must be made to the URL below using the POST method.  
```
http://localhost:8001/routes/rest.php/notification?{id-customer}
```
This URL is recorded as a payment purchase for the payment ID. The call must be made to the URL below using the POST method.  
```
http://localhost:8001/routes/rest.php/payment-orders{id-payment}
```

## Call sample for Boleto ##
token=bDAxMjkzMHV0ZzRxcHRpYzFuN2l1YWNrMzY=  
&buyerName=vitor f cazelatto  
&buyerCpf=38817707805  
&buyerEmail=vitorcazelatto@gmail.com  
&paymentType=boleto  
&price=259.99  

## Call sample for Credit Card ##
token=bDAxMjkzMHV0ZzRxcHRpYzFuN2l1YWNrMzY=  
&buyerName=vitor f cazelatto  
&buyerCpf=38817707805  
&buyerEmail=vitorcazelatto@gmail.com  
&paymentType=creditCard  
&price=259.99  
&cardName=VITOR  F CAZELATTO  
&cardNumber=4111111111111111  
&cardExpiry=12/2030  
&cardCVV=12
