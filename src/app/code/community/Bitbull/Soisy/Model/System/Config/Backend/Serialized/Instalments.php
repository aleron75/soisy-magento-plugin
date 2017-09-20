<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Model_System_Config_Backend_Serialized_Instalments extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    /**
     * @var
     */
    protected $_minimalAmount;

    /**
     * Bitbull_Soisy_Model_System_Config_Backend_Serialized_Instalments constructor
     */
    protected function _construct()
    {
        $websiteCode = Mage::app()->getRequest()->getParam('website');
        $websiteId = 0;
        if ($websiteCode) {
            $website = Mage::getModel('core/website')->load($websiteCode);
            $websiteId = $website->getId();
        }

        $this->_minimalAmount = Mage::getStoreConfig(Bitbull_Soisy_Helper_Data::XML_PATH_MIN_TOTAL, $websiteId);

        parent::_construct();
    }

    /**
     * Unset array element with '__empty' key and check if min amount set to table
     *
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            return;
        }

        unset($value['__empty']);

        $isSetMinValue = false;
        foreach ($value as $data) {
            if ($data['from_price'] == $this->_minimalAmount) {
                $isSetMinValue = true;
            }
        }

        if (!$isSetMinValue) {
            Mage::throwException(Mage::helper('soisy')->__('You should add minimum amount %s in Instalments table',
                $this->_minimalAmount));
        }

        $this->setValue($value);
        parent::_beforeSave();
    }
}