<?php

class Bitbull_Soisy_Block_Form_Soisy extends Mage_Payment_Block_Form
{
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
}
