<?php

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class SalesModelServiceQuoteSubmitSuccess implements ObserverInterface
{

    function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getQuote();
        $billing_address = $quote->getBillingAddress();
        if ($billing_address->getCustomerAddressId() && !$billing_address->getCustomerId() && $quote->getCustomerId()) {
            $billing_address->setCustomerId($quote->getCustomerId());
        }
    }
}