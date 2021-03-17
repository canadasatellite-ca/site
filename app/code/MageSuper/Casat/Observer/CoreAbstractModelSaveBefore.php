<?php

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class CoreAbstractModelSaveBefore implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $object = $observer->getObject();
        if ( $object instanceof \Magento\Bundle\Model\Selection) {
            $selection_price_value = $object->getData('selection_price_value');
            if(stripos($selection_price_value,'C$')===0){
                $selection_price_value = substr($selection_price_value,2);
                $object->setData('selection_price_value',$selection_price_value);
            }
        }

    }
}