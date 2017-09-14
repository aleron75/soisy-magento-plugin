<?php
/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Gennaro Vietri <gennaro.vietri@bitbull.it>
 */
class Bitbull_Soisy_Model_Observer
{
    /**
     * Add order information into Soisy redirect block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setOrdersIds(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();

        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('soisy_redirect');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
