<?php
/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
 */
class Bitbull_Soisy_Model_Soisy extends Mage_Payment_Model_Method_Abstract
{
    protected $_canUseInternal = false;

    /**
     * @var Bitbull_Soisy_Client
    */
    protected $_client = null;
    
    protected $_code  = 'soisy';
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

        $tokenResponse = $this->_client->getToken([
            'email'       => $billingAddress->getEmail(),
            'amount'      => $quote->getGrandTotal() * 100,
            'lastname'    => $billingAddress->getLastname(),
            'firstname'   => $billingAddress->getFirstname(),
            'fiscalCode'  => $data->getFiscalCode(),
            'mobilePhone' => $data->getMobilePhone(),
            'city'        => $billingAddress->getCity(),
            'address'     => $billingAddress->getStreetFull(),
            'province'    => $billingAddress->getRegion(),
            'postalCode'  => $billingAddress->getPostcode(),
            'civicNumber' => $data->getCivicNumber(),
        ]);

        Mage::getSingleton('checkout/session')->setData('soisy_token', $tokenResponse->getToken());

        $this->getInfoInstance()
            ->setFiscalCode($data->getData('fiscal_code'))
            ->setMobilePhone($data->getData('mobile_phone'))
            ->setCivicNumber($data->getData('civic_number'));

        return $this;
    }
}
