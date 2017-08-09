<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Block_Form_Soisy extends Mage_Payment_Block_Form
{

    /**
     * @var $_additionalInformationArray
     */
    protected $_additionalInformationArray = [];

    /**
     * Bitbull_Soisy_Block_Form_Soisy constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('soisy/payment/form.phtml');
    }

    /**
     * @return Mage_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        $quote = $this->getMethod()->getInfoInstance()->getQuote();

        return $quote->getBillingAddress();
    }

    /**
     * @param $param
     * @return mixed|null
     */
    public function getAdditionalInformation($param)
    {
        if (empty($this->_additionalInformationArray)) {
            $this->_additionalInformationArray = $this->getMethod()->getInfoInstance()->getAdditionalInformation();
        }

        return (array_key_exists($param, $this->_additionalInformationArray)) ? $this->_additionalInformationArray[$param] : null;
    }
}
