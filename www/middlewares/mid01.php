<?php
/*
Basic Auth
 *  */

$mid01 = function ($request, $response, $next) {
    
    require_once "../model/Connect.php";
    require_once "../model/Api.php";
    
    $token = trim(strip_tags($request->getParam('token')));
    
    if ($token == '' || $token == null || empty($token)) {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'token is required'
                ], 401, JSON_UNESCAPED_UNICODE);
        return $response;
    }
    
    
    $a = new Api();
    $result = $a->valid_token($token);
    
    if (empty($result)) {
        $response = $response->withJson([
            "erro" => true,
            "msg" => 'Unauthorized'
                ], 401, JSON_UNESCAPED_UNICODE);
    } else {
        $response = $next($request, $response);
    }

    return $response;
};