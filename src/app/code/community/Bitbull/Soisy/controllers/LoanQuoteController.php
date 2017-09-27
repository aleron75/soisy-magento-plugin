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
            $amountInEurocents = $this->getRequest()->getPost('amount');
            $loanAmountInEurocents = Mage::helper('soisy')->calculateAmountBasedOnPercentage($amountInEurocents);
            $loanAmountInEurocents = ($loanAmountInEurocents) ? $loanAmountInEurocents : $amountInEurocents;
            $instalments = Mage::helper('soisy')->getDefaultInstalmentPeriodByAmountFromTable($loanAmountInEurocents);
            $textPathStoreConfig = $this->getRequest()->getPost('text');
            if (Mage::helper('soisy')->checkIfAvailableBuyAmount($loanAmountInEurocents)) {
                $this->_client = Mage::helper('soisy')->getClient();
                $amountInEurocentsResponse = $this->_client->getAmount(['amount' => $loanAmountInEurocents, 'instalments' => $instalments]);

                if ($amountInEurocentsResponse && isset($amountInEurocentsResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}) && $textPathStoreConfig) {
                    $amountInEurocentsResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->loanAmount = $loanAmountInEurocents;
                    $amountInEurocentsResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->amount = $amountInEurocents;
                    $amountInEurocentsResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->instalmentPeriod = $instalments;
                    $formatedResponse = Mage::helper('soisy')->formatResponseFromSoisyAPI($amountInEurocentsResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')});
                    $this->getResponse()
                        ->setBody(Mage::helper('soisy')->formatProductInfoLoanQuoteBlock(Mage::getStoreConfig($textPathStoreConfig, Mage::app()->getStore()),$formatedResponse));
                }
            }
        }
    }

    /**
     * Amountbyinstalment action
     */
    public function amountbyinstalmentAction() {
        if ($this->isAjax() && $this->getRequest()->getPost('amount') && $this->getRequest()->getPost('instalments')) {
            $amount = $this->getRequest()->getPost('amount');
            $instalments = $this->getRequest()->getPost('instalments');
            $loanAmount = Mage::helper('soisy')->calculateAmountBasedOnPercentage($amount);
            $loanAmount = ($loanAmount) ? $loanAmount : $amount;

            if (Mage::helper('soisy')->checkIfAvailableBuyAmount($loanAmount)) {
                $this->_client = Mage::helper('soisy')->getClient();
                $amountResponse = $this->_client->getAmount([
                    'amount' => $loanAmount ,
                    'instalments' => $instalments
                ]);

                if ($amountResponse && isset($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')})) {
                    $amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->loanAmount = $loanAmount;
                    $amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->amount = $amount;
                    $amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')}->instalmentPeriod = $instalments;
                    $formatedResponse = Mage::helper('soisy')->formatResponseFromSoisyAPI($amountResponse->{Mage::getStoreConfig('payment/soisy/information_about_loan')});
                    $this->getResponse()
                        ->clearHeaders()->setHeader(
                            'Content-type',
                            'application/json'
                        )
                        ->setBody(Mage::helper('core')->jsonEncode(
                            $formatedResponse
                        ));
                }
            }
        }
    }
}