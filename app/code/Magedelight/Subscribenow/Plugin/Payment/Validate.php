<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Plugin\Payment;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magedelight\Subscribenow\Helper\Data as SubscribeHelper;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;

class Validate
{

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var SubscribeHelper
     */
    private $subscribeHelper;
    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @param CheckoutSession $checkoutSession
     * @param SubscribeHelper $subscribeHelper
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        SubscribeHelper $subscribeHelper,
        SubscriptionService $subscriptionService
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->subscribeHelper = $subscribeHelper;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @return bool
     */
    public function hasSubscriptionProduct()
    {
        $bool = false;
        if ($this->subscribeHelper->isModuleEnable()) {
            $allItems = $this->checkoutSession->getQuote()->getAllItems();
            foreach ($allItems as $item) {
                if ($this->subscriptionService->isSubscribed($item)) {
                    $bool = true;
                    break;
                }
            }
        }
        return $bool;
    }
}
