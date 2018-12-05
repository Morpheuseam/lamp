<?php

class Api extends Connect {

    public function __construct() {
        
    }

    /* Instance token client
     * @param text $token => Session ID User
     */

    public function instance_token($token) {
        $data = array($token);
        $sql = "SELECT api.* FROM api WHERE api.token = ?";
        $result = parent::selectDB($sql, $data);

        if (empty($result)) {
            $result = $this->insert_token($token);
            return $token;
        } else {
            return $result[0]->token;
        }
    }

    /* New token client
     * @param text $token => Session ID User
     */

    private function insert_token($token) {
        $data = array($token);
        $sql = "INSERT api (token) VALUES (?)";
        return parent::insertDB($sql, $data);
    }

    /* Validate token client
     * @param text $token => Session ID User
     */

    public function valid_token($token) {
        $data = array($token);
        $sql = "SELECT api.* FROM api WHERE api.token = ?";
        return parent::selectDB($sql, $data);
    }

    /* Start transaction client
     * @param int $client_id => ID Client
     * @param varchar $name => Name Client
     * @param varchar $email => Email Client
     * @param varchar $cpf => CPF Client
     * @param varchar $type => Payment type
     * @param text $card_json => JSON Card informations
     */

    private function new_transaction($client_id, $name, $email, $cpf, $type, $price, $buyerId, $cardName, $cardNumber, $cardExpiry, $cardCVV) {

        $data = array($client_id, $name, $email, $cpf);
        $sql = "INSERT buyer (id_client, name, email, cpf) VALUES (?,?,?,?)";
        $buyer = parent::insertDB($sql, $data);

        if ($type == 'boleto') {
            $boleto_number = md5(date('Y-m-d') . mt_rand(15, 50));
            $data = array($boleto_number);
            $sql = "INSERT boleto (number) VALUES (?)";
            $boleto = parent::insertDB($sql, $data);

            $data = array($buyer, $price, $type, $boleto);
            $sql = "INSERT payment (id_buyer, amount, type, boleto_id) VALUES (?,?,?,?)";
            $payment = parent::insertDB($sql, $data);

            if ($payment > 0) {
                $array_response = ['error' => false, 'response' => $boleto_number, 'id' => $payment];
                return $array_response;
            }
        } else {
            $data = array($cardName, $cardNumber, $this->validateBrand($cardNumber), $cardExpiry, $cardCVV);
            $sql = "INSERT card (card_holder_name, card_number, card_brand, card_exp, card_cvv) VALUES (?,?,?,?,?)";
            $card = parent::insertDB($sql, $data);

            $data = array($buyer, $price, $type, $card);
            $sql = "INSERT payment (id_buyer, amount, type, card_id) VALUES (?,?,?,?)";
            $payment = parent::insertDB($sql, $data);

            if ($payment > 0) {
                $array_response = ['error' => false, 'response' => 'OK', 'id' => $payment];
                return $array_response;
            }
        }
    }

    /* Validate transaction client
     * @param varchar $name => Name Client
     * @param varchar $email => Email Client
     * @param varchar $cpf => CPF Client
     * @param boolean $type => Payment type
     * @param json $card_json => JSON Card informations
     */

    public function valid_client_transaction($name, $email, $cpf, $type, $price, $cardName = null, $cardNumber = null, $cardExpiry = null, $cardCVV = null) {

        $data = array($cpf);
        $sql = "SELECT client.id, buyer.cpf, buyer.id AS buyerId FROM client LEFT JOIN buyer ON client.id = buyer.id_client WHERE buyer.cpf = ?";
        $response = parent::selectDB($sql, $data);

        $buyerId = false;
        if (!empty($response)) {
            foreach ($response as $buyer) {
                
            }
            $response = $buyer->id;
            $buyerId = $buyer->buyerId;
        } else {
            $response = $this->new_client();
        }

        $trans = $this->new_transaction($response, $name, $email, $cpf, $type, $price, $buyerId, $cardName, $cardNumber, $cardExpiry, $cardCVV);
        return $trans;
    }

    /* New Client */

    private function new_client() {
        $sql = "INSERT INTO `client` (`id`, `create_date`) VALUES (NULL, CURRENT_TIMESTAMP);";
        return parent::insertDB($sql);
    }

    /*
     * Search info pay client 
     * @param int $id => ID Client
     */

    public function infoClientPay($id) {
        $data = array($id);
        $sql = "SELECT 
                (SELECT COUNT(payment.id) FROM payment WHERE payment.id_buyer = buyer.id) AS cont,
                client.id, buyer.name, buyer.email, buyer.cpf, payment.id AS payId, payment.amount, payment.type, payment.status,
                payment.status, boleto.number, card.card_holder_name, card.card_number, card.card_brand, card.card_exp, card.card_cvv
                FROM client
                LEFT JOIN buyer ON client.id = buyer.id_client
                LEFT JOIN payment ON buyer.id = payment.id_buyer
                LEFT JOIN boleto ON boleto.id = payment.boleto_id
                LEFT JOIN card ON card.id = payment.card_id
                WHERE client.id = ?";
        return parent::selectDB($sql, $data);
    }
    
    /* Search info pay client 
        @param int $id => ID Payment Order
    */
    public function infoPayOrder($id) {
        $data = array($id);
        $sql = "SELECT 
                (SELECT COUNT(payment.id) FROM payment WHERE payment.id_buyer = buyer.id) AS cont,
                client.id, buyer.name, buyer.email, buyer.cpf, payment.id AS payId, payment.amount, payment.type, payment.status,
                payment.status, boleto.number, card.card_holder_name, card.card_number, card.card_brand, card.card_exp, card.card_cvv
                FROM client
                LEFT JOIN buyer ON client.id = buyer.id_client
                LEFT JOIN payment ON buyer.id = payment.id_buyer
                LEFT JOIN boleto ON boleto.id = payment.boleto_id
                LEFT JOIN card ON card.id = payment.card_id
                WHERE payment.id = ?";
        return parent::selectDB($sql, $data);
    }
    
     /* Detect card brand
     @param int $number => Number credit card
     @param boolean $string [optional] => return int/text value brand
    */
    public function validateBrand($number, $string = null) {
        if (empty($number)) {
            return false;
        }

        $number = trim($number);

        if ($string == true) {
            $matchingPatterns = [
                'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
                'master card' => '/^5[1-5][0-9]{14}$/',
                'american express' => '/^3[47][0-9]{13}$/',
                'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
                'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
                'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
	        'amex'       => '/^3[47]\d{13}$/',
	        'aura'       => '/^(5078\d{2})(\d{2})(\d{11})$/',
	        'hipercard'  => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
	        'elo'    => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
		'other' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/',
            ];
        } else {
            $matchingPatterns = [
                '1' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
                '0' => '/^5[1-5][0-9]{14}$/',
                '2' => '/^3[47][0-9]{13}$/',
                '3' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
                '4' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
                '5' => '/^(?:2131|1800|35\d{3})\d{11}$/',
	        '6'       => '/^3[47]\d{13}$/',
	        '7'       => '/^(5078\d{2})(\d{2})(\d{11})$/',
	        '8'  => '/^(606282\d{10}(\d{3})?)|(3841\d{15})$/',
	        '9'    => '/^(?:5[0678]\d\d|6304|6390|67\d\d)\d{8,15}$/',
                '10' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/'
            ];
        }


        $ctr = 1;
        foreach ($matchingPatterns as $key => $pattern) {
            if (preg_match($pattern, $number)) {
                return $key;
            }
            $ctr++;
        }
    }
    
    /* Validate CPF
       @param varchar $cpf => CPF number
    */
    public function validateCPF($cpf = null) {

        // Checks if a number has been reported
        if (empty($cpf)) {
            return false;
        }

        // Remove possible mask
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Checks if the number of digits entered is equal to 11
        if (strlen($cpf) != 11) {
            return false;
        }
        else if ($cpf == '00000000000' ||
                $cpf == '11111111111' ||
                $cpf == '22222222222' ||
                $cpf == '33333333333' ||
                $cpf == '44444444444' ||
                $cpf == '55555555555' ||
                $cpf == '66666666666' ||
                $cpf == '77777777777' ||
                $cpf == '88888888888' ||
                $cpf == '99999999999') {
            return false;
            // Calculate the verification digits to verify that the CPF is valid
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }
            return true;
        }
    }

}
