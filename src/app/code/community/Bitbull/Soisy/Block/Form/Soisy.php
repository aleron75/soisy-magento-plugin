<?php
class Bitbull_Soisy_Block_Form_Soisy extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('soisy/payment/form.phtml');
    }
}
