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

namespace Magedelight\Subscribenow\Plugin\Checkout\Model;

use Magento\Framework\App\Request\Http;
use Magedelight\Subscribenow\Helper\Data as SubscriptionHelper;
use Magedelight\Subscribenow\Model\Subscription;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Sales\Model\OrderRepository;

class Quote
{

    /**
     * @var Http
     */
    private $request;

    /**
     * @var SubscriptionHelper
     */
    private $subscriptionHelper;

    /**
     * @var Subscription
     */
    private $subscription;
    
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;
    
    /**
     * @var OrderRepository
     */
    private $orderRepository;


    /**
     * Quote Plugin Constructor
     *
     * @param Http $request
     * @param SubscriptionHelper $subscriptionHelper
     * @param SubscriptionService $subscription
     * @param DataObjectFactory $objectFactory
     */
    public function __construct(
        Http $request,
        SubscriptionHelper $subscriptionHelper,
        Subscription $subscription,
        DataObjectFactory $objectFactory,
        OrderRepository $orderRepository
    ) {
        $this->request = $request;
        $this->subscriptionHelper = $subscriptionHelper;
        $this->subscription = $subscription;
        $this->objectFactory = $objectFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Add Subscription Info Summary
     *
     * @param object $subject
     * @param object $product
     *
     * @return $product
     */
    public function beforeAddProduct($subject, $product, $request = null)
    {
        if (!$this->subscriptionHelper->isModuleEnable()) {
            return [$product,$request];
        }
        
        if ($this->isSubscriptionItem($product)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You need to choose subscription options for this item.')
            );
        }

        if ($request === null) {
            $request = 1;
        }
        if (is_numeric($request)) {
            $request = $this->objectFactory->create(['qty' => $request]);
        }

        if (!$request instanceof \Magento\Framework\DataObject) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        
        if ($this->getSubscription()->isAdd($product)) {
            $subscription = $this->getSubscription()->getData($product, $request)->getSubscriptionData();
            
            if ($request) {
                $options = $request->getData('options');
                $options['_1'] = 'subscription';
                $request->setData('options', $options);
            }
            
            if ($request && !$request->getSubscriptionStartDate()) {
                $request->setData('subscription_start_date', $subscription->getSubscriptionStartDate());
            }

            $additionalInfo = $this->getSubscription()->getBuildInfo($subscription, $request);
            $product->addCustomOption('additional_options', $additionalInfo);
        }
    }

    /**
     * Get Subscription Model
     *
     * @return Magedelight\Subscribenow\Model\Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }
    
    private function isSubscriptionItem($product = null)
    {
        $orderId = $this->request->getParam('order_id');
        $isSubscribeItem = false;
        
        if ($orderId && $product && $product->getIsSubscription()
            && $this->request->getFullActionName() == 'sales_order_reorder'
        ) {
            $order = $this->orderRepository->get($orderId);
            $items = $order->getItems();
            
            foreach ($items as $item) {
                if ($this->getSubscription()->getService()->isSubscribed($item)) {
                    $isSubscribeItem = true;
                    break;
                }
            }
        }
        return $isSubscribeItem;
    }
}
