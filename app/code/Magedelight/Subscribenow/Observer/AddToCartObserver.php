<?php

/**
 * Magedelight
 * Copyright (C) 2018 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2018 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\Subscribenow\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magedelight\Subscribenow\Helper\Data;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;
use Magedelight\Subscribenow\Model\Subscription;
use Magento\Framework\Message\ManagerInterface;

/**
 * AddToCartObserver
 *
 * This observer validate requested qty is not greater than
 * the "Allowed Maximum Quantity To Subscribe Per Product" from configuration
 */
class AddToCartObserver implements ObserverInterface
{
   
    /**
     * @var Subscription
     */
    private $subscription;
    
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var RequestInterface
     */
    private $request;
    
    /**
     * @var Data
     */
    private $helper;
    
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * Constructor
     * @param Data $helper
     * @param Subscription $subscription
     * @param SubscriptionService $subscriptionService
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Data $helper,
        Subscription $subscription,
        SubscriptionService $subscriptionService,
        RequestInterface $request,
        ManagerInterface $messageManager
    ) {
        $this->helper = $helper;
        $this->subscription = $subscription;
        $this->subscriptionService = $subscriptionService;
        $this->request = $request;
        $this->messageManager = $messageManager;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $isModuleEnabled = $this->helper->isModuleEnable();
        $item = $observer->getEvent()->getItem();

        if (!$isModuleEnabled
            || !$item
            || !$item->getProductId()
            || !$item->getQuote()
        ) {
            return $this;
        }
        
        $product = $item->getProduct();
        if ($this->subscription->isAdd($product)) {
            $cartRequest = $this->request->getParam('cart', []);
            try {
                $this->subscriptionService->validate($item, $cartRequest);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                throw new \Exception($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Item is not updating'));
            }
        }
    }
}
