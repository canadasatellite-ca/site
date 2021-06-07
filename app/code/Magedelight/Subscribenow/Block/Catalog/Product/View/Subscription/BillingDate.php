<?php

/**
 * Magedelight
 * Copyright (C) 2018 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Block\Catalog\Product\View\Subscription;

use Magedelight\Subscribenow\Block\Catalog\Product\View\Subscription;
use Magedelight\Subscribenow\Helper\Data as SubscriptionHelper;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data as priceHelper;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class BillingDate extends Subscription
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * BillingDate constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param SubscriptionHelper $subscriptionHelper
     * @param SubscriptionService $subscriptionService
     * @param priceHelper $priceHelper
     * @param CheckoutSession $checkoutSession
     * @param Json $serialize
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        SubscriptionHelper $subscriptionHelper,
        SubscriptionService $subscriptionService,
        PriceHelper $priceHelper,
        CheckoutSession $checkoutSession,
        Json $serialize,
        TimezoneInterface $timezone,
        array $data = []
    ) {
    
        parent::__construct($context, $registry, $subscriptionHelper, $subscriptionService, $priceHelper, $checkoutSession, $serialize, $data);
        $this->timezone = $timezone;
    }

    public function isCustomerDefined()
    {
        return $this->getSubscription()->getDefineStartFrom() == "defined_by_customer";
    }

    public function getSubscriptionDate()
    {
        return $this->getSubscription()->getSubscriptionStartDate();
    }

    public function getCurrentDate()
    {
        return $this->timezone->date()->format('Y/m/d');
    }
    
    public function getSubscriptionSelectedDate()
    {
        $productEditData = $this->getRequestedParams();
        
        if ($productEditData && isset($productEditData['subscription_start_date'])) {
            return date('Y/m/d', strtotime($productEditData['subscription_start_date']));
        }
        
        return $this->timezone->date()->format('Y/m/d');
    }
}
