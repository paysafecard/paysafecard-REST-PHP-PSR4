<?php

namespace paysafecard\paysafecardSDK;

class Payments extends Base
{
    protected $path = 'payments';

    /**
     * create a payment
     * @param double $amount
     * @param string $currency
     * @param string $customer_id
     * @param string $customer_ip
     * @param string $success_url
     * @param string $failure_url
     * @param string $notification_url
     * @param string|double $correlation_id
     * @param string $country_restriction
     * @param string $kyc_restriction
     * @param int|string $min_age
     * @param int|string $shop_id
     * @param string $submerchant_id
     * @return array|bool
     */
    public function createPayment($amount, $currency, $customer_id, $customer_ip, $success_url, $failure_url, $notification_url, $correlation_id = "", $country_restriction = "", $kyc_restriction = "", $min_age = "", $shop_id = "", $submerchant_id = "")
    {
        $amount = str_replace(',', '.', $amount);

        $customer = array(
            "id" => $customer_id,
            "ip" => $customer_ip,
        );
        if ($country_restriction != "") {
            array_push($customer,
                "country_restriction", $country_restriction
            );
        }

        if ($kyc_restriction != "") {
            array_push($customer,
                "kyc_level", $kyc_restriction
            );
        }

        if ($min_age != "") {
            array_push($customer,
                "min_age", $min_age
            );
        }

        $jsonarray = array(
            "currency" => $currency,
            "amount" => $amount,
            "customer" => $customer,
            "redirect" => array(
                "success_url" => $success_url,
                "failure_url" => $failure_url,
            ),
            "type" => "PAYSAFECARD",
            "notification_url" => $notification_url,
            "shop_id" => $shop_id,
        );

        if ($submerchant_id != "") {
            array_push($jsonarray,
                "submerchant_id", $submerchant_id
            );
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
     * get the payment id
     * @param string $payment_id
     * @return array|bool
     */
    public function capturePayment($payment_id)
    {
        $this->url = $this->url . $payment_id . "/capture";
        $jsonarray = array(
            'id' => $payment_id,
        );
        $this->doRequest($jsonarray, "POST");
        if ($this->requestIsOk() == true) {
            return $this->response;
        } else {
            return false;
        }
    }

    /**
     * retrieve a payment
     * @param string $payment_id
     * @return array|bool
     */
    public function retrievePayment($payment_id)
    {
        $this->url = $this->url . $payment_id;
        $jsonarray = array();
        $this->doRequest($jsonarray, "GET");
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
                    $this->response["message"] = 'Transaction could not be initiated due to connection problems. The IP from the server is not whitelisted! Server IP:' . $_SERVER["SERVER_ADDR"];
                    break;
                case 500:
                    $this->response["number"] = "HTTP:500";
                    $this->response["message"] = 'Server error. Please check logs.';
                    break;
            }
        }
        switch ($this->response["number"]) {
            case 4003:
                $this->response["message"] = 'The amount for this transaction exceeds the maximum amount. The maximum amount is 1000 EURO (equivalent in other currencies)';
                break;
            case 3001:
                $this->response["message"] = 'Transaction could not be initiated due to connection problems. If the problem persists, please contact our support.';
                break;
            case 2002:
                $this->response["message"] = 'payment id is unknown.';
                break;
            case 2010:
                $this->response["message"] = 'Currency is not supported.';
                break;
            case 2029:
                $this->response["message"] = 'Amount is not valid. Valid amount has to be above 0.';
                break;
            default:
                $this->response["message"] = 'Transaction could not be initiated due to connection problems. If the problem persists, please contact our support. ';
                break;
        }
        return $this->response;
    }
}