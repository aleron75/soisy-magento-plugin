<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_LoanQuoteController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Bitbull_Soisy_Client
     */
    protected $_client;

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    /**
     * Amount action
     */
    public function amountAction()
    {
        if ($this->isAjax()) {
            $instalments = Mage::getStoreConfig('payment/soisy/instalments', Mage::app()->getStore());

            if ($this->getRequest()->getPost('amount') >= Mage::getStoreConfig('payment/soisy/min_order_total', Mage::app()->getStore())) {
                $this->_client = Mage::helper('soisy')->getClient();

                $amountResponse = $this->_client->getAmount(['amount' => $this->getRequest()->getPost('amount'), 'instalments' => $instalments]);

                if ($amountResponse && isset($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')})) {
                    $this->getResponse()
                        ->setBody(Mage::helper('soisy')->formatProductInfoLoanQuoteBlock($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}));
                }
            }
        }
    }
}