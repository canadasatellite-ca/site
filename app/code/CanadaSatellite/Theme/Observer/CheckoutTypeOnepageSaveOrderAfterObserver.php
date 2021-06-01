<?php

namespace CanadaSatellite\Theme\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Session\Quote;

class CheckoutTypeOnepageSaveOrderAfterObserver implements ObserverInterface
{
    const METHODS = array(
        'cashondelivery',
        'beanstream',
        'purchaseorder'
    );
    /**
     * @var Quote
     */
    private $_session;

    /**
     * CheckoutTypeOnepageSaveOrderAfterObserver constructor.
     * @param Quote $quoteSession
     */
    function __construct(
        Quote $quoteSession
    )
    {
        $this->_session = $quoteSession;
    }

    /**
     * @param EventObserver $observer
     */
    function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();
        foreach (self::METHODS as $method) {
            if ($paymentMethod == $method) {
                $order->setCanSendNewEmailFlag(false);
                break;
            }
        }
    }
}