<?php

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class PaymentMethodIsActive implements ObserverInterface
{

    function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $method_instance = $observer->getMethodInstance();
        $quote = $observer->getQuote();
        $result = $observer->getResult();

        if($method_instance->getCode()=='quotation_quote' && $quote){
            $result->setData('is_available',false);
        }
    }
}