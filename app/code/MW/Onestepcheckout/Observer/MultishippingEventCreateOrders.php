<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class MultishippingEventCreateOrders implements ObserverInterface
{
	/**
     * Set giftwrap amount to order from address in multiple addresses checkout.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder();
    	$address = $observer->getEvent()->getAddress();

        $order->setGiftwrapAmount($address->getGiftwrapAmount());
        $order->setBaseGiftwrapAmount($address->getBaseGiftwrapAmount());

        return $this;
    }
}
