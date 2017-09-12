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
     * @var $_additionalInformationArray
     */
    protected $_additionalInformationArray = [];

    /**
     * Bitbull_Soisy_Block_Form_Soisy constructor
     */
    protected function _construct()
    {
        $this->_client =  Mage::helper('soisy')->getClient();
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $mark->setTemplate('soisy/payment/mark.phtml');
        $this->setTemplate('soisy/payment/form.phtml');
        $this->setMethodLabelAfterHtml($mark->toHtml());
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

    public function getMethodTitle()
    {
        $instalments = Mage::getStoreConfig('payment/soisy/instalments', Mage::app()->getStore());

        $amountResponse = $this->_client->getAmount(['amount' => Mage::helper('soisy')->calculateAmountBasedOnPercentage(Mage::getModel('checkout/session')->getQuote()->getGrandTotal()), 'instalments' => $instalments]);

        if ($amountResponse && isset($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')})) {
            return __(' Pay Installment %s x %s months',
                Mage::helper('core')->formatPrice($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->instalmentAmount / 100,
                    false),$instalments );
        }
    }
}
