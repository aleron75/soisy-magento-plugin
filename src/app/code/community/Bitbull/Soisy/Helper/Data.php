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

    const XML_PATH_ZERO_INTEREST = 'payment/soisy/zero_interest';

    const XML_PATH_INSTALMENT_PERIOD = 'payment/soisy/instalments';

    const XML_PATH_INSTALMENTS_TABLE = 'payment/soisy/instalments_table';

    const XML_PATH_TERMS_AND_CONDITIONS = 'payment/soisy/terms_and_conditions';

    const XML_PATH_DESCRIPTION = 'payment/soisy/description';

    const XML_PATH_NEW_ORDER_TEMPLATE = 'payment/soisy/template';

    const XML_PATH_NEW_ORDER_TEMPLATE_GUEST = 'payment/soisy/guest_template';

    const XML_PATH_MIN_TOTAL = 'payment/soisy/min_order_total';

    const XML_PATH_MAX_TOTAL = 'payment/soisy/max_order_total';

    const XML_PATH_MAX_SANDBOX_MODE = 'payment/soisy/sandbox_mode';

    const PATTERN = "/^([-\p{L}.'0-9 ]*?)(?: ([0-9]*))?$/u";

    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_SEARCH, $store);
    }

    /**
     * Check if Zero Interest rate is enable
     * @param null $store
     * @return bool
     */
    public function isZeroInterestRate($store = null)
    {
        return (Mage::getStoreConfigFlag(self::XML_PATH_ZERO_INTEREST, $store)) ? "true" : "false";
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
        $sandboxMode = (int)Mage::getStoreConfig(self::XML_PATH_MAX_SANDBOX_MODE);
        $logger = Mage::helper('soisy/log');

        $client = new Bitbull_Soisy_Client($shopId, $apiKey, $logger, $sandboxMode);

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
     * @param $obj
     * @return mixed
     */
    public function formatResponseFromSoisyAPI($obj)
    {
        $obj->instalmentAmount = Mage::helper('core')->formatPrice($obj->instalmentAmount / 100, true);
        $obj->totalRepaid = Mage::helper('core')->formatPrice($obj->totalRepaid / 100, true);
        $obj->upfront = Mage::helper('core')->formatPrice(($obj->amount - $obj->loanAmount) / 100, true);

        return $obj;
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
            '{INSTALMENT_AMOUNT}' => $obj->instalmentAmount,
            '{INSTALMENT_PERIOD}' => $obj->instalmentPeriod,
            '{TOTAL_REPAID}' => $obj->totalRepaid,
            '{UPFRONT_PAYMENT}' => $obj->upfront,
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
        return $amount - ($amount / 100) * (Mage::getStoreConfig(self::XML_PATH_TOTAL_PERCENTAGE_OF_LOAN,
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
     * @param $amount
     * @return string
     */
    public function getInstalmentPeriodJson($amount)
    {
        $instalmentPeriodArray = explode(',',
            Mage::getStoreConfig(self::XML_PATH_INSTALMENT_PERIOD, Mage::app()->getStore()));

        $instalmentConfig = [
            'preselected' => $this->getDefaultInstalmentPeriodByAmountFromTable($amount),
            'options' => $instalmentPeriodArray
        ];

        return Mage::helper('core')->jsonEncode($instalmentConfig);
    }

    /**
     * @param $amount
     * @return bool
     */
    public function checkIfAvailableBuyAmount($amount)
    {
        return ((Mage::getStoreConfig(self::XML_PATH_MIN_TOTAL, Mage::app()->getStore()) * 100 <= $amount)
            && ($amount <= (Mage::getStoreConfig(self::XML_PATH_MAX_TOTAL, Mage::app()->getStore()) * 100)));
    }

    /**
     * Get instalment period from system config serialized array
     * @param $amount
     * @return int
     */
    public function getDefaultInstalmentPeriodByAmountFromTable($amount)
    {
        $instalmentTable = Mage::getStoreConfig(self::XML_PATH_INSTALMENTS_TABLE, Mage::app()->getStore());

        if ($instalmentTable) {
            $instalmentTable = unserialize($instalmentTable);
            if (is_array($instalmentTable)) {
                $lastItem = null;

                usort($instalmentTable, function ($a, $b) {
                    return $a['from_price'] - $b['from_price'];
                });

                for ($i = 0; $i < count($instalmentTable); $i++) {

                    if ((($instalmentTable[$i]['from_price'] * 100) <= $amount) && isset($instalmentTable[$i + 1]) && (($instalmentTable[$i + 1]['from_price'] * 100) > $amount)) {
                        return (int)($instalmentTable[$i]['instalments']);
                    } else {
                        if (!isset($instalmentTable[$i + 1])) {
                            return (int)($instalmentTable[$i]['instalments']);
                        }
                    }
                }
            }
        }
    }
}