<?php
/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
*/
class Bitbull_Soisy_Client
{
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';

    /**
     * Base url for API calls
     *
     * @var string
     */
    protected $_apiBaseUrl = 'http://api.sandbox.soisy.it/api/shops';

    /**
     * Base url for Soisy webapp
     *
     * @var string
    */
    protected $_webappBaseUrl = 'http://shop.sandbox.soisy.it';

    /**
     * API key
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * Shop ID
     *
     * @var string
     */
    protected $_shopId;

    /**
     * Timeout for API connection wait
     * in milliseconds
     *
     * @var int
     */
    protected $_connectTimeout = 4000;

    /**
     * Timeout for API response wait
     * in milliseconds
     *
     * @var int
     */
    protected $_timeout = 4000;

    /**
     * @var stdClass
    */
    protected $_response = null;

    /**
     * @var Bitbull_Soisy_Log_LoggerInterface
    */
    protected $_logger;

    /**
     * @param string $shopId
     * @param string $apiKey
     * @param Bitbull_Soisy_Log_LoggerInterface $logger
    */
    public function __construct($shopId, $apiKey, Bitbull_Soisy_Log_LoggerInterface $logger)
    {
        $this->_shopId = $shopId;
        $this->_apiKey = $apiKey;

        $this->_logger = $logger;
    }

    /**
     * Perform a search for suggestions
     *
     * @param array $params
     * @return Bitbull_Soisy_Order_Token
     */
    public function getToken(array $params)
    {
        $rawResponse = $this->_doRequest('/orders', self::HTTP_METHOD_POST, $params);

        $result = new Bitbull_Soisy_Order_Token();
        $result->setResponse($rawResponse);

        return $result;
    }

    /**
     * Build and return the redirect url
     *
     * @param string $token
     * @return string
    */
    public function getRedirectUrl($token)
    {
        return $this->_webappBaseUrl . '/' . $this->_shopId . '#/loan-request-and-quote?token=' . $token;
    }

    /**
     * Build and execute request via CURL.
     *
     * @param string $path
     * @param string $httpMethod
     * @param array $params
     * @param int $timeout
     * @return stdClass
     * @throws Bitbull_Soisy_Exception
    */
    protected function _doRequest($path, $httpMethod = self::HTTP_METHOD_GET, $params = [], $timeout = null)
    {
        $url = $this->_buildUrl($path, $params);

        $this->_logger->debug("Performing API request to url: " . $url . " with method: " . $httpMethod);
        $this->_logger->debug("Params: " . print_r($params, true));

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Auth-Token: ' . $this->_apiKey,
        ]);

        if ($httpMethod == self::HTTP_METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->_connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, !is_null($timeout) ? $timeout : $this->_timeout);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $output = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errorNumber = curl_errno($ch);

        curl_close($ch);

        $this->_logger->debug("Raw response: " . print_r($output, true));

        if (false === $output) {
            
            throw new Bitbull_Soisy_Exception('cURL error = ' . $error, $errorNumber);

        } else if ($httpStatusCode != 200) {

            $validationMessages = [];
            switch ($httpStatusCode) {
                case 400:
                    $message = 'Some fields contains errors';
                    $validationMessages = $this->_parseValidationMessages($output);
                    break;

                default:
                    $message = 'API unavailable, HTTP STATUS CODE = ' . $httpStatusCode;
            }

            $e = new Bitbull_Soisy_Exception($message);
            $e->setValidationMessages($validationMessages);

            throw $e;

        } else {
            $response = json_decode($output);

            return $response;
        }
    }

    /**
     * @param string $rawResponse
     * @return array
    */
    protected function _parseValidationMessages($rawResponse)
    {
        $validationMessages = [];

        $response = json_decode($rawResponse);

        if ($response->errors) {
            foreach ($response->errors as $field => $errors) {
                foreach ($errors as $error) {
                    $validationMessages[] = $field . ': ' . $error;
                }
            }
        }

        Mage::log($validationMessages);

        return $validationMessages;
    }

    /**
     * Build an url for an API call
     *
     * @param string $path
     * @param array $params
     * @return string
     * @throws Bitbull_Soisy_Exception
    */
    protected function _buildUrl($path, $params)
    {
        if (filter_var($this->_apiBaseUrl, FILTER_VALIDATE_URL) === false) {
            $message = 'API base URL missing or invalid: "' . $this->_apiBaseUrl . '"';

            throw new Bitbull_Soisy_Exception($message, 0);
        }

        $url = $this->_apiBaseUrl . '/' . $this->_shopId . $path;

        $queryString = [];

        foreach ($params as $key => $value) {
            $queryString[] = $key . '=' . $value;
        }

        $url .= '?' . implode('&', $queryString);

        return $url;
    }
}