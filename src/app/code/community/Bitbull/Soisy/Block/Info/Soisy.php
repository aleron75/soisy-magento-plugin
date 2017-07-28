<?php

class Bitbull_Soisy_Block_Info_Soisy extends Mage_Payment_Block_Info
{

    /**
     *  Bitbull_Soisy_Block_Info_Soisy constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('soisy/payment/info.phtml');
    }

    /**
     * @var $_additionalDataArray
     */
    protected $_additionalDataArray = [];

    /**
     * @var $_fiscal_code
     */
    protected $_fiscal_code;

    /**
     * @return array
     */
    protected function getAdditionalData()
    {
        if (is_null($this->_additionalDataArray)) {
            $this->_additionalDataArray = $this->getInfo()->getData('additional_information');
        }

        return $this->_additionalDataArray;
    }

    /**
     * @param $param
     * @return string|null
     */
    protected function getParam($param)
    {
        return (array_key_exists($param, $this->getAdditionalData())) ? $this->_additionalDataArray[$param] : null;
    }

    /**
     * @return null|string
     */
    public function getFiscalCode()
    {
        return $this->getParam('fiscal_code');
    }

    /**
     * @return null|string
     */
    public function getMobilePhone()
    {
        return $this->getParam('mobile_phone');
    }

    /**
     * @return null|string
     */
    public function getCivicNumber()
    {
        return $this->getParam('civic_number');
    }
}
