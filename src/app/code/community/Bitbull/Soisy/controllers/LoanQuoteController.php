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
        if ($this->isAjax() && $this->getRequest()->getPost('amount')) {
            $amount = $this->getRequest()->getPost('amount');
            $instalments = Mage::helper('soisy')->getDefaultInstalmentPeriodByAmountFromTable($amount);
            $loanAmount = Mage::helper('soisy')->calculateAmountBasedOnPercentage($amount);
            $loanAmount = ($loanAmount) ? $loanAmount : $amount;
            $textPathStoreConfig = $this->getRequest()->getPost('text');
            if (Mage::helper('soisy')->checkIfAvailableBuyAmount($loanAmount)) {
                $this->_client = Mage::helper('soisy')->getClient();
                $amountResponse = $this->_client->getAmount(['amount' => $loanAmount * 100, 'instalments' => $instalments]);

                if ($amountResponse && isset($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}) && $textPathStoreConfig) {
                    $amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->loanAmount = $loanAmount;
                    $amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->amount = $amount;
                    $amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->instalmentPeriod = $instalments;
                    $this->getResponse()
                        ->setBody(Mage::helper('soisy')->formatProductInfoLoanQuoteBlock(Mage::getStoreConfig($textPathStoreConfig, Mage::app()->getStore()),$amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}));
                }
            }
        }
    }
}