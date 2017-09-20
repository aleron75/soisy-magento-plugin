<?php

/**
 * @package Bitbull_Soisy
 * @author Gennaro Vietri <gennaro.vietri@bitbull.it>
 */
class Bitbull_Soisy_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE_SEARCH = 'payment/soisy/active';

    const XML_PATH_API_KEY = 'payment/soisy/api_key';

    const XML_PATH_SHOP_ID = 'payment/soisy/shop_id';

    const XML_PATH_TOTAL_PERCENTAGE_OF_LOAN = 'payment/soisy/percentage';

    const XML_PATH_LOAN_CONFIGURE = 'payment/soisy/information_about_loan';

    const XML_PATH_INSTALMENT_PERIOD = 'payment/soisy/instalments';

    const XML_PATH_TERMS_AND_CONDITIONS = 'payment/soisy/terms_and_conditions';

    const XML_PATH_DESCRIPTION = 'payment/soisy/description';

    const XML_PATH_NEW_ORDER_TEMPLATE = 'payment/soisy/template';

    const XML_PATH_NEW_ORDER_TEMPLATE_GUEST = 'payment/soisy/guest_template';

    const XML_PATH_MIN_TOTAL = 'payment/soisy/min_order_total';

    const XML_PATH_MAX_TOTAL = 'payment/soisy/max_order_total';

    const PATTERN = "/^([-\p{L}.'0-9 ]*?)(?: ([0-9]*))?$/u";

    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_SEARCH, $store);
    }

    /**
     * Create and configure a Soisy API Client instance
     *
     * @return Bitbull_Soisy_Client
     */
    public function getClient()
    {
        $apiKey = Mage::getStoreConfig(self::XML_PATH_API_KEY);
        $shopId = Mage::getStoreConfig(self::XML_PATH_SHOP_ID);

        $logger = Mage::helper('soisy/log');

        $client = new Bitbull_Soisy_Client($shopId, $apiKey, $logger);

        return $client;
    }

    /**
     * Get billing address without civic number
     * @param $address
     * @return mixed
     */
    public function getBillingAddressWithoutCivicNumber($address)
    {
        $matches = [];
        preg_match(self::PATTERN, $address, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }
    }

    /**
     * Get civic number from billing address
     * @param $address
     * @return mixed
     */
    public function getCivicFromBillingAddress($address)
    {
        $matches = [];
        preg_match(self::PATTERN, $address, $matches);

        if (isset($matches[2])) {
            return $matches[2];
        }
    }

    /**
     * Format output message for soisy product loan block
     * @param text
     * @param $obj
     * @return string
     */
    public function formatProductInfoLoanQuoteBlock($text, $obj)
    {
        $variables = array(
            '{INSTALMENT_AMOUNT}' => Mage::helper('core')->formatPrice($obj->instalmentAmount / 100, true),
            '{INSTALMENT_PERIOD}' => $this->getInstalmentPeriod(),
            '{TOTAL_REPAID}' => Mage::helper('core')->formatPrice($obj->totalRepaid / 100, true),
            '{UPFRONT_PAYMENT}' => Mage::helper('core')->formatPrice(($obj->amount - $obj->loanAmount) , true),
            '{TAEG}' => $obj->apr,
        );

        return strtr($text, $variables);
    }

    /**
     * Inject custom Soisy Merchant email templates
     */
    public function setNewOrderTemplate()
    {
        if (Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_TEMPLATE, Mage::app()->getStore())) {
            Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE,
                Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_TEMPLATE, Mage::app()->getStore()));
        }

        if (Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_TEMPLATE_GUEST, Mage::app()->getStore())) {
            Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE,
                Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_TEMPLATE, Mage::app()->getStore()));
        }
    }

    /**
     * Calculate Soisy loan based on percentage of loan
     *
     * @param $amount
     * @return mixed
     */
    public function calculateAmountBasedOnPercentage($amount)
    {
        return $amount - (($amount / 100) * Mage::getStoreConfig(self::XML_PATH_TOTAL_PERCENTAGE_OF_LOAN,
                    Mage::app()->getStore()));
    }

    public function getInstalmentMaxAndMinPeriod()
    {
        $instalmentPeriodArray = explode(',',
            Mage::getStoreConfig(self::XML_PATH_INSTALMENT_PERIOD, Mage::app()->getStore()));

        if (min($instalmentPeriodArray) != max($instalmentPeriodArray)) {
            return [min($instalmentPeriodArray), max($instalmentPeriodArray)];
        } else {
            return [min($instalmentPeriodArray)];
        }
    }

    /**
     * Return instalment period from select
     * @return mixed
     */
    public function getInstalmentPeriod()
    {
        $instalmentPeriodArray = explode(',',
            Mage::getStoreConfig(self::XML_PATH_INSTALMENT_PERIOD, Mage::app()->getStore()));

        return min($instalmentPeriodArray);
    }

    /**
     *
     *
     * @param $amount
     * @return bool
     */
    public function checkIfAvailableBuyAmount($amount)
    {
        return ((Mage::getStoreConfig(self::XML_PATH_MIN_TOTAL, Mage::app()->getStore()) <= $amount)
            && ($amount <= Mage::getStoreConfig(self::XML_PATH_MAX_TOTAL, Mage::app()->getStore())));
    }
}