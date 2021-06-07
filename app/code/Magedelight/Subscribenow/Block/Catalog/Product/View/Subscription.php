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

namespace Magedelight\Subscribenow\Block\Catalog\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;
use Magedelight\Subscribenow\Helper\Data as SubscriptionHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magedelight\Subscribenow\Model\Source\PurchaseOption;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Catalog\Model\Product\Type as ProductType;

class Subscription extends Template
{
    const TYPE_CONFIGURABLE = 'configurable';

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * @var SubscriptionService
     */
    protected $subscriptionService;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var array
     */
    protected $info = [];

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Json
     */
    protected $serialize;

    /**
     * Subscription constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param SubscriptionHelper $subscriptionHelper
     * @param SubscriptionService $subscriptionService
     * @param PriceHelper $priceHelper
     * @param CheckoutSession $checkoutSession
     * @param Json $serialize
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        SubscriptionHelper $subscriptionHelper,
        SubscriptionService $subscriptionService,
        priceHelper $priceHelper,
        CheckoutSession $checkoutSession,
        Json $serialize,
        array $data = []
    ) {

        $this->coreRegistry = $registry;
        $this->subscriptionHelper = $subscriptionHelper;
        $this->subscriptionService = $subscriptionService;
        $this->priceHelper = $priceHelper;
        $this->checkoutSession = $checkoutSession;
        $this->serialize = $serialize;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    public function canDisplaySubscription()
    {
        return $this->subscriptionHelper->isModuleEnable() && $this->isSubscriptionProduct();
    }

    public function canDisplayContent()
    {
        return $this->getSubscription()->getSubscriptionType() == PurchaseOption::SUBSCRIPTION
            || $this->isSubscriptionChecked();
    }

    public function isSubscriptionProduct()
    {
        return $this->getProduct()->getIsSubscription();
    }

    public function isCartEdit()
    {
        $handles = $this->getLayout()->getUpdate()->getHandles();
        return in_array('checkout_cart_configure', $handles);
    }

    private function getItemBuyRequest()
    {
        $itemId = $this->getRequest()->getParam('id');
        $items = $this->checkoutSession->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item->getId() == $itemId) {
                return $item->getOptionByCode('info_buyRequest')->getValue();
            }
        }
    }

    public function getRequestedParams()
    {
        $request = null;
        if ($this->isCartEdit()) {
            $buyRequest = $this->getItemBuyRequest();
            if ($buyRequest) {
                $request = $this->serialize->unserialize($buyRequest);
            }
        }
        return $request;
    }

    public function isSubscriptionChecked()
    {
        $request = $this->getSubscription()->getRequestPayload();
        return isset($request['is_subscription']) && $request['is_subscription'] == 1;
    }

    public function getSubscription()
    {
        $product = $this->getProduct();
        $request = $this->getRequestedParams();
        $subscriptionData = $this->subscriptionService
            ->getProductSubscriptionDetails($product, $request)
            ->getSubscriptionData();
        return $subscriptionData;
    }

    public function canPurchaseSeparately()
    {
        return $this->getSubscription()->getSubscriptionType() == PurchaseOption::EITHER;
    }
    
    public function isBundle()
    {
        if ($this->getProduct()->getTypeId() == ProductType::TYPE_BUNDLE) {
            return true;
        }
        return false;
    }

    public function isConfigurable()
    {
        if ($this->getProduct()->getTypeId() == self::TYPE_CONFIGURABLE) {
            return true;
        }
        return false;
    }

    public function getDiscountConfig()
    {
        $amount = ($this->getSubscription()->getDiscountType() == 'fixed')
                ? $this->getSubscription()->getBaseDiscountAmount()
                : (float) $this->getProduct()->getDiscountAmount();
        
        return $this->serialize->serialize([
            "product_type" => $this->getProduct()->getTypeId(),
            "subscription" => $this->getSubscription()->getIsSubscription(),
            "subscription_type" => $this->getSubscription()->getSubscriptionType(),
            "discount" => $amount,
            "discount_type" => $this->getSubscription()->getDiscountType(),
        ]);
    }
    
    public function getConfigDiscountAmount() {
        $amount = $this->getDiscountAmount();
        
        if ($this->getSubscription()->getDiscountType() != 'fixed' 
            && $this->isConfigurable()) {
            $amount = 0;
        }
        
        return $amount;
    }
    
    public function getDiscountAmount($format = false)
    {
        $productPrice = $this->getProduct()->getData('final_price');
        $discount = $this->getSubscription()->getBaseDiscountAmount();
        
        if ($this->getSubscription()->getDiscountType() != 'fixed' && $format) {
            return (float) $this->getProduct()->getDiscountAmount() .'%';
        }
        
        if ($this->isBundle()) {
            return $this->priceHelper->currency($discount, $format);
        }
        
        if (0 > ($productPrice - $discount)) {
            $discount = $productPrice;
        }
        
        if ($this->getProduct()->getTypeId() == self::TYPE_CONFIGURABLE && $this->getSubscription()->getDiscountType() != 'fixed') {
            return $discount;
        }
        
        return $this->priceHelper->currency($discount, $format);
    }

    public function getInitialAmount($format = false)
    {
        return $this->priceHelper->currency($this->getSubscription()->getInitialAmount(), $format);
    }

    public function getTrialAmount($format = false)
    {
        if ($this->getSubscription()->getTrialAmount()) {
            return $this->priceHelper->currency($this->getSubscription()->getTrialAmount(), $format);
        }
        return 0;
    }

    public function getSubscriptionLabel()
    {
        $subscriptionWithDiscount = __("Subscribe with discount - %1", $this->getDiscountAmount(true));
                
        if ($this->isBundle()) {
            if ($this->getSubscription()->getDiscountType() == 'fixed') {
                $subscriptionWithDiscount = __(
                    "Subscribe with discount - %1 <br> &emsp; (on every child products)",
                    $this->getDiscountAmount(true)
                );
            } else {
                $subscriptionWithDiscount = __(
                    "Subscribe with discount - %1 <br> &emsp; (Percentage calculate on every child products)",
                    $this->getDiscountAmount(true)
                );
            }
        }
        
        return ($this->getDiscountAmount()) ? $subscriptionWithDiscount : __("Subscribe this product");
    }

    public function getJsonConfig()
    {
        $config = [];
        $config['_1']['subscription'] = [
            'prices' => [
                'oldPrice' => [
                    'amount' => 0,
                    'adjustments' => [],
                ],
                'basePrice' => [
                    'amount' => '-' . $this->getConfigDiscountAmount(),
                ],
                'finalPrice' => [
                    'amount' => '-' . $this->getConfigDiscountAmount(),
                ],
            ],
            'type' => 'fixed',
            'name' => 'Subscribe This Product',
        ];
        
        return $this->serialize->serialize($config);
    }
}
