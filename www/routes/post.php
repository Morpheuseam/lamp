<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once "../model/Connect.php";
require_once "../model/Api.php";

$app->post('/auth', function (Request $request, Response $response) {
    $token = $request->getParam('token');
    if ($token == '' || $token == null || empty($token)) {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'token is required'
                ], 401, JSON_UNESCAPED_UNICODE);
        return $response;
    }

    $api = new Api();
    $result = $api->instance_token($token);

    $response = $response->withJson([
        "erro" => false,
        "token" => $token
            ], 200, JSON_UNESCAPED_UNICODE);

    return $response;
});

$app->post('/transaction', function (Request $request, Response $response) {
    $token = trim(strip_tags($request->getParam('token')));
    
    $api = new Api();
    /* BASIC VALIDATE */
    $msg = []; // msg validate
    if ($token == '' || $token == null || empty($token))
        $msg[] = 'token is required';

    $name = ltrim(rtrim(strip_tags($request->getParam('buyerName'))));
    if ($name == '' || $name == null || empty($name))
        $msg[] = 'full name is required';
    else{
        if(!strstr($name, ' ')){
            $msg[] = 'invalid full name';
        }
    }

    $price = $request->getParam('price');
    if (!is_numeric(trim($price)))
        $msg[] = 'price must be in decimal format';
    else {
        if ($price == '' || $price == null || empty($price))
            $msg[] = 'price is required';
    }

    $cpf = preg_replace("/[^0-9]/", "", trim(strip_tags($request->getParam('buyerCpf'))));
    if ($cpf == '' || $cpf == null || empty($cpf))
        $msg[] = 'cpf is required';
    else{
        if(!$api->validateCPF($cpf)){
            $msg[] = 'invalid cpf format';
        }
    }

    $type = $request->getParam('paymentType');
    if ($type == '' || $type == null || empty($type))
        $msg[] = 'type payment is required';
    else {
        if ($type != 'boleto' && $type != 'creditCard')
            $msg[] = 'invalid payment type';
    }

    $email = trim(strip_tags($request->getParam('buyerEmail')));
    if ($email == '' || $email == null || empty($email))
        $msg[] = 'email is required';

    /* credit card erros validate */
    if ($type == 'creditCard') {
        $cardName = ltrim(rtrim(strip_tags($request->getParam('cardName'))));
        if ($cardName == '' || $cardName == null || empty($cardName))
            $msg[] = 'card holder name is required';

        $cardNumber = preg_replace('/\s+/', '', trim(strip_tags($request->getParam('cardNumber'))));
        if ($cardNumber == '' || $cardNumber == null || empty($cardNumber))
            $msg[] = 'card number is required';

        $cardExpiry = preg_replace('/\s+/', '', trim(strip_tags($request->getParam('cardExpiry'))));
        if ($cardExpiry == '' || $cardExpiry == null || empty($cardExpiry))
            $msg[] = 'card expiration is required';

        $cardCVV = trim(strip_tags($request->getParam('cardCVV')));
        if ($cardCVV == '' || $cardCVV == null || empty($cardCVV))
            $msg[] = 'cvv is required';

        require_once '../model/CCValidator.php';

        $ccv = new CCValidator($cardName, $cardNumber, (int) substr($cardExpiry, 0, 2), (int) substr($cardExpiry, 3, 4));

        if ($validCard = $ccv->validate()) {

            if ($validCard & CCV_RES_ERR_HOLDER)
                $msg[] = 'card holder name is missing or incorrect';

            if ($validCard & CCV_RES_ERR_TYPE)
                $msg[] = 'incorrect credit card type';

            if ($validCard & CCV_RES_ERR_DATE)
                $msg[] = 'Incorrect expiration date';

            if ($validCard & CCV_RES_ERR_FORMAT)
                $msg[] = 'incorrect credit card number format';

            if ($validCard & CCV_RES_ERR_NUMBER)
                $msg[] = 'invalid credit card number';
        }
    }

    if (!empty($msg)) { // error messages
        $string_erro = '';
        for ($i = 0; $i < count($msg); ++$i)
            $string_erro .= $msg[$i] . ', ';

        $string_erro = rtrim($string_erro, ', ') . '.';

        $response = $response->withJson([
            "erro" => true,
            "msg" => $string_erro
                ], 401, JSON_UNESCAPED_UNICODE);
    } else {
        
        if ($type == 'creditCard')
            $result = $api->valid_client_transaction($name, $email, $cpf, $type, $price, $cardName, $cardNumber, $cardExpiry, $cardCVV); // validate transaction client
        else
            $result = $api->valid_client_transaction($name, $email, $cpf, $type, $price); // validate transaction client
            
        if (!empty($result)) {
// Success
            if ($type == 'boleto') { // boleto response
                $response = $response->withJson([
                    "erro" => $result['error'],
                    "token" => $token,
                    "id" => $result['id'],
                    "payment" => [
                        'type' => 'boleto',
                        'result' => $result['response']
                    ]], 200, JSON_PRETTY_PRINT);
            } else { // credit card response
                $response = $response->withJson([
                    "erro" => $result['error'],
                    "token" => $token,
                    "id" => $result['id'],
                    "payment" => [
                        'type' => 'credit card',
                        'result' => $result['response']
                    ]], 200, JSON_PRETTY_PRINT);
            }
        } else {
            $response = $response->withJson([
                "erro" => true,
                "msg" => 'Error'
                    ], 200, JSON_UNESCAPED_UNICODE);
        }
    }

    return $response;
})->add($mid01);

$app->post('/notification', function (Request $request, Response $response) {
    $token = $request->getParam('token');
    $id = $request->getParam('id');

    if ($id == '' || $id == null || empty($id)) {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'id customer is required'
                ], 401, JSON_UNESCAPED_UNICODE);
        return $response;
    }

    $a = new Api();
    $result = $a->infoClientPay($id);
    if (!empty($result)) {
        $data[] = ['erro' => false, 'count' => $result[0]->cont];
        foreach ($result as $pay) {

            $pay->status == 0 ? $status = 'Waiting payment' : $status = 'Approved';

            if ($pay->type == 'boleto')
                $data[] = ['id' => $pay->id, 'buyer' => ['name' => $pay->name, 'email' => $pay->email, 'cpf' => $pay->cpf, 'payment' => ['paymentID' => $pay->payId, 'amount' => $pay->amount, 'status' => ['label' => $status, 'value' => $pay->status], 'type' => ['label' => 'boleto', 'number' => $pay->number]]]];
            else // Credit card
                $data[] = ['id' => $pay->id, 'buyer' => ['name' => $pay->name, 'email' => $pay->email, 'cpf' => $pay->cpf, 'payment' => ['paymentID' => $pay->payId, 'amount' => $pay->amount, 'status' => ['label' => $status, 'value' => $pay->status], 'type' => ['label' => 'credit card', 'cardName' => $pay->card_holder_name, 'cardNumer' => $pay->card_number, 'cardBrand' => $a->validateBrand($pay->card_number, true), 'cardCVV' => $pay->card_cvv, 'result' => 'OK']]]];
        }

        $response = $response->withJson($data, 200, JSON_UNESCAPED_UNICODE);
    }else {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'No information for the customer'
                ], 401, JSON_UNESCAPED_UNICODE);
    }

    return $response;
})->add($mid01);


$app->post('/card-brand', function (Request $request, Response $response) {
	$api = new Api();
	$number = preg_replace('/\s+/', '', trim(strip_tags($request->getParam('number'))));

    if ($number == '' || $number == null || empty($number)) {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'invalid credit card number '
                ], 401, JSON_UNESCAPED_UNICODE);
        return $response;
    }
	
	
	$response = $response->withJson([
            "erro" => false,
            "brand" => preg_replace("/[^A-Za-z]/", "", $api->validateBrand($number, true))
                ], 200, JSON_UNESCAPED_UNICODE);
	
	return $response;
})->add($mid01);
	
$app->post('/payment-order', function (Request $request, Response $response) {
    $token = $request->getParam('token');
    $id = $request->getParam('id');

    if ($id == '' || $id == null || empty($id)) {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'id payment is required'
                ], 401, JSON_UNESCAPED_UNICODE);
        return $response;
    }
    
    $a = new Api();
    $result = $a->infoPayOrder($id);
    if (!empty($result)) {
        $data[] = ['erro' => false];
        foreach ($result as $pay) {
            $pay->status == 0 ? $status = 'Waiting payment' : $status = 'Approved';

            if ($pay->type == 'boleto')
                $data[] = ['id' => $pay->id, 'buyer' => ['name' => $pay->name, 'email' => $pay->email, 'cpf' => $pay->cpf, 'payment' => ['paymentID' => $pay->payId, 'amount' => $pay->amount, 'status' => ['label' => $status, 'value' => $pay->status], 'type' => ['label' => 'boleto', 'number' => $pay->number]]]];
            else // Credit card
                $data[] = ['id' => $pay->id, 'buyer' => ['name' => $pay->name, 'email' => $pay->email, 'cpf' => $pay->cpf, 'payment' => ['paymentID' => $pay->payId, 'amount' => $pay->amount, 'status' => ['label' => $status, 'value' => $pay->status], 'type' => ['label' => 'credit card', 'cardName' => $pay->card_holder_name, 'cardNumer' => $pay->card_number, 'cardBrand' => $a->validateBrand($pay->card_number, true), 'cardCVV' => $pay->card_cvv, 'result' => 'OK']]]];
        }

        $response = $response->withJson($data, 200, JSON_UNESCAPED_UNICODE);
    }else {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'No information for the customer'
                ], 401, JSON_UNESCAPED_UNICODE);
    }

    return $response;
})->add($mid01);
