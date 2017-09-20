<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Block_Adminhtml_Form_Field_Instalments extends Mage_Core_Block_Html_Select
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        $websiteCode = Mage::app()->getRequest()->getParam('website');
        $websiteId = 0;
        if ($websiteCode) {
            $website = Mage::getModel('core/website')->load($websiteCode);
            $websiteId = $website->getId();
        }

        $instalmentPeriodArray = explode(',',
            Mage::getStoreConfig(Bitbull_Soisy_Helper_Data::XML_PATH_INSTALMENT_PERIOD, $websiteId));

        foreach ($instalmentPeriodArray as $instalmentPeriod) {
            $this->addOption($instalmentPeriod, $instalmentPeriod);
        }

        return parent::_toHtml();
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}