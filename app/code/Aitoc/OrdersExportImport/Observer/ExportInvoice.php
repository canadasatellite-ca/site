<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Observer;

/**
 * Class ExportInvoice
 * @package Aitoc\OrdersExportImport\Observer
 */
class ExportInvoice extends \Aitoc\OrdersExportImport\Observer\AbstractObserver
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoiceItem = $observer->getEvent()->getData('invoice_item');
        $order       = $invoiceItem->getInvoice()->getOrder();
        $orderItems  = [];
        foreach ($order->getItemsCollection() as $item) {
            if (!$item->isDummy()) {
                $orderItems[] = $item->getId();
            }
        }
        $items = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\Sales\Model\Order\Invoice\Item')
            ->getCollection()->addFieldToFilter('order_item_id', ['in' => $orderItems]);

        $invoiceItems = [];
        foreach ($items as $item) {
            if ($item->getBasePrice()) {
                $invoiceItems[] = $item->getOrderItemId();
            }
        }
        if (!count(array_diff($orderItems, $invoiceItems))) {
            $this->getProfiles($order->getId(), 2);
        }

        return $this;
    }
}
