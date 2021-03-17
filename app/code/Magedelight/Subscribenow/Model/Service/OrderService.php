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

use Magedelight\Subscribenow\Helper\Data as subscriptionHelper;
use Magedelight\Subscribenow\Model\ProductSubscribersFactory as SubscriptionFactory;
use Magento\Eav\Model\Entity\Increment\NumericValue;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magedelight\Subscribenow\Model\Service\Order\Generate;
use Magedelight\Subscribenow\Logger\Logger;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magedelight\Subscribenow\Model\ProductSubscriptionHistory;

class OrderService
{

    private $subscriptionHelper;

    private $subscriptionFactory;

    private $subscriptionService;

    private $numericValue;

    /**
     * @var \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    private $subscriptionModel;
    /**
     * @var PaymentService
     */
    private $paymentService;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var array
     */
    public $intervalType = [
        'day' => 1,
        'week' => 2,
        'month' => 3,
        'year' => 4,
    ];
    /**
     * @var Generate
     */
    private $generate;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;
    
    private $additionalInfoData = [];

    /**
     * OrderService constructor.
     * @param subscriptionHelper $subscriptionHelper
     * @param SubscriptionFactory $subscriptionFactory
     * @param SubscriptionService $subscriptionService
     * @param NumericValue $numericValue
     * @param PaymentService $paymentService
     * @param TimezoneInterface $timezone
     * @param CartRepositoryInterface $cartRepository
     * @param Generate $generate
     */
    public function __construct(
        subscriptionHelper $subscriptionHelper,
        SubscriptionFactory $subscriptionFactory,
        SubscriptionService $subscriptionService,
        NumericValue $numericValue,
        PaymentService $paymentService,
        TimezoneInterface $timezone,
        CartRepositoryInterface $cartRepository,
        Generate $generate,
        Logger $logger,
        EventManager $eventManager
    ) {
    
        $this->subscriptionHelper = $subscriptionHelper;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionService = $subscriptionService;
        $this->numericValue = $numericValue;
        $this->paymentService = $paymentService;
        $this->timezone = $timezone;
        $this->generate = $generate;
        $this->cartRepository = $cartRepository;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
    }

    public function getSubscriptionModel()
    {
        return $this->subscriptionModel;
    }

    private function initSubscriptionModel()
    {
        $this->subscriptionModel = $this->subscriptionFactory->create();
        return $this;
    }

    public function getSubscriptionService($product, $request)
    {
        return $this->subscriptionService->getProductSubscriptionDetails($product, $request);
    }

    public function createSubscriptionOrder($subscription, $modifiedBy = ProductSubscriptionHistory::HISTORY_BY_ADMIN)
    {
        $subscription->setModifiedBy($modifiedBy);
        try {
            $this->logger->info("Process start for subscription profile # " . $subscription->getProfileId());
            $this->generate->setProfile($subscription)->generateOrder();
            $this->logger->info("Process successfully end for subscription profile # " . $subscription->getProfileId());
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->orderFailed($subscription, $ex->getMessage(), null, $modifiedBy);
            throw new \Exception($ex->getMessage());
        } catch (\Exception $ex) {
            $this->orderFailed($subscription, null, $ex, $modifiedBy);
            throw new \Exception(__("There was an error when generating subscription order #%1", $subscription->getProfileId()));
        }
        
        return null;
    }
    
    private function orderFailed($subscription, $message = null, $exeception = null, $modifiedBy)
    {
        $comment =__("There was an error when generating subscription order #%1", $subscription->getProfileId());
        $subscription->addHistory($modifiedBy, $comment);
        
        $subscription->updateSubscriptionFailedCount();
        $logMessage = $message;
        
        if (is_null($message)) {
            $logMessage = $exeception->getMessage();
        }
        
        $this->logger->info($logMessage);
        $this->logger->info("Process end with error for subscription profile # " . $subscription->getProfileId());
        
        $this->eventManager->dispatch('subscribenow_subscription_failed', [
            'subscription' => $subscription, 'exceptionMessage' => $message]);
    }

    /**
     * @param $order
     * @param $item
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createSubscriptionProfile($order, $item)
    {
        $quote = $this->cartRepository->get($order->getQuoteId());
        $quoteItem = $quote->getItemById($item->getQuoteItemId());

        $this->initSubscriptionModel()
        ->setOrderInfo($order, $quoteItem)
        ->setItemInfo($order, $quoteItem)
        ->setPaymentInfo($order)
        ->setShippingInfo($order)
        ->setAdditionalInfo();

        $this->getSubscriptionModel()->save();
        $this->getSubscriptionModel()->setProfileId($this->getSubscriptionIncrementId());
        $this->getSubscriptionModel()->save();

        $this->sendEmail($order, $quoteItem);
    }

    private function setAdditionalInfo()
    {
        $this->getSubscriptionModel()
            ->setNextOccurrenceDate($this->getNextOccurenceDate())
            ->setlastBillDate($this->timezone->convertConfigTimeToUtc($this->timezone->date()->format('Y-m-d H:i:s')));
        if (!$this->isFutureOccurence()) {
            if ($this->getSubscriptionModel()->isTrialPeriod()) {
                $this->getSubscriptionModel()->setTrialCount(1);
            } else {
                $this->getSubscriptionModel()->setTotalBillCount(1);
            }
        }
        
        $subscriptionStartDate = $this->getSubscriptionModel()->getSubscriptionStartDate();
        $this->getSubscriptionModel()->setSubscriptionStartDate(
            $this->getUtcDateTime($subscriptionStartDate)
        );
        
        if($this->additionalInfoData) {
            $this->getSubscriptionModel()->setAdditionalInfo($this->additionalInfoData);
        }
    }

    private function getNextOccurenceDate()
    {
        $subscription = $this->getSubscriptionModel();
        $currentTime = $this->timezone->date()->format(' H:i:s');
        $startDate = $subscription->getSubscriptionStartDate() . $currentTime;
        if ($this->isFutureOccurence()) {
            return $this->timezone->convertConfigTimeToUtc($startDate);
        }
        if ($subscription->getIsTrial()) {
            $addTime = '+' . $subscription->getTrialPeriodFrequency() . array_search($subscription->getTrialPeriodUnit(), $this->intervalType);
        } else {
            $addTime = '+' . $subscription->getBillingFrequency() . array_search($subscription->getBillingPeriod(), $this->intervalType);
        }
        $nextCycle = date('Y-m-d H:i:s', strtotime($addTime, strtotime($startDate)));
        return $this->timezone->convertConfigTimeToUtc($nextCycle);
    }

    /**
     * @return bool
     */
    private function isFutureOccurence()
    {
        $startDate = $this->getSubscriptionModel()->getSubscriptionStartDate();
        $today = $this->timezone->date()->format('Y-m-d');
        return $startDate > $today;
    }
    
    private function setShippingInfo($order)
    {
        $this->additionalInfoData['shipping_title'] = $order->getShippingDescription();
        $this->getSubscriptionModel()->setShippingMethodCode($order->getShippingMethod());
        return $this;
    }

    private function setPaymentInfo($order)
    {
        $paymentService = $this->paymentService->get($order);
        $this->getSubscriptionModel()->setPaymentToken($paymentService->getPaymentToken());
        $this->getSubscriptionModel()->setPaymentMethodCode($paymentService->getMethodCode());
        $this->getSubscriptionModel()->setPaymentTitle($paymentService->getTitle());
        return $this;
    }

    public function setItemInfo($order, $item)
    {
        $product = $item->getProduct();
        
        $infoBuyRequest = $item->getBuyRequest()->getData();
        
        $this->getSubscriptionModel()->setOrderItemInfo($infoBuyRequest);
        $this->getSubscriptionModel()->setSubscriptionStartDate($infoBuyRequest['subscription_start_date']);

        $subscriptionService = $this->getSubscriptionService($product, $infoBuyRequest);
        $subscriptionData = $subscriptionService->getSubscriptionData();

        $this->getSubscriptionModel()->setBillingPeriodLabel($subscriptionData->getData('billing_frequency_label'));
        $this->getSubscriptionModel()->setBillingFrequency($subscriptionData->getData('billing_frequency'));
        $this->getSubscriptionModel()->setBillingPeriod($this->intervalType[$subscriptionData->getBillingPeriod()]);
        $this->getSubscriptionModel()->setPeriodMaxCycles($subscriptionData->getData('billing_max_cycles'));

        $this->getSubscriptionModel()->setIsTrial($subscriptionData->getData('allow_trial'));
        if ($subscriptionData->getData('allow_trial')) {
            $order->setHasTrial(true);
            $this->getSubscriptionModel()->setIsTrial(true);
            $this->getSubscriptionModel()->setTrialPeriodLabel($subscriptionData->getData('trial_period_label'));
            $this->getSubscriptionModel()->setTrailCount(1);
            $this->getSubscriptionModel()->setTrialPeriodUnit($this->intervalType[$subscriptionData->getTrialPeriod()]);
            $this->getSubscriptionModel()->setTrialPeriodFrequency($subscriptionData->getData('trial_frequency'));
            $this->getSubscriptionModel()->setTrialPeriodMaxCycle($subscriptionData->getData('trial_max_cycle'));
            $baseTrialAmount = $subscriptionData->getData('trial_amount');
            $this->getSubscriptionModel()->setTrialBillingAmount($subscriptionService->getConvertedPrice($baseTrialAmount));
            $this->getSubscriptionModel()->setBaseTrialBillingAmount($baseTrialAmount);
        }
        $baseInitialAmount = $subscriptionData->getData('initial_amount');
        $this->getSubscriptionModel()->setBaseInitialAmount($baseInitialAmount);
        $this->getSubscriptionModel()->setInitialAmount($subscriptionService->getConvertedPrice($baseInitialAmount));

        /* Set Billed Amount */
        $baseBillingAmount = $product->setSkipValidateTrial(1)->getFinalPrice($item->getQty());
        $billAmount = $subscriptionService->getConvertedPrice($baseBillingAmount);
        $this->getSubscriptionModel()->setBillingAmount($billAmount);
        $this->getSubscriptionModel()->setBaseBillingAmount($baseBillingAmount);
        
        $productFinalPrice = $product->setSkipDiscount(1)->getFinalPrice($item->getQty());
        $subscriptionService->getSubscriptionDiscountAmount($productFinalPrice);
        $this->getSubscriptionModel()->setDiscountAmount($subscriptionData->getDiscountAmount());
        $this->getSubscriptionModel()->setBaseDiscountAmount($subscriptionData->getBaseDiscountAmount());

        $this->getSubscriptionModel()->setTaxAmount($item->getTaxAmount());
        $this->getSubscriptionModel()->setBaseTaxAmount($item->getBaseTaxAmount());
        $this->getSubscriptionModel()->setProductId($item->getProduct()->getId());
        $this->getSubscriptionModel()->setProductName($item->getProduct()->getName());
        $this->additionalInfoData['product_sku'] = $item->getProduct()->getSku();
        
        return $this;
    }

    public function setOrderInfo($order, $item)
    {
        $this->getSubscriptionModel()->setSubscriptionStatus(1);
        $this->getSubscriptionModel()->setInitialOrderId($order->getIncrementId());
        
        $billingAddressId = $order->getBillingAddress()->getCustomerAddressId() ? : $order->getShippingAddress()->getCustomerAddressId();
        $this->getSubscriptionModel()->setBillingAddressId($billingAddressId);
        if (!$item->getIsVirtual()) {
            $this->getSubscriptionModel()->setShippingAddressId($order->getShippingAddress()->getCustomerAddressId());
        }

        $this->getSubscriptionModel()->setBaseCurrencyCode($order->getBaseCurrencyCode());
        $this->getSubscriptionModel()->setCurrencyCode($order->getOrderCurrencyCode());

        $this->getSubscriptionModel()->setCustomerId($order->getCustomerId());
        $this->getSubscriptionModel()->setSubscriberName($order->getCustomerName());
        $this->getSubscriptionModel()->setSubscriberEmail($order->getCustomerEmail());

        $this->getSubscriptionModel()->setStoreId($order->getStoreId());

        $this->getSubscriptionModel()->setBaseShippingAmount($order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount());
        $this->getSubscriptionModel()->setShippingAmount($order->getShippingAmount() + $order->getShippingTaxAmount());
        $this->getSubscriptionModel()->setOrderIncrementId($order->getIncrementId());
        return $this;
    }

    public function getSubscriptionIncrementId()
    {
        $incrementInstance = $this->numericValue->setPrefix($this->getSubscriptionModel()->getStoreId())
                                ->setPadLength(8)->setPadChar('0');
        return $incrementInstance->format($this->getSubscriptionModel()->getId());
    }

    public function sendEmail($order, $item)
    {
        $subscription = $this->getSubscriptionModel();
        
        $nextDate = $this->timezone->date($subscription->getSubscriptionStartDate())->format('F d, Y');
        $billDate = $this->timezone->date($subscription->getNextOccurrenceDate())->format('F d, Y');
        $subscription->setSubscriptionStartDate($nextDate);
        $subscription->setNextOccurrenceDate($billDate);
        $additionalInfo = $subscription->getAdditionalInfo();
        $orderItemInfo = $subscription->getOrderItemInfo();
        
        $emailVars = [
            'subscription' => $subscription,
            'order' => $order,
            'store' => $order->getStore(),
            'formatted_billing_address' => $subscription->getFormattedBillingAddress($order),
            'formatted_shipping_address' => $subscription->getFormattedShippingAddress($order),
            'payment_html'  => $subscription->getPaymentHtml($order),
            'init_amount' => $order->formatPrice($subscription->getInitialAmount()),
            'trial_amount' => $order->formatPrice($subscription->getTrialBillingAmount()),
            'billing_amount' => $order->formatPrice($subscription->getBillingAmount()),
            'item_name' => $subscription->getProductName(),
            'item_sku' => isset($additionalInfo['product_sku']) ? $additionalInfo['product_sku'] : $item->getSku(),
            'item_qty' => isset($orderItemInfo['qty']) ? $orderItemInfo['qty'] : $item->getQty()
        ];
        
        $this->subscriptionModel->sendEmail($emailVars, EmailService::EMAIL_NEW_SUBSCRIPTION);
    }
    
    /**
     * @param $date
     * @param bool $appendTime
     * @return string
     */
    private function getUtcDateTime($date, $appendTime = true)
    {
        if ($appendTime) {
            $currentStoreTime = $this->timezone->date()->format(' H:i:s');
            $date .= $currentStoreTime;
        }
        
        return $this->timezone->convertConfigTimeToUtc($date);
    }
}
