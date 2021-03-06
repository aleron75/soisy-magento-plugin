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
        $loanAmount = Mage::helper('soisy')->calculateAmountBasedOnPercentage($quote->getGrandTotal() * 100);
        $params = [
            'email' => $billingAddress->getEmail(),
            'amount' => ($loanAmount) ? $loanAmount : $quote->getGrandTotal() * 100,
            'lastname' => $data->getLastname(),
            'firstname' => $data->getName(),
            'fiscalCode' => $data->getFiscalCode(),
            'mobilePhone' => $data->getMobilePhone(),
            'city' => $data->getCity(),
            'address' => $data->getAddress(),
            'province' => strtoupper(substr(trim($data->getProvince()),0, 2)),
            'postalCode' => $data->getPostcode(),
            'civicNumber' => $data->getCivicNumber(),
            'instalments' => $data->getInstalment(),
            'zeroInterestRate' => Mage::helper('soisy')->isZeroInterestRate()
        ];

        if ($data->getVatId()) {
            $params['vatId'] = $data->getVatId();
        }

        try {
            $tokenResponse = $this->_client->getToken($params);
        } catch (Bitbull_Soisy_Exception $exception) {
            throw new Mage_Payment_Exception(__('Impossible to connect to Soisy service'));
        }

        if ($tokenResponse->getToken()) {
            Mage::helper('soisy')->setNewOrderTemplate();
            Mage::getSingleton('checkout/session')->setData('soisy_token', $tokenResponse->getToken());
            $params['region_id'] = $data->getRegionId();
            $this->getInfoInstance()
                ->setAdditionalInformation($params);
        } else {
            if ($tokenResponse->getErrorFromSoisy()) {
                $msg = 'Error in %s field';

                $field = $tokenResponse->getErrorFromSoisy();
                if (in_array($field, ['amount', 'address', 'civicNumber', 'postalCode', 'city', 'province', 'firstname',
                    'lastname', 'email', 'mobilePhone', 'postalCode', 'fiscalCode',])) {
                    $msg = sprintf($msg, $field);
                }
                Mage::helper('soisy/log')->log($msg);
                Mage::throwException(Mage::helper('payment')->__($msg));
            } else {
                Mage::helper('soisy/log')->log('Error while trying to complete payment');
                Mage::throwException(Mage::helper('payment')->__('Error while trying to complete payment'));
            }
        }

        return $this;
    }

    public function isApplicableToQuote($quote, $checksBitMask)
    {
        if (!parent::isApplicableToQuote($quote, $checksBitMask)) {
            return false;
        }

        $total = $quote->getGrandTotal() * 100;
        $loanAmount = Mage::helper('soisy')->calculateAmountBasedOnPercentage($total);

        return ($loanAmount) ? Mage::helper('soisy')->checkIfAvailableBuyAmount($loanAmount) : Mage::helper('soisy')->checkIfAvailableBuyAmount($total);
    }
}
