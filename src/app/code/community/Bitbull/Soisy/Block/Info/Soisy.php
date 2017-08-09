<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
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
     * @var array $_additionalInformationArray
     */
    protected $_additionalInformationArray = [];

    /**
     * @return array
     */
    protected function getAdditionalInformation()
    {
        if (empty($this->_additionalInformationArray)) {
            $this->_additionalInformationArray = $this->getInfo()->getData('additional_information');
        }

        return $this->_additionalInformationArray;
    }

    /**
     * @param $param
     * @return string|null
     */
    public function getAdditionalInformationValueByKey($param)
    {
        return (array_key_exists($param, $this->getAdditionalInformation())) ? $this->_additionalInformationArray[$param] : null;
    }

}
