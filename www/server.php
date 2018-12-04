<?php

session_start();

if ($_POST) {
    if (isset($_POST['type']) && isset($_SESSION['id'])) {

        $data['token'] = $_SESSION['id'];
        switch ($_POST['type']) {
            case 'createSessionUser': // Create Session Usernotification
                $url = 'http://localhost:8001/routes/rest.php/auth';
                break;
            case 'transaction': // Start transition
                $url = 'http://localhost:8001/routes/rest.php/transaction';
                $curl = curl_init($url);
                $data['buyerName'] = $_POST['buyerName'];
                $data['buyerCpf'] = $_POST['buyerCpf'];
                $data['buyerEmail'] = $_POST['buyerEmail'];
                $data['paymentType'] = $_POST['paymentType'];
                $data['cardName'] = $_POST['cardName'];
                $data['cardNumber'] = $_POST['cardNumber'];
                $data['cardExpiry'] = $_POST['cardExpiry'];
                $data['cardCVV'] = $_POST['cardCVV'];
                $data['price'] = $_POST['price'];
                break;
            case 'infoClientPay':
                $url = 'http://localhost:8001/routes/rest.php/notification';
                $data['id'] = $_POST['id'];
                break;
            case 'infoPayOrder':
                $url = 'http://localhost:8001/routes/rest.php/payment-order';
                $data['id'] = $_POST['id'];
                break;
        }
	
        $curl = curl_init($url);
        $buildQuery = http_build_query($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded; charset=UTF-8"));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $buildQuery);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        echo $curl_response;
    }
}