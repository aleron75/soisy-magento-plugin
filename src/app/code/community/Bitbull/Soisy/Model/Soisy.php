<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
 */
class Bitbull_Soisy_Model_Soisy extends Mage_Payment_Model_Method_Abstract
{
    const METHOD_CODE = 'soisy';

    protected $_canUseInternal = false;

    /**
     * @var Bitbull_Soisy_Client
     */
    protected $_client = null;

    protected $_code = self::METHOD_CODE;
    protected $_formBlockType = 'soisy/form_soisy';
    protected $_infoBlockType = 'soisy/info_soisy';

    public function __construct()
    {
        $this->_client = Mage::helper('soisy')->getClient();
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Bitbull_Soisy_Model_Soisy
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        $paymentInfo = $this->getInfoInstance();
        $quote = $paymentInfo->getQuote();
        $billingAddress = $quote->getBillingAddress();

        $params = [
            'email' => $billingAddress->getEmail(),
            'amount' => $quote->getGrandTotal() * 100,
            'lastname' => $data->getLastname(),
            'firstname' => $data->getName(),
            'fiscalCode' => $data->getFiscalCode(),
            'mobilePhone' => $data->getMobilePhone(),
            'city' => $data->getCity(),
            'address' => $data->getAddress(),
            'province' => ($regionCode = Mage::helper('soisy')->getRegionById($data)) ? $regionCode : $data->getRegion(),
            'postalCode' => $data->getPostcode(),
            'civicNumber' => $data->getCivicNumber(),
        ];

        $tokenResponse = $this->_client->getToken($params);

        if ($tokenResponse->getToken()) {
            Mage::getSingleton('checkout/session')->setData('soisy_token', $tokenResponse->getToken());
            $params['region_id'] = $data->getRegionId();
            $this->getInfoInstance()
                ->setAdditionalInformation($params);
        } else {
            $this->_client->_logger->log('Error while trying to complete payment');
            Mage::throwException(Mage::helper('payment')->__('Error while trying to complete payment'));
        }

        return $this;
    }
}
