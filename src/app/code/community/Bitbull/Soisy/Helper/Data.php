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

    const XML_PATH_TERMS_AND_CONDITIONS = 'payment/soisy/terms_and_conditions';

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
     * @param Varien_Object $data
     */
    public function getRegionById(Varien_Object $data)
    {
        if ($regionId = $data->getRegionId()) {

            return Mage::getModel('directory/region')->load($regionId)->getCode();
        }
    }

    public function getRegionByCode($code)
    {
        if ($code) {

            return Mage::getModel('directory/region')->loadByCode($code)->getId();
        }
    }
}