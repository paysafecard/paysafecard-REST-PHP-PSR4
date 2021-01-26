<?php

namespace paysafecard\paysafecardSDK;

class Payouts extends Base
{
    protected $path = 'payouts';

    /**
     * get payout details of payment
     * @param string $payment
     * @return array|bool
     */

    public function getPayoutDetail($payment = "")
    {
        $this->doRequest($payment, "GET");
        if ($this->requestIsOk() == true) {
            return $this->response;
        } else {
            return false;
        }
    }

    /**
     * get limits for a certain currency
     * @param string $currency
     * @return array|bool
     */

    public function getLimits($currency = "")
    {
        $this->url = $this->url . "limits/" . $currency;
        $this->doRequest($currency, "GET");
        if ($this->requestIsOk() == true) {
            return $this->response;
        } else {
            return false;
        }
    }

    /**
     * validate a payout
     * @param double $amount
     * @param string $currency
     * @param string $merchantclientid
     * @param string $customer_mail
     * @param string $customer_ip
     * @param string $first_name
     * @param string $last_name
     * @param string $birthday
     * @param string $correlation_id
     * @return array|bool
     */
    public function validatePayout($amount, $currency, $merchantclientid, $customer_mail, $customer_ip, $first_name, $last_name, $birthday, $correlation_id = "", $submerchant_id = "")
    {
        $amount = str_replace(',', '.', $amount);
        $jsonarray = array(
            "amount" => $amount,
            "currency" => $currency,
            "type" => "PAYSAFECARD",
            "customer" => array(
                "id" => $merchantclientid,
                "email" => $customer_mail,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "date_of_birth" => $birthday,
                "ip" => $customer_ip,
            ),
            "capture" => "false",
        );

        if ($submerchant_id != "") {
            array_push($jsonarray, [
                "submerchant_id" => $submerchant_id,
            ]);
        }

        if ($correlation_id != "") {
            $headers = ["Correlation-ID: " . $correlation_id];
        } else {
            $headers = [];
        }

        $this->doRequest($jsonarray, "POST", $headers);
        if ($this->requestIsOk() == true) {
            return $this->response;
        } else {
            return false;
        }
    }

    /**
     * execute a payout
     * @param string $payout_id
     * @param double $amount
     * @param string $currency
     * @param string $merchantclientid
     * @param string $customer_mail
     * @param string $customer_ip
     * @param string $first_name
     * @param string $last_name
     * @param string $birthday
     * @param string $correlation_id
     * @return array|bool
     */

    public function executePayout($payout_id, $amount, $currency, $merchantclientid, $customer_mail, $customer_ip, $first_name, $last_name, $birthday, $correlation_id = "", $submerchant_id = "")
    {
        $amount = str_replace(',', '.', $amount);
        $jsonarray = array(
            "amount" => $amount,
            "currency" => $currency,
            "type" => "PAYSAFECARD",
            "customer" => array(
                "id" => $merchantclientid,
                "email" => $customer_mail,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "date_of_birth" => $birthday,
                "ip" => $customer_ip,
            ),
            "capture" => "true",
        );

        if ($submerchant_id != "") {
            array_push($jsonarray, [
                "submerchant_id" => $submerchant_id,
            ]);
        }

        if ($correlation_id != "") {
            $headers = ["correlation_ID: " . $correlation_id];
        } else {
            $headers = [];
        }

        $this->url = $this->url . $payout_id . "/capture";
        $this->doRequest($jsonarray, "POST", $headers);
        if ($this->requestIsOk() == true) {
            return $this->response;
        } else {
            return false;
        }
    }

    /**
     * get error
     * @return array
     */
    public function getError()
    {
        if (!isset($this->response["number"])) {
            switch ($this->curl["info"]['http_code']) {
                case 400:
                    $this->response["number"] = "HTTP:400";
                    $this->response["message"] = 'Logical error. Please check logs.';
                    break;
                case 403:
                    $this->response["number"] = "HTTP:403";
                    $this->response["message"] = 'IP not whitelisted! Your IP:' . $_SERVER["SERVER_ADDR"];
                    break;
                case 500:
                    $this->response["number"] = "HTTP:500";
                    $this->response["message"] = 'Server error. Please check logs.';
                    break;
            }
        }
        switch ($this->response["number"]) {
            case 3162:
                $this->response["message"] = 'Unfortunately, no my paysafecard account exists under the e-mail address you have entered. Please check the address for a typing error. If you do not have a my paysafecard account, you can register for one online now for free.';
                break;
            case 3195:
                $this->response["message"] = 'The personal details associated with your my paysafecard account do not match the details of this account. Please check the first names, surnames and dates of birth entered in both accounts and request the payout again.';
                break;
            case 3167:
            case 3170:
            case 3194:
            case 3168:
            case 3230:
            case 3231:
            case 3232:
            case 3233:
            case 3234:
                $this->response["message"] = 'Unfortunately, the payout could not be completed due to a problem which has arisen with your my paysafecard account. paysafecard has already sent you an e-mail with further information on this. Please follow the instructions found in this e-mail before requesting the payout again.';
                break;
            case 3197:
            case 3198:
                $this->response["message"] = 'Unfortunately, the payout could not be completed due to a problem which has arisen with your my paysafecard account. Please contact the paysafecard support team. info@paysafecard.com';
                break;
            case 10008:
                $this->response["message"] = 'Invalid API Key';
                break;
            default:
                $this->response["message"] = 'Unfortunately there has been a technical problem and your payout request could not be executed. If the problem persists, please contact our customer support: support@company.com';
                break;
        }
        return $this->response;
    }
}