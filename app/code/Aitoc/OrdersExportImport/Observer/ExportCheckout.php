<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Observer;

/**
 * Class ExportCheckout
 * @package Aitoc\OrdersExportImport\Observer
 */
class ExportCheckout extends \Aitoc\OrdersExportImport\Observer\AbstractObserver
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order      = $observer->getEvent()->getData('order');
        if($order){
            $this->getProfiles($order->getId());
        }
        return $this;
    }
}
