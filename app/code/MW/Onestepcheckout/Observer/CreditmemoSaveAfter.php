<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class CreditmemoSaveAfter implements ObserverInterface
{
	/**
     * Set giftwrap amount refunded to the order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();

        if ($creditmemo->getGiftwrapAmount()) {
            $order = $creditmemo->getOrder();
            $order->setGiftwrapAmountRefunded(
            	$order->getGiftwrapAmountRefunded() + $creditmemo->getGiftwrapAmount()
            );
            $order->setBaseGiftwrapAmountRefunded(
            	$order->getBaseGiftwrapAmountRefunded() + $creditmemo->getBaseGiftwrapAmount()
            );
        }

        return $this;
    }
}
