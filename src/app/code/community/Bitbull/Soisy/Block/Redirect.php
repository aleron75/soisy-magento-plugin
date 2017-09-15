<?php
/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
 */
class Bitbull_Soisy_Block_Redirect extends Mage_Core_Block_Template
{
    /**
     * @var Bitbull_Soisy_Client
     */
    protected $_client;

    /**
     * @var Mage_Checkout_Model_Session
     */
    protected $_session;

    protected function _construct()
    {
        parent::_construct();

        $this->_client = Mage::helper('soisy')->getClient();
        $this->_session = Mage::getSingleton('checkout/session');

        $this->setTemplate('soisy/redirect.phtml');
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $token = $this->_session->getData('soisy_token');
        $this->_session->unsetData('soisy_token');

        return ($token) ? $this->_client->getRedirectUrl($token) : null;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->orderPaidWithSoisy()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Verify that the order was paid using Soisy
     *
     * @return boolean
    */
    public function orderPaidWithSoisy()
    {
        return Mage::getSingleton('checkout/session')->hasData('soisy_token');
    }
}