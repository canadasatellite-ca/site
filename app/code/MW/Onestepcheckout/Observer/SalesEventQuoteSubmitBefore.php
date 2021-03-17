<?php

namespace MW\Onestepcheckout\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesEventQuoteSubmitBefore implements ObserverInterface
{
	/**
     * Set giftwrap amount to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder();
    	$quote = $observer->getEvent()->getQuote();

        $address = $quote->getShippingAddress();

        $order->setGiftwrapAmount($address->getGiftwrapAmount());
        $order->setBaseGiftwrapAmount($address->getBaseGiftwrapAmount());

        return $this;
    }
}
