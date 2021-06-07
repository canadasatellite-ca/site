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

namespace Magedelight\Subscribenow\Model;

use Magento\Framework\App\Request\Http;
use Magedelight\Subscribenow\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartRepositoryInterface;
use Magedelight\Subscribenow\Model\Source\DiscountType;
use Magedelight\Subscribenow\Model\Source\PurchaseOption;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Bundle\Model\Product\TypeFactory as BundleTypeFactory;
use Magento\Catalog\Model\ProductFactory as ProductModelFactory;
use Magento\Framework\Registry;

/**
 * Subscription
 */
class Subscription
{

    /**
     * Define Table Columns
     */
    const TBL_PRODUCT_SUBSCRIBER = 'md_subscribenow_product_subscribers';
    const TBL_ASSOCIATE_PRODUCT_ORDER = 'md_subscribenow_product_associated_orders';
    const TBL_OCCURENCE_PRODUCT = 'md_subscribenow_product_occurrence';
    const TBL_AGGREGATE_PRODUCT = 'md_subscribenow_aggregated_product';
    const TBL_AGGREGATE_CUSTOMER = 'md_subscribenow_aggregated_customer';
    const TBL_SUBSCRIPTION_HISTORY = 'md_subscribenow_product_subscription_history';

    /**
     * Custom Extension Attribute Columns
     */
    const INIT_AMOUNT_FIELD_NAME = 'subscribenow_init_amount';
    const TRIAL_AMOUNT_FIELD_NAME = 'subscribenow_trial_amount';

    /**
     * @var Data
     */
    public $helper;

    /**
     * @var Json
     */
    public $serialize;

    /**
     * @var SubscriptionService
     */
    public $service;

    /**
     * @var Http
     */
    public $request;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    
    /**
     * @var PriceHelper
     */
    private $priceHelper;
    
    /**
     * @var ProductModelFactory
     */
    private $productModelFactory;
    
    /**
     * @var ProductModelFactory
     */
    private $productModel;
    
    /**
     * @var BundleTypeFactory
     */
    private $bundleTypeFactory;
    
    /**
     * @var BundleTypeFactory
     */
    private $bundleType;

    private $childProduct = null;
    private $hasParent = false;
    private $parentProduct = null;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Subscription constructor
     *
     * @param Data $helper
     * @param Json $serialize
     * @param SubscriptionService $service
     * @param Http $request
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param PriceHelper $priceHelper
     */
    public function __construct(
        Data $helper,
        Json $serialize,
        SubscriptionService $service,
        Http $request,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        PriceHelper $priceHelper,
        ProductModelFactory $productModelFactory,
        BundleTypeFactory $bundleTypeFactory,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->serialize = $serialize;
        $this->service = $service;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->priceHelper = $priceHelper;
        $this->productModelFactory = $productModelFactory;
        $this->bundleTypeFactory = $bundleTypeFactory;
        $this->registry = $registry;
    }

    /**
     * Get Discounted Final Price
     *
     * @param Object $product
     * @param float $finalPrice
     *
     * @return float Price
     */
    public function getFinalPrice($product, $finalPrice)
    {
        if (!$this->helper->isModuleEnable()) {
            return $finalPrice;
        }

        return $this->getSubscriptionDiscount($finalPrice, $product);
    }

    /**
     * @param float $finalPrice
     * @param object $product
     * @param bool $convert
     * @return float Price
     */
    public function getSubscriptionDiscount($finalPrice, $product, $convert = false)
    {
        $this->hasParent = false;
        $this->parentProduct = null;
        
        $optionPrice = $this->getOptionPrice($product);
        $price = $finalPrice;

        if ($this->helper->isModuleEnable() && $this->isSubscriptionProduct($product)) {
            $price = $finalPrice - $optionPrice;
            $type = $this->getDiscountType($product);

            $discount = ($convert) ? $this->service->getConvertedPrice($this->getDiscountAmount($product)) : $this->getDiscountAmount($product);

            if ($type == DiscountType::PERCENTAGE) {
                $percentageAmount = $price * ($discount / 100);
                $price = $price - $percentageAmount;
            } else {
                $price = $price - $discount;
            }

            $price += $optionPrice;
            
            if ($product->hasSkipValidateTrial() && $product->getSkipValidateTrial()) {
                return max(0, $price);
            }

            if ($this->service->isFutureSubscription($product) ||
                ($product->getAllowTrial() && $product->getTrialAmount() > 0)) {
                $product->setCustomPrice(0);
            }

            /** set custom price to product for trial & future items
              && show custom price everywhere excluding product detail page */
            if ($product->hasCustomPrice() && $this->request->getRouteName() != 'catalog') {
                $price = $product->getCustomPrice(0);
            }
        }

        if ($product->hasSkipDiscount() && $product->getSkipDiscount()) {
            return $price - $optionPrice;
        }
        
        return max(0, $price);
    }

    private function getOptionPrice($product)
    {
        $finalPrice = 0;
        $optionIds = $product->getCustomOption('option_ids');
        if ($optionIds) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }

        return $finalPrice;
    }

    private function getDiscountType($product)
    {
        if ($this->hasParent) {
            return $this->parentProduct->getDiscountType();
        }
        return $product->getDiscountType();
    }

    private function getDiscountAmount($product)
    {
        if ($this->hasParent) {
            return $this->parentProduct->getDiscountAmount();
        }
        return $product->getDiscountAmount();
    }

    /**
     * @return SubscriptionService
     */
    public function getService()
    {
        return $this->service;
    }
    
    private function getBundleParentId($product)
    {
        if (!$this->bundleType) {
            $this->bundleType = $this->bundleTypeFactory->create();
        }
        $ids = $this->bundleType->getParentIdsByChild($product->getId());
        return ($ids && isset($ids[0])) ? $ids[0] : null;
    }
    
    private function getProductModel($parentId)
    {
        if (!$this->productModel) {
            $this->productModel = $this->productModelFactory->create();
        }
        return $this->productModel->load($parentId);
    }

    /**
     * If Valid Data
     * Show Subscription Price
     *
     * @param type $product
     * @return boolean
     */
    public function isSubscriptionProduct($product)
    {
        $parentId = $this->getBundleParentId($product);
        if ($parentId) {
            $this->childProduct = $product;
        }
        
        if ($product && $parentId) {
            $parentProduct = $this->getProductModel($parentId);

            if ($this->isSubscriptionProduct($parentProduct)) {
                $this->hasParent = true;
                $this->parentProduct = $parentProduct;
                
                if ($this->service->isFutureSubscription($parentProduct)
                    || ($parentProduct->getAllowTrial() && $parentProduct->getTrialAmount() > 0 && $this->isProfileInTrial())
                ) {
                    $product->setCustomPrice(0); // Set child product price to zero
                }
                return true;
            }
        }
        
        if ($product->hasSkipDiscount() && $product->getSkipDiscount()) {
            return false;
        }
        
        if ($product->hasSkipValidateTrial() && $product->getSkipValidateTrial()) {
            return true;
        }
        
        $isSubscription = $product->getIsSubscription();
        $subscriptionType = $product->getSubscriptionType();
        
        if ($isSubscription && $subscriptionType == PurchaseOption::SUBSCRIPTION) {
            return true;
        } elseif ($isSubscription && $this->isProductWithSubscriptionOption($product)) {
            return true;
        }
        
        return false;
    }

    private function getCurrentProfile()
    {
        return $this->registry->registry('current_profile');
    }

    private function isProfileInTrial()
    {
        $profile = $this->getCurrentProfile();
        if (!$profile) {
            return true;
        }

        if ($profile->getIsTrial() && $profile->isTrialPeriod()) {
            return true;
        }
        return false;
    }

    /**
     * Check Current Product have Subscription Option
     *
     * @param $product
     * @return boolean
     */
    private function isProductWithSubscriptionOption($product)
    {
        $infoRequest = $product->getCustomOption('info_buyRequest');
        
        if ((!$infoRequest || !$infoRequest->getValue()) && $this->childProduct) {
            $infoRequest = $this->childProduct->getCustomOption('info_buyRequest');
        }
        
        if ($infoRequest) {
            $requestData = $this->serialize->unserialize($infoRequest->getValue());
            if ($this->service->checkProductRequest($requestData)) {
                return true;
            }
        }
        return false;
    }
    
    private function getOptionPriceHtml($amount)
    {
        if ($amount) {
            return sprintf('<span class="price">%s</span>', $amount);
        }
        return "";
    }

    /**
     * Build Cart Summary
     *
     * @param type $subscription
     * @return type
     */
    public function getBuildInfo($subscription, $request)
    {
        $info[] = [
            'label' => $this->helper->getBillingPeriodTitle(),
            'value' => $this->getBillingPeriod($subscription)
        ];
        
        $info[] = [
            'label' => $this->helper->getBillingCycleTitle(),
            'value' => $this->getBillingCycle($subscription)
        ];

        if ($subscription->getInitialAmount() > 0) {
            $info[] = [
                'code' => 'init_amount',
                'label' => $this->helper->getInitAmountTitle(),
                'value' => $this->getOptionPriceHtml($this->getInitialAmount($subscription)),
                'has_html' => true,
            ];
        }
        
        if ($subscription->getAllowTrial() && !$this->service->isFutureItem($request)) {
            $info[] = [
                'code' => 'trial_amount',
                'label' => $this->helper->getTrialAmountTitle(),
                'value' => $this->getOptionPriceHtml($this->getTrialAmount($subscription)),
                'has_html' => true,
            ];

            $info[] = [
                'label' => $this->helper->getTrialPeriodTitle(),
                'value' => $this->getTrialPeriod($subscription)
            ];
            
            $info[] = [
                'label' => $this->helper->getTrialCycleTitle(),
                'value' => $this->getTrialCycle($subscription)
            ];
        }

        $info[] = [
            'code' => 'md_sub_start_date',
            'label' => $this->helper->getSubscriptionStartDateTitle(),
            'value' => $this->getSubscriptionStartDate($subscription),
        ];

        return $this->serialize->serialize($info);
    }

    /**
     * Get Initial Amount with formatted price
     * @param $subscription
     * @return mixed
     */
    private function getInitialAmount($subscription)
    {
        return $this->priceHelper->currency($subscription->getInitialAmount(), true);
    }

    /**
     * Get Trial Amount with formatted price
     * @param $subscription
     * @return mixed
     */
    private function getTrialAmount($subscription)
    {
        if ($subscription->getTrialAmount()) {
            return $this->priceHelper->currency($subscription->getTrialAmount(), true);
        }
        return $this->priceHelper->currency(0.00);
    }

    /**
     * Trial Period
     *
     * @param object $subscription
     * @return string
     */
    private function getTrialPeriod($subscription)
    {
        return $subscription->getTrialPeriodLabel();
    }

    /**
     * Trial Period Cycle
     *
     * @param object $subscription
     * @return string
     */
    private function getTrialCycle($subscription)
    {
        return ($subscription->getTrialMaxCycle()) ? __('%1 times(s)', $subscription->getTrialMaxCycle()) : __('Repeats until failed or canceled');
    }
    
    /**
     * Subscription Start Date
     *
     * @param object $subscription
     * @return string
     */
    private function getSubscriptionStartDate($subscription)
    {
        if ($subscription->getDefineStartFrom() == "defined_by_customer") {
            return $this->request->getPostValue('subscription_start_date');
        }
        return $this->service->getSubscriptionStartDate();
    }

    /**
     * Get Billing Period
     *
     * @param object $subscription
     * @return string
     */
    private function getBillingPeriod($subscription)
    {
        
        if ($subscription->getBillingPeriodType() == 'customer') {
            $billingFrequency = $this->helper->getIntervalLabel($this->request->getPostValue('billing_period'));
        } else {
            $billingFrequency = $subscription->getBillingFrequencyLabel();
        }

        return ucfirst($billingFrequency);
    }
    
    /**
     * Get Billing Cycle
     *
     * @param object $subscription
     * @return string
     */
    private function getBillingCycle($subscription)
    {
        return ($subscription->getBillingMaxCycles())?
            __('Repeat %1 times(s)', $subscription->getBillingMaxCycles()):
            __("Repeats until failed or canceled");
    }

    /**
     * Get Subscription Object
     *
     * @param object $product
     * @return object
     */
    public function getData($product, $request = null)
    {
        return $this->service->getProductSubscriptionDetails($product, $request);
    }

    /**
     * Check available product is valid to
     * add as subscription product
     *
     * @param $product
     * @return boolean
     */
    public function isAdd($product)
    {
        $params = $this->request->getParams();

        $isSubscription = $product->getIsSubscription();
        $subscriptionType = $product->getSubscriptionType();

        if ($isSubscription && $subscriptionType == PurchaseOption::SUBSCRIPTION) {
            return true;
        } elseif ($this->service->checkProductRequest($params)) {
            return true;
        } elseif ($this->isProductWithSubscriptionOption($product)) {
            return true;
        }

        return false;
    }
    
    /**
     * Check if product is valid to buy from listing
     * and return product page url if not valid.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return boolean
     */
    public function isValidBuyFromList($product)
    {
        $isSubscriptionProduct = $product->getIsSubscription();
        $productSubscriptionType = $product->getSubscriptionType();
        $defineStartFrom = $product->getDefineStartFrom();
        $billingPeriodDefineBy = $product->getBillingPeriodType();
        
        if ($this->request->getFullActionName() != 'catalog_product_view'
            && $isSubscriptionProduct == '1'
            && ($productSubscriptionType == PurchaseOption::EITHER
            || $defineStartFrom == "defined_by_customer"
            || $billingPeriodDefineBy == "customer")
        ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check cart have subscription item
     * @return boolean
     */
    public function cartHasSubscriptionItem()
    {
        $result = false;

        $items = $this->checkoutSession->getQuote()->getAllItems();

        foreach ($items as $item) {
            if ($this->service->isSubscribed($item)) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
