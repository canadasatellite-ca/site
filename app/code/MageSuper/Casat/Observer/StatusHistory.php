<?php

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class StatusHistory implements ObserverInterface
{
    protected $request;
    function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }


    function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $object = $observer->getData('data_object');
        $history = $this->request->getPost('history');
        $visible = isset($history['is_show_in_pdf']) ? boolval($history['is_show_in_pdf']) : false;
        $object->setData('is_show_in_pdf', $visible);
    }
}