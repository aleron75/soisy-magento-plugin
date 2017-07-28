<?php
/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
 */
class Bitbull_Soisy_Order_Token
{
    protected $_response = null;

    public function __construct($response = null)
    {
        if ($response) {
            $this->setResponse($response);
        }
    }

    public function setResponse($response)
    {
        $this->_response = $response;
    }

    public function getToken()
    {
        return $this->_response->token;
    }
}