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
    protected $_client = null;

    protected function _construct()
    {
        parent::_construct();

        $this->_client = Mage::helper('soisy')->getClient();

        $this->setTemplate('soisy/redirect.phtml');
    }

    /**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        // @todo take from the order
        $token = Mage::getSingleton('checkout/session')->getData('soisy_token');

        return $this->_client->getRedirectUrl($token);
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
        // @todo take from the order
        return Mage::getSingleton('checkout/session')->hasData('soisy_token');
    }
}