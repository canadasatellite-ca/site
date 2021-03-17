<?php

namespace MageSuper\Invoice\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class ChangeInvoiceIncrement implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $invoice = $observer->getInvoice();

        $increment = $invoice->getIncrementId();
        if (!$increment) {
            $increment = $invoice->getOrder()->getIncrementId();
            $invoice->setIncrementId($increment);
        }
    }
}