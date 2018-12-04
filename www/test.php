<?php
$data['token'] = 1234567899;
$url = 'http://localhost:8001/routes/rest.php/auth';
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