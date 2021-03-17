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

namespace Magedelight\Subscribenow\Model\Service;

use Magedelight\Subscribenow\Helper\Data as SubscriptionHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magedelight\Subscribenow\Model\Source\SubscriptionStart;
use Magedelight\Subscribenow\Model\Source\DiscountType;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Directory\Model\PriceCurrency;
use Magedelight\Subscribenow\Model\Source\PurchaseOption;
use Magento\Framework\Exception\LocalizedException;

class SubscriptionService
{

    /**
     * @var SubscriptionHelper
     */
    private $subscriptionHelper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * @var DataObjectFactory
     */
    private $dataObject;

    /**
     * @var DataObjectFactory
     */
    private $subscriptionObject;

    /**
     * @var Json
     */
    private $serialize;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var PriceCurrency
     */
    private $priceCurrency;

    /**
     * SubscriptionService constructor.
     * @param SubscriptionHelper $subscriptionHelper
     * @param DataObjectFactory $dataObject
     * @param Json $serializer
     * @param TimezoneInterface $timezone
     * @param PriceCurrency $priceCurrency
     */
    public function __construct(
        SubscriptionHelper $subscriptionHelper,
        DataObjectFactory $dataObject,
        Json $serializer,
        TimezoneInterface $timezone,
        PriceCurrency $priceCurrency
    ) {
        $this->subscriptionHelper = $subscriptionHelper;
        $this->dataObject = $dataObject;
        $this->serialize = $serializer;
        $this->timezone = $timezone;
        $this->priceCurrency = $priceCurrency;
    }
    
    /**
     * @return \Magento\Catalog\ModelProduct
     */
    private function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \Magento\Catalog\ModelProduct $product
     * @return $this
     */
    private function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
    
    /**
     * Create Subscription Object
     * @return $this
     */
    private function intSubscriptionObject()
    {
        $this->subscriptionObject = $this->dataObject->create();
        return $this;
    }
    
    /**
     * Set Subscription Data Object
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    private function setSubscriptionData($key, $value)
    {
        $this->subscriptionObject->setData($key, $value);
    }
    
    /**
     * Get Subscription Data
     * @return object
     */
    public function getSubscriptionData()
    {
        return $this->subscriptionObject;
    }
    
    /**
     * Is Subscription Product
     * @return int
     */
    public function getIsSubscription()
    {
        return $this->getProduct()->getIsSubscription();
    }
    
    /**
     * Subscription Type
     * subscription|either
     *
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->getProduct()->getSubscriptionType();
    }
    
    /**
     * Discount Type
     * fixed|percentage
     *
     * @return string
     */
    public function getDiscountType()
    {
        return $this->getProduct()->getDiscountType();
    }
    
    /**
     * Discount Amount
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getProduct()->getDiscountAmount();
    }
    
    /**
     * Get Initial Amount
     * @return float
     */
    public function getInitialAmount()
    {
        return $this->getProduct()->getInitialAmount();
    }
    
    /**
     * Get Subscription Start From
     * @return mixed
     */
    public function getDefineStartFrom()
    {
        return $this->getProduct()->getDefineStartFrom();
    }
    
    /**
     * Get Day of month
     * @return mixed
     */
    public function getDayOfMonth()
    {
        return $this->getProduct()->getDayOfMonth();
    }
    
    /**
     * Get Billing Period Type
     * @return mixed
     */
    public function getBillingPeriodType()
    {
        return $this->getProduct()->getBillingPeriodType();
    }
    
    /**
     * Get Billing Period Time
     * @return mixed
     */
    public function getBillingPeriod()
    {
        return $this->getProduct()->getBillingPeriod();
    }
    
    /**
     * Get Trial Period Time
     * @return mixed
     */
    public function getTrialPeriod()
    {
        return $this->getProduct()->getTrialPeriod();
    }
    
    /**
     * Allow Update Date
     * @return boolean
     */
    public function getAllowUpdateDate()
    {
        return $this->getProduct()->getAllowUpdateDate();
    }
    
    /**
     * Get Billing Maximum Cycles
     * @return mixed
     */
    public function getBillingMaxCycles()
    {
        return $this->getProduct()->getBillingMaxCycles();
    }

    /**
     * Is Allow Trial
     * @return int
     */
    public function getAllowTrial()
    {
        if ($this->getProduct()->getAllowTrial() && $this->getTrialAmount() > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * Get Trial Max Cycle
     * @return int
     */
    public function getTrialMaxcycle()
    {
        return $this->getProduct()->getTrialMaxcycle();
    }
    
    /**
     * Get Trial Amount
     * @return float
     */
    public function getTrialAmount()
    {
        return $this->getProduct()->getTrialAmount();
    }
    
    /**
     * Get Subscription Start Date
     * @return mixed
     */
    public function getSubscriptionStartDate()
    {
        $date = $this->timezone->date();
        
        if ($this->getDefineStartFrom() == SubscriptionStart::MOMENT) {
            return $date->format('Y-m-d');
        }

        if ($this->getDefineStartFrom() == SubscriptionStart::LAST_DAY_MONTH) {
            return $date->format('Y-m-t');
        }

        if ($this->getDefineStartFrom() == SubscriptionStart::EXACT_DAY) {
            $day = ($this->getDayOfMonth()) ? $this->getDayOfMonth() : 'd';
            return $date->format('Y-m-' . $day);
        }
    }
    
    /**
     * Get Subscription Interval
     * @return null|array
     */
    public function getSubscriptionInterval($key = null)
    {
        $interval = $this->subscriptionHelper->getSubscriptionInterval();

        if (!empty($interval) && array_key_exists($key, $interval)) {
            return $interval[$key];
        } elseif (!empty($interval)) {
            return reset($interval);
        }
    }

    /**
     * Set Product Final Price
     * @param float $price
     * @return float
     */
    public function setProductFinalPrice($price)
    {
        return $this->getProduct()->setData('finalPrice', $price);
    }

    /**
     * Get Product Final Price
     * @return float
     */
    public function getProductFinalPrice()
    {
        return $this->getProduct()->getData('final_price');
    }
    
    /**
     * Set Subscription Interval Data
     * @return void
     */
    private function setSubscriptionIntervalData()
    {
        $billingPeriod = $this->getBillingPeriod();
        $subscriptionInterval = $this->getSubscriptionInterval($billingPeriod);
        
        $this->setSubscriptionData('billing_period', $subscriptionInterval['interval_type']);
        $this->setSubscriptionData('billing_frequency', $subscriptionInterval['no_of_interval']);
        $this->setSubscriptionData('billing_frequency_label', $subscriptionInterval['interval_label']);
    }
    
    /**
     * Set Trial Period Data
     * @return void
     */
    private function setTrialData()
    {
        $this->setSubscriptionData('allow_trial', $this->getAllowTrial());
        if ($this->getAllowTrial()) {
            $this->setSubscriptionData('trial_max_cycle', $this->getTrialMaxcycle());
            $this->setSubscriptionData('trial_amount', $this->getTrialAmount());
            
            $trialPeriod = $this->getTrialPeriod();
            $trialInterval = $this->getSubscriptionInterval($trialPeriod);
            
            $this->setSubscriptionData('trial_period', $trialInterval['interval_type']);
            $this->setSubscriptionData('trial_frequency', $trialInterval['no_of_interval']);
            $this->setSubscriptionData('trial_period_label', $trialInterval['interval_label']);
        }
    }

    /**
     * @param $product
     * @param array|null $request
     * @return $this
     */
    public function getProductSubscriptionDetails($product, $request = null)
    {
        
        $this->setProduct($product)->intSubscriptionObject();
        
        $this->setSubscriptionData('is_subscription', $this->getIsSubscription());
        $this->setSubscriptionData('subscription_type', $this->getSubscriptionType());
        $this->setSubscriptionData('initial_amount', $this->getInitialAmount());
        $this->setSubscriptionData('define_start_from', $this->getDefineStartFrom());
        $this->setSubscriptionData('subscription_start_date', $this->getSubscriptionStartDate());
        $this->setSubscriptionData('billing_period_type', $this->getBillingPeriodType());
        $this->setSubscriptionData('billing_max_cycles', $this->getBillingMaxCycles());
        $this->setSubscriptionData('allow_update_date', $this->getAllowUpdateDate());
        
        $this->setSubscriptionIntervalData();
        $this->setTrialData();
        
        $this->getSubscriptionDiscountAmount();
        $this->buildRequest($request);

        return $this;
    }
    
    /**
     * Subscription Amount
     * @return int|float
     */
    public function getSubscriptionDiscountAmount($amount = null)
    {
        $baseDiscountAmount = 0;
        $discountAmount = 0;
        $productPrice = ($amount)?:$this->getProductFinalPrice();

        if ($this->getDiscountAmount()) {
            $baseDiscountAmount = $this->getDiscountAmount();

            if ($this->getDiscountType() == DiscountType::PERCENTAGE) {
                $baseDiscountAmount = $productPrice * ($this->getDiscountAmount() / 100);
            }

            $discountAmount = $this->getConvertedPrice($baseDiscountAmount);
        }

        $this->setSubscriptionData('discount_type', $this->getDiscountType());
        $this->setSubscriptionData('base_discount_amount', $baseDiscountAmount);
        $this->setSubscriptionData('discount_amount', $discountAmount);

        return $baseDiscountAmount;
    }

    /**
     * Overwrite existing billing data
     *
     * @param array $request
     * @return void
     */
    private function setQuoteBillingPeriod($request = [])
    {
        if ($request && isset($request['billing_period']) && $request['billing_period']) {
            $subscriptionInterval = $this->getSubscriptionInterval($request['billing_period']);

            $this->setSubscriptionData('billing_period', $subscriptionInterval['interval_type']);
            $this->setSubscriptionData('billing_frequency', $subscriptionInterval['no_of_interval']);
            $this->setSubscriptionData('billing_frequency_label', $subscriptionInterval['interval_label']);
        }
    }
    
    /**
     * Set Requested Payload
     * @param type $request
     */
    private function buildRequest($request)
    {
        $requestPayload = [];
        $requestPayload['is_subscription'] = $this->checkProductRequest($request) ? 1 : 0;

        if (isset($requestPayload['is_subscription']) && $requestPayload['is_subscription'] == 1) {
            $this->setQuoteBillingPeriod($request);
        }

        $this->setSubscriptionData('request_payload', $requestPayload);
    }

    /**
     * Get Converted Price with multi-currency
     * @param mixed $amount
     * @return mixed
     */
    public function getConvertedPrice($amount)
    {
        return $this->priceCurrency->convert($amount);
    }
    
    /**
     * Get Billing Maximum Cycle Label
     * @return string
     */
    public function getBillingMaxCyclesLabel()
    {
        return ($this->getSubscriptionData()->getBillingMaxCycles())?
            __('%1 times(s)', $this->getSubscriptionData()->getBillingMaxCycles()):
            __('Repeats until failed or canceled');
    }

    /**
     * Get Trial Maximum Cycle Label
     * @return string
     */
    public function getTrialMaxCycleLabel()
    {
        $trialLabel = ($this->getSubscriptionData()->getTrialMaxCycle())?
            __('%1 times(s)', $this->getSubscriptionData()->getTrialMaxCycle()):
            __('Repeats until failed or canceled');
        return $trialLabel;
    }
    
    /**
     * @param object $item CartItem
     * @param array $request
     * @return boolean
     */
    public function isSubscribed($item)
    {
        $request = $item->getBuyRequest()->getData();
        return $this->checkProductRequest($request, $item);
    }
    
    /**
     * Check product is subscription
     * @param object $item
     * @return boolean
     */
    public function isSubscriptionProduct($item)
    {
        return ($item->getProduct()->getIsSubscription() && $item->getProduct()->getSubscriptionType() == PurchaseOption::SUBSCRIPTION);
    }

    /**
     * is Requested Product Subscription
     * @param type $param
     * @return type
     */
    public function checkProductRequest($param = null, $item = null)
    {
        if (isset($param) &&
                isset($param['options']['_1']) &&
                $param['options']['_1'] == PurchaseOption::SUBSCRIPTION
        ) {
            return true;
        } elseif ($item && $this->isSubscriptionProduct($item)) {
            return true;
        }
        return false;
    }
    
    /**
     * Validating Item on Add to cart time
     * @param Object $item
     * @return mixed
     * @throws LocalizedException
     */
    public function validate($item, $cartRequest)
    {
        $this->validateQty($item, $cartRequest);
    }
    
    /**
     * Validate Item Qty
     *
     * @param type $item
     * @throws LocalizedException
     */
    public function validateQty($item)
    {
        $allowedQty = $this->subscriptionHelper->getMaxAllowedQty();
        
        $itemQty = $item->getQty();
        
        if ($allowedQty && $itemQty > $allowedQty) {
            $errorMessage = $this->subscriptionHelper->getQtyErrorMessage();
            throw new LocalizedException($errorMessage);
        }
    }
    
     /**
      * Check current product
      * subscription date is greater than today
      *
      * @param \Magento\Catalog\Model\Product $product
      * @return boolean
      */
    public function isFutureSubscription($product)
    {
        if ($product->getCustomOption('info_buyRequest') && $product->getCustomOption('info_buyRequest')->getValue()) {
            $infoBuyRequest = $this->serialize->unserialize($product->getCustomOption('info_buyRequest')->getValue());
            if ($this->isFutureItem($infoBuyRequest)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function isFutureItem($request = null)
    {
        if ($request && is_object($request)) {
            $request = $request->getData();
        }
        
        if ($this->checkProductRequest($request) && count($request) > 0) {
            $currentDate = $this->timezone->date()->format('Y-m-d');
            $requestDate = (string) isset($request['subscription_start_date']) ? $request['subscription_start_date'] : $currentDate;
            $subscriptionStartDate = $this->timezone->date($requestDate, null, false)->format('Y-m-d');
            if ($currentDate !== $requestDate && $subscriptionStartDate > $currentDate) {
                return true;
            }
        }
        
        return false;
    }
}
