<?php

/**
 * Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Observer;

use Magento\Framework\Event\ObserverInterface;

use Magedelight\Subscribenow\Helper\Data as SubscribeHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;

/**
 * Payment method event must be in etc/events.xml so
 * whenever file is call it's work fine.
 */
class RestrictPaymentMethods implements ObserverInterface
{

    /**
     * @var SubscribeHelper
     */
    private $subscribeHelper;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * RestrictPaymentMethods constructor.
     * @param SubscribeHelper $subscribeHelper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        SubscribeHelper $subscribeHelper,
        CheckoutSession $checkoutSession,
        SubscriptionService $subscriptionService
    ) {
        $this->subscribeHelper = $subscribeHelper;
        $this->checkoutSession = $checkoutSession;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->subscribeHelper->isModuleEnable()) {
            return;
        }
        
        $allowedMethods = $this->subscribeHelper->getAllowedPaymentMethods();
        if (!empty($allowedMethods) && $this->hasSubscriptionProduct()) {
            $paymentModel = $observer->getEvent()->getMethodInstance();
            if (!in_array($paymentModel->getCode(), $allowedMethods)) {
                $checkResult = $observer->getEvent()->getResult();
                $checkResult->setData('is_available', false);
                return;
            }
        }
    }

    private function hasSubscriptionProduct()
    {
        $isSubscribedItem = false;
        $allItems = $this->checkoutSession->getQuote()->getAllItems();
        foreach ($allItems as $item) {
            if ($this->subscriptionService->isSubscribed($item)) {
                $isSubscribedItem = true;
                break;
            }
        }
        return $isSubscribedItem;
    }
}
