<?php

namespace MageSuper\Casat\Observer;

class CreditmemoRefund implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getData('creditmemo');
        $order = $creditmemo->getOrder();
        $order->setState('canceled')->setStatus('canceled');
    }
}
