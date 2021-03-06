<?php


namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;

class FixFreeShippingIssue implements ObserverInterface
{
    function execute(\Magento\Framework\Event\Observer $observer) {
        $quote = $observer->getData('quote');
        $quote->getShippingAddress()->setFreeShipping(false);
    }
}
