<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class InvoiceSaveAfter implements ObserverInterface
{
	/**
     * Set giftwrap amount invoiced to the order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();

        if ($invoice->getBaseGiftwrapAmount()) {
            $order = $invoice->getOrder();
            $order->setGiftwrapAmountInvoiced(
            	$order->getGiftwrapAmountInvoiced() + $invoice->getGiftwrapAmount()
            );
            $order->setBaseGiftwrapAmountInvoiced(
            	$order->getBaseGiftwrapAmountInvoiced() + $invoice->getBaseGiftwrapAmount()
            );
        }

        return $this;
    }
}
