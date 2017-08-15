<?php

/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */
class Bitbull_Soisy_Block_Catalog_Product_LoanQuote extends Mage_Core_Block_Template {

    /**
     * @var $_client
     */
    protected $_client;

    /**
     * Bitbull_Soisy_Block_Catalog_Product_LoanQuote constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_client = Mage::helper('soisy')->getClient();
        $this->setTemplate('soisy/catalog/product/loan_quote.phtml');
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }

        return Mage::registry('product');
    }
}