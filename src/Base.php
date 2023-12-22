<?php

namespace paysafecard\paysafecardSDK;

abstract class Base
{
    protected $path = null;

    protected $response;
    protected $request = array();
    protected $curl;
    protected $key = "";
    protected $url = "";
    protected $environment = 'TEST';

    public function __construct($key = "", $environment = "TEST")
    {
        $this->key = $key;
        $this->environment = $environment;
        $this->setEnvironment();
    }

    /**
     * set environment
     * @return bool
     */
    protected function setEnvironment()
    {
        if ($this->environment == "TEST") {
            $this->url = "https://apitest.paysafecard.com/v1/" . $this->path . "/";
        } else if ($this->environment == "PRODUCTION") {
            $this->url = "https://api.paysafecard.com/v1/" . $this->path . "/";
        } else {
            echo "Environment not supported";
            return false;
        }
        return true;
    }

    /**
     * check request status
     * @return bool
     */
    public function requestIsOk()
    {
        if (($this->curl["error_nr"] == 0) && ($this->curl["http_status"] < 300)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the request
     * @return array
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * get curl
     * @return array
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * get the response
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * send curl request
     * @param array|string $curlparam
     * @param string $method
     * @param array $headers
     * @return void
     */
    protected function doRequest($curlparam, $method, $headers = array())
    {
        $ch = curl_init();

        $header = array(
            "Authorization: Basic " . base64_encode($this->key),
            "Content-Type: application/json",
        );

        $header = array_merge($header, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlparam));
            curl_setopt($ch, CURLOPT_POST, true);
        } elseif ($method == 'GET') {
            if (!empty($curlparam)) {
                curl_setopt($ch, CURLOPT_URL, $this->url . $curlparam);
                curl_setopt($ch, CURLOPT_POST, false);
            } else {
                curl_setopt($ch, CURLOPT_URL, $this->url);
            }
        }
        curl_setopt($ch, CURLOPT_PORT, 443);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        if (is_array($curlparam)) {
            $curlparam['request_url'] = $this->url;

        } else {
            $requestURL = $this->url . $curlparam;
            $curlparam = array();
            $curlparam['request_url'] = $requestURL;
        }
        $this->request = $curlparam;
        $this->response = json_decode(curl_exec($ch), true);

        $this->curl["info"] = curl_getinfo($ch);
        $this->curl["error_nr"] = curl_errno($ch);
        $this->curl["error_text"] = curl_error($ch);
        $this->curl["http_status"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        // reset URL do default
        $this->setEnvironment();
    }
}
