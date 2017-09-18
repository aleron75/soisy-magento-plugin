<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Block_Form_Soisy extends Mage_Payment_Block_Form
{

    /**
     * @var Bitbull_Soisy_Client
     */
    protected $_client = null;

    /**
     * @var Bitbull_Soisy_Helper_Data
     */
    protected $_helper = null;

    /**
     * @var $_additionalInformationArray
     */
    protected $_additionalInformationArray = [];

    /**
     * Bitbull_Soisy_Block_Form_Soisy constructor
     */
    protected function _construct()
    {
        $this->_helper = $this->helper('soisy');
        $this->_client = $this->_helper->getClient();
        $markBlockClassname = Mage::getConfig()->getBlockClassName('core/template');
        $markBlock = new $markBlockClassname;
        $markBlock->setTemplate('soisy/payment/mark.phtml');
        $this->setTemplate('soisy/payment/form.phtml');
        $this->setMethodLabelAfterHtml($markBlock->toHtml());
        $this->setMethodTitle($this->getMethodTitle());

        return parent::_construct();
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

        return (array_key_exists($param,
            $this->_additionalInformationArray)) ? $this->_additionalInformationArray[$param] : null;
    }

    /**
     * Get name for payment method
     * @return string
     */
    public function getMethodTitle()
    {
        $total = (float)Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
        $amountBasedOnPercentage = $this->_helper->calculateAmountBasedOnPercentage($total);

        if ($amountBasedOnPercentage) {
            return __('&nbsp; Pay with Soisy: upfront of %s + %s installments',
                $this->helper('core')->formatPrice($total - $amountBasedOnPercentage, false),
                implode("/", $this->_helper->getInstalmentMaxAndMinPeriod()));
        } else {
            return __('&nbsp; Pay with Soisy:  %s installments',
                implode("/", $this->_helper->getInstalmentMaxAndMinPeriod()));
        }
    }
}
