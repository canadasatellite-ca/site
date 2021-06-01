<?php

namespace CanadaSatellite\Theme\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Event\ManagerInterface;

class QuoteFixExtension implements ObserverInterface
{
    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $_eventManager;

    function __construct(
        ManagerInterface $eventManager
    ) {
        $this->_eventManager = $eventManager;
    }

    function execute(EventObserver $observer)
    {
        if ($observer->getEvent()->getQuoteAddress()->getExtensionPhone()) {
            $extensionPhone = $observer->getEvent()->getQuoteAddress()->getExtensionPhone();
            $extensionPhone = trim(str_replace('extension_phone', '', $extensionPhone));
            $observer->getEvent()->getQuoteAddress()->setExtensionPhone($extensionPhone);
        }
    }

}