<?php

/**
 * Credit card number validation class file
 *
 * This file contains the credit card number validation class.
 *
 * @package  ADirectRide
 * @access   public
 * @author   Vagharshak Tozalakyan <vagh@tozalakyan.com>
 */
/**
 * MasterCard type
 */
define('CCV_MASTER_CARD', 0);

/**
 * VISA card type
 */
define('CCV_VISA', 1);

/**
 * American Express card type
 */
define('CCV_AMERICAN_EXPRESS', 2);

/**
 * Diners Club card type
 */
define('CCV_DINERS_CLUB', 3);

/**
 * Discover card type
 */
define('CCV_DISCOVER', 4);

/**
 * JCB card type
 */
define('CCV_JCB', 5);


/**
 * Credit card information is valid
 */
define('CCV_RES_VALID', 0x00);

/**
 * Card holder's name is missing or incorrect
 */
define('CCV_RES_ERR_HOLDER', 0x01);

/**
 * Incorrect card type
 */
define('CCV_RES_ERR_TYPE', 0x02);

/**
 * Incorrect expiration date
 */
define('CCV_RES_ERR_DATE', 0x04);

/**
 * Incorrect card number format
 */
define('CCV_RES_ERR_FORMAT', 0x08);

/**
 * Invalid credit card number
 */
define('CCV_RES_ERR_NUMBER', 0x10);

/**
 * Credit card validation class
 *
 * This class implements Mod 10 algorithm to validate a credit card number. It
 * also checks if a credit card number prefix and an exparation date are correct.
 *
 * Example of usage:
 *
 * <code>
 * $ccv = new CCValidator(
 *     'JOHN JOHNSON', CCV_AMERICAN_EXPRESS, '378282246310005', 3, 2007);
 * if ($validCard = $ccv->validate()) {
 *     if ($validCard & CCV_RES_ERR_HOLDER) {
 *         echo 'Card holder\'s name is missing or incorrect.<br />';
 *     }
 *     if ($validCard & CCV_RES_ERR_TYPE) {
 *         echo 'Incorrect credit card type.<br />';
 *     }
 *     if ($validCard & CCV_RES_ERR_DATE) {
 *         echo 'Incorrect expiration date.<br />';
 *     }
 *     if ($validCard & CCV_RES_ERR_FORMAT) {
 *         echo 'Incorrect credit card number format.<br />';
 *     }
 *     if ($validCard & CCV_RES_ERR_NUMBER) {
 *         echo 'Invalid credit card number.<br />';
 *     }
 * } else {
 *     echo 'Credit card information is valid.<br />';
 * }
 * </code>
 *
 * @see      http://www.beachnet.com/~hstiles/cardtype.html
 * @package  ADirectRide
 * @version  1.0
 * @access   public
 * @author   Vagharshak Tozalakyan <vagh@tozalakyan.com>
 */
class CCValidator {

    /**
     * Credit card holder's name
     *
     * Read only. Use constructor to set the value.
     *
     * @var     string
     * @access  public
     */
    var $cardHolder = '';

    /**
     * Credit card type
     *
     * CCV_MASTER_CARD - 0, CCV_VISA - 1, CCV_AMERICAN_EXPRESS - 2,
     * CCV_DINERS_CLUB - 3, CCV_DISCOVER - 4, CCV_JCB - 5 or -1 if no card.
     * Read only. Use constructor to set the value.
     *
     * @var     int
     * @access  public
     */
    var $cardType = -1;

    /**
     * Credit card number
     *
     * Read only. Use constructor to set the value.
     *
     * @var     string
     * @access  public
     */
    var $cardNumber = '';

    /**
     * Credit card expiration month
     *
     * 1-12 or 0 if not set. Read only. Use constructor to set the value.
     *
     * @var     int
     * @access  public
     */
    var $cardExpiredMonth = 0;

    /**
     * Credit card expiration year
     *
     * YYYY or 0 if not set. Read only. Use constructor to set the value.
     *
     * @var     int
     * @access  public
     */
    var $cardExpiredYear = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string  $cardHolder        Credit card holder's name.
     * @param   int     $cardType          Credit card type.
     *                                     CCV_MASTER_CARD - 0, CCV_VISA - 1,
     *                                     CCV_AMERICAN_EXPRESS - 2, CCV_DINERS_CLUB - 3,
     *                                     CCV_DISCOVER - 4, CCV_JCB - 5.
     * @param  string   $cardNumber        Credit card number
     * @param  int      $cardExpiredMonth  Credit card expiration month. 1-12.
     * @param  int      $cardExpiredYear   Credit card expiration year in YYYY format.
     */
    public function CCValidator($cardHolder, $cardNumber, $cardExpiredMonth, $cardExpiredYear) {
        $api = new Api();
        
        $this->cardHolder = trim($cardHolder);
        $this->cardType = $api->validateBrand($cardNumber);
        $this->cardNumber = $cardNumber;
        $this->cardExpiredMonth = $cardExpiredMonth;
        $this->cardExpiredYear = $cardExpiredYear;
    }

    /**
     * Validate credit card information
     *
     * @access  public
     * @return  int     Validation result as a bitwise combination of following numbers:
     *                  CCV_RES_VALID - credit card information is valid;
     *                  CCV_RES_ERR_HOLDER - card holder's name is missing or incorrect;
     *                  CCV_RES_ERR_TYPE - incorrect credit card type;
     *                  CCV_RES_ERR_DATE - incorrect expiration date;
     *                  CCV_RES_ERR_FORMAT - incorrect credit card number format;
     *                  CCV_RES_ERR_NUMBER - invalid credit card number.
     */
    public function validate() {
        $validCard = CCV_RES_VALID;
        if (empty($this->cardHolder)) {
            $validCard |= CCV_RES_ERR_HOLDER;
        }
        if (!is_integer($this->cardType) || $this->cardType < 0 || $this->cardType > 5) {
            $validCard |= CCV_RES_ERR_TYPE;
        }
        if (!is_integer($this->cardExpiredMonth) || $this->cardExpiredMonth < 1 || $this->cardExpiredMonth > 12) {
            $validCard |= CCV_RES_ERR_DATE;
        }
        $currentYear = intval(date('Y'));
        if (!is_integer($this->cardExpiredYear) || $this->cardExpiredYear < $currentYear) {
            $validCard |= CCV_RES_ERR_DATE;
        }
        $cardNumber = str_replace(' ', '', $this->cardNumber);
        if (!$this->checkFormat($cardNumber)) {
            $validCard |= CCV_RES_ERR_FORMAT;
        }
        if (!$this->mod10($cardNumber)) {
            $validCard |= CCV_RES_ERR_NUMBER;
        }
        return $validCard;
    }

    /**
     * Check a credit card number prefix
     *
     * @access  private
     * @param   string   carNumber
     * @return  bool
     */
    public function checkFormat($cardNumber) {
        $validFormat = false;
        switch ($this->cardType) {
            case CCV_MASTER_CARD:
                $validFormat = preg_match('/^5[1-5][0-9]{14}$/', $cardNumber);
                break;
            case CCV_VISA:
                $validFormat = preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $cardNumber);
                break;
            case CCV_AMERICAN_EXPRESS:
                $validFormat = preg_match('/^3[47][0-9]{13}$/', $cardNumber);
                break;
            case CCV_DINERS_CLUB:
                $validFormat = preg_match('/^3(0[0-5]|[68][0-9])[0-9]{11}$/', $cardNumber);
                break;
            case CCV_DISCOVER:
                $validFormat = preg_match('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cardNumber);
                break;
            case CCV_JCB:
                $validFormat = preg_match('/^(?:2131|1800|35\d{3})\d{11}$/', $cardNumber);
                break;
            default:
                $validFormat = false;
                break;
        }
        return $validFormat;
    }

    /**
     * Check credit card number by Mod 10 algorithm
     *
     * @access  private
     * @param   string   carNumber
     * @return  bool
     */
    public function mod10($cardNumber) {
        $cardNumber = strrev($cardNumber);
        $numSum = 0;
        for ($i = 0; $i < strlen($cardNumber); $i++) {
            $currentNum = substr($cardNumber, $i, 1);
            if ($i % 2 == 1) {
                $currentNum *= 2;
            }
            if ($currentNum > 9) {
                $firstNum = $currentNum % 10;
                $secondNum = ($currentNum - $firstNum) / 10;
                $currentNum = $firstNum + $secondNum;
            }
            $numSum += $currentNum;
        }
        return ($numSum % 10 == 0);
    }

}

?>