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

use Magedelight\Subscribenow\Model\Source\ProfileStatus;
use Magedelight\Subscribenow\Helper\Data as SubscribeHelper;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magedelight\Subscribenow\Model\Service\EmailService;
use Magedelight\Subscribenow\Model\Service\EmailServiceFactory;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Eav\Model\Entity\Increment\NumericValue;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class ProductSubscribers extends \Magento\Framework\Model\AbstractModel
{
    const SUBSCRIPTION_ID = 'subscription_id';
    const PROFILE_ID = 'profile_id';
    const INITIAL_ORDER_ID = 'initial_order_id';
    const CUSTOMER_ID = 'customer_id';
    const PRODUCT_ID = 'product_id';
    const SUBSCRIBER_NAME = 'subscriber_name';
    const SUBSCRIBER_EMAIL = 'subscriber_email';
    const STORE_ID = 'store_id';
    const NEXT_OCCURRENCE_DATE = 'next_occurrence_date';
    const BILLING_AMOUNT = 'billing_amount';
    const TRIAL_BILLING_AMOUNT = 'trial_billing_amount';
    const BASE_TRIAL_BILLING_AMOUNT = 'base_trial_billing_amount';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const BASE_BILLING_AMOUNT = 'base_billing_amount';
    const TRIAL_COUNT = 'trial_count';
    const TOTAL_BILL_COUNT = 'total_bill_count';
    const PERIOD_MAX_CYCLES = 'period_max_cycles';
    const TRIAL_PERIOD_MAX_CYCLE = 'trial_period_max_cycle';
    const SUBSCRIPTION_ITEM_INFO = 'subscription_item_info';
    const SUBSCRIPTION_START_DATE = 'subscription_start_date';
    const PAYMENT_METHOD_CODE = 'payment_method_code';
    const SUBSCRIPTION_STATUS = 'subscription_status';
    const ORDER_INFO = 'order_info';
    const BILLING_ADDRESS_INFO = 'billing_address_info';
    const SHIPPING_ADDRESS_INFO = 'shipping_address_info';
    const CURRENCY_CODE = 'currency_code';
    const SHIPPING_AMOUNT = 'shipping_amount';
    const BILLING_PERIOD_LABEL = 'billing_period_label';
    const BILLING_FREQUENCY = 'billing_frequency';
    const BILLING_PERIOD = 'billing_period';
    const TRIAL_PERIOD_LABEL = 'trial_period_label';
    const TRIAL_PERIOD_UNIT = 'trial_period_unit';
    const TRIAL_PERIOD_FREQUENCY = 'trial_period_frequency';
    const TAX_AMOUNT = 'tax_amount';
    const ORDER_ITEM_INFO = 'order_item_info';
    const SUSPENSION_THRESHOLD = 'suspension_threshold';
    const UPDATE_NEXT_DATE = 'update_next_date';
    const INITIAL_AMOUNT = 'initial_amount';
    const ADDITIONAL_INFO = 'additional_info';
    const PAYMENT_REGULAR = 'regular';
    const PAYMENT_TRIAL = 'trial';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * @var array
     */
    private $intervalType = [
        'day' => 1,
        'week' => 2,
        'month' => 3,
        'year' => 4,
    ];

    /**
     * @var TimezoneInterface
     */
    protected $timezone;
    /**
     * @var ProductSubscriptionHistory
     */
    private $subscriptionHistory;
    /**
     * @var SubscribeHelper
     */
    private $subscribeHelper;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var EncryptorInterface
     */
    private $encryptor;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    private $emailService;
    
    private $addressRenderer;
    
    private $paymentHelper;
    /**
     * @var NumericValue
     */
    private $numericValue;

    private $renewResetFields = [
        self::SUBSCRIPTION_ID,
        self::PROFILE_ID,
        self::TRIAL_COUNT,
        self::TOTAL_BILL_COUNT,
        self::NEXT_OCCURRENCE_DATE,
    ];

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param TimezoneInterface $timezone
     * @param ProductSubscriptionHistoryFactory $subscriptionHistory
     * @param SubscribeHelper $subscribeHelper
     * @param AddressRepositoryInterface $addressRepository
     * @param EncryptorInterface $encryptor
     * @param EmailServiceFactory $emailService
     * @param AddressRenderer $addressRender
     * @param PaymentHelper $paymentHelper
     * @param NumericValue $numericValue
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        TimezoneInterface $timezone,
        ProductSubscriptionHistoryFactory $subscriptionHistory,
        SubscribeHelper $subscribeHelper,
        AddressRepositoryInterface $addressRepository,
        EncryptorInterface $encryptor,
        EmailServiceFactory $emailService,
        AddressRenderer $addressRender,
        PaymentHelper $paymentHelper,
        NumericValue $numericValue,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->subscriptionHistory = $subscriptionHistory;
        $this->subscribeHelper = $subscribeHelper;
        $this->timezone = $timezone;
        $this->addressRepository = $addressRepository;
        $this->encryptor = $encryptor;
        $this->emailService = $emailService;
        $this->addressRenderer = $addressRender;
        $this->paymentHelper = $paymentHelper;
        $this->numericValue = $numericValue;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    protected function _construct()
    {
        $this->_init('Magedelight\Subscribenow\Model\ResourceModel\ProductSubscribers');
    }

    /**
     * Skip subscription
     * Get next occurrence date of current subscription
     * @param int $subscriptionHistoryBy
     */
    public function skipSubscription($subscriptionHistoryBy = ProductSubscriptionHistory::HISTORY_BY_CUSTOMER)
    {
        $nextOccurenceDate = $this->timezone->date($this->getNextOccurrenceDate())->format('Y-m-d');
        $nextCycle = $this->getNextSubscriptionDate();
        $this->setNextOccurrenceDate($nextCycle)->save();
        $storeNextDate = $this->timezone->date($nextCycle)->format('Y-m-d');
        $comment = __("Subscription date skip to $storeNextDate from $nextOccurenceDate");
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $subscriptionHistoryBy,
            $comment
        );
        
        $this->sendUpdateEmail($comment, true);
    }

    /**
     * @param null $nextOccurenceDate
     * @return string
     */
    public function getNextSubscriptionDate($nextOccurenceDate = null)
    {
        if (!$nextOccurenceDate) {
            $nextOccurenceDate = $this->getNextOccurrenceDate();
        }
        if ($this->isSubscriptionOnTrail()) {
            $addTime = '+' . $this->getTrialPeriodFrequency() . array_search($this->getTrialPeriodUnit(), $this->intervalType);
        } else {
            $addTime = '+' . $this->getBillingFrequency() . array_search($this->getBillingPeriod(), $this->intervalType);
        }
        return date('Y-m-d H:i:s', strtotime($addTime, strtotime($nextOccurenceDate)));
    }
    
    private function isProfileInTrialPeriod()
    {
        return $this->getIsTrial() && $this->isTrialPeriod();
    }

    public function afterSubscriptionCreate()
    {
        $comment = __('Subscription order #%1 created successfully', $this->getOrderIncrementId());
        $this->addHistory($this->getModifiedBy(), $comment);
        
        $this->setNextOccurrenceDate($this->getNextSubscriptionDate());
        
        // Increase Successful Occurrence
        if ($this->isProfileInTrialPeriod()) {
            $this->setTrialCount($this->getTrialCount() + 1);
        } else {
            $this->setTotalBillCount($this->getTotalBillCount() + 1);
        }
        
        if ($this->getPeriodMaxCycles() && $this->getTotalBillCount() >= $this->getPeriodMaxCycles()) {
            $this->completeSubscription($this->getModifiedBy());
        }
        return $this;
    }
    
    public function completeSubscription($subscriptionHistoryBy = ProductSubscriptionHistory::HISTORY_BY_CRON)
    {
        $currentStatus = $this->getSubscriptionStatus();
        $this->setSubscriptionStatus(ProfileStatus::COMPLETED_STATUS)->save();
        $labels = $this->subscribeHelper->getStatusLabel();
        $comment = __("Change status %1 from %2", $labels[ProfileStatus::COMPLETED_STATUS], $labels[$currentStatus]);
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $subscriptionHistoryBy,
            $comment
        );
        $this->sendUpdateEmail($comment);
    }

    public function pauseSubscription($subscriptionHistoryBy = ProductSubscriptionHistory::HISTORY_BY_CUSTOMER)
    {
        $currentStatus = $this->getSubscriptionStatus();
        $this->setSubscriptionStatus(ProfileStatus::PAUSE_STATUS)->save();
        $labels = $this->subscribeHelper->getStatusLabel();
        $comment = __("Change status %1 from %2", $labels[ProfileStatus::PAUSE_STATUS], $labels[$currentStatus]);
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $subscriptionHistoryBy,
            $comment
        );
        $this->sendUpdateEmail($comment);
    }

    public function resumeSubscription($subscriptionHistoryBy = ProductSubscriptionHistory::HISTORY_BY_CUSTOMER)
    {
        $currentStatus = $this->getSubscriptionStatus();
        $this->setSubscriptionStatus(ProfileStatus::ACTIVE_STATUS)->save();
        $labels = $this->subscribeHelper->getStatusLabel();
        $comment = __("Change status %1 from %2", $labels[ProfileStatus::ACTIVE_STATUS], $labels[$currentStatus]);
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $subscriptionHistoryBy,
            $comment
        );
        $this->sendUpdateEmail($comment);
    }

    public function cancelSubscription($subscriptionHistoryBy = ProductSubscriptionHistory::HISTORY_BY_CUSTOMER)
    {
        $currentStatus = $this->getSubscriptionStatus();
        $this->setSubscriptionStatus(ProfileStatus::CANCELED_STATUS)->save();
        $labels = $this->subscribeHelper->getStatusLabel();
        $comment = __("Change status %1 from %2", $labels[ProfileStatus::CANCELED_STATUS], $labels[$currentStatus]);
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $subscriptionHistoryBy,
            $comment
        );
        $this->sendUpdateEmail($comment);
    }
    
    public function failedSubscription($subscriptionHistoryBy = ProductSubscriptionHistory::HISTORY_BY_CRON)
    {
        $currentStatus = $this->getSubscriptionStatus();
        $labels = $this->subscribeHelper->getStatusLabel();
        $comment = __("Change status %1 from %2", $labels[ProfileStatus::FAILED_STATUS], $labels[$currentStatus]);
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $subscriptionHistoryBy,
            $comment
        );
        $this->sendUpdateEmail($comment);
    }

    public function updateSubscription($postValue, $updateBy)
    {
        if (isset($postValue['qty'])) {
            $this->updateSubscriptionQty($postValue['qty'], $updateBy);
        }
        if (isset($postValue['subscription_start_date'])) {
            $this->updateNextOccurenceDate($postValue['subscription_start_date'], $updateBy);
        }
        $this->updateSubscriptionAddress($postValue, $updateBy);
        $this->updatePaymentToken($postValue, $updateBy);
        $this->save();
    }

    public function updatePaymentToken($postValue, $updateBy)
    {
        if (isset($postValue['md_savecard'])) {
            $originalToken = $this->encryptor->decrypt($this->getPaymentToken());
            $token = $this->encryptor->decrypt($postValue['md_savecard']);
            if ($originalToken != $token) {
                $this->setPaymentToken($postValue['md_savecard']);
                $this->subscriptionHistory->create()->addSubscriptionHistory(
                    $this->getId(),
                    $updateBy,
                    __('Card details updated')
                );
                $comment = __('Card details updated');
                $this->sendUpdateEmail($comment);
            }
        }
    }

    private function updateSubscriptionAddress($postValue, $updateBy)
    {
        if (isset($postValue['md_billing_address'])) {
            $originalBillingId = $this->getBillingAddressId();
            if ($originalBillingId && $originalBillingId != $postValue['md_billing_address']) {
                $this->setBillingAddressId($postValue['md_billing_address']);
                $comment = __("Update Billing Address");
                $this->subscriptionHistory->create()->addSubscriptionHistory(
                    $this->getId(),
                    $updateBy,
                    $comment
                );
            }
        }

        if (isset($postValue['md_shipping_address'])) {
            $originalShippingId = $this->getShippingAddressId();
            if ($originalShippingId && $originalShippingId != $postValue['md_shipping_address']) {
                $this->setShippingAddressId($postValue['md_shipping_address']);
                $comment = __("Update Shipping Address");
                $this->subscriptionHistory->create()->addSubscriptionHistory(
                    $this->getId(),
                    $updateBy,
                    $comment
                );
            }
        }
    }

    public function addHistory($updatedBy, $comment)
    {
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $this->getId(),
            $updatedBy,
            $comment
        );
    }

    public function renewSubscription($updatedBy)
    {
        $newSubscription = clone $this;
        $this->resetProfileForRenew($newSubscription);

        $today = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');
        $newSubscription->setSubscriptionStartDate($today);
        $newSubscription->setNextOccurrenceDate($this->getNextSubscriptionDate($today));
        $newSubscription->setSubscriptionStatus(ProfileStatus::ACTIVE_STATUS);
        $newSubscription->save();

        $incrementInstance = $this->numericValue->setPrefix($newSubscription->getStoreId())
            ->setPadLength(8)->setPadChar('0');

        $newSubscription->setProfileId($incrementInstance->format($newSubscription->getId()));
        $newSubscription->save();
        
        $this->sendRenewSubscriptionEmail($newSubscription);
        
        $this->subscriptionHistory->create()->addSubscriptionHistory(
            $newSubscription->getId(),
            $updatedBy,
            __("Subscription profile renewed from #%1", $this->getProfileId())
        );
        $comment = __("Subscription profile renewed to #%1", $newSubscription->getProfileId());
        $this->addHistory($updatedBy, $comment);
        return $newSubscription;
    }
    
    /**
     * 
     * @param type $orderIncrementId
     * @return type
     */
    private function getOrderByIncrementId($orderIncrementId) 
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('increment_id', $orderIncrementId, 'eq')->create();
            return $this->orderRepository->getList($searchCriteria)->getFirstItem();
        } catch (Exception $ex) {
            return null;
        }
    }

    private function sendRenewSubscriptionEmail($subscription)
    {
        $order = $this->getOrderByIncrementId($subscription->getInitialOrderId());
        $orderItemInfo = $subscription->getOrderItemInfo();

        $item = null;
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getParentItemId() != null) {
                continue;
            }
            if ($orderItemInfo['item'] == $orderItem->getProductId()) {
                $item = $orderItem;
                break;
            }
        }

        if (!$item) {
            return false;
        }

        $nextDate = $this->timezone->date($subscription->getSubscriptionStartDate())->format('F d, Y');
        $billDate = $this->timezone->date($subscription->getNextOccurrenceDate())->format('F d, Y');
        $subscription->setSubscriptionStartDate($nextDate);
        $subscription->setNextOccurrenceDate($billDate);
        $additionalInfo = $subscription->getAdditionalInfo();
        
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
        
        $this->sendEmail($emailVars, EmailService::EMAIL_NEW_SUBSCRIPTION);
    }
    
    private function resetProfileForRenew($model)
    {
        foreach ($this->renewResetFields as $fields) {
            $model->unsetData($fields);
        }
        $this->setTrialCount(0);
        $this->setTotalBillCount(0);
        return $this;
    }

    /**
     * Checks weather subscription is on trial
     * @return bool
     */
    private function isSubscriptionOnTrail()
    {
        return $this->getTrialCount() != $this->getTrialPeriodMaxCycle();
    }

    private function updateNextOccurenceDate($date, $updateBy)
    {
        $originalDate = $this->timezone->date($this->getNextOccurrenceDate())->format('F d, Y');
        $utcDate = $this->getUtcDateTime($date);
        $newDate = $this->timezone->date($utcDate)->format('F d, Y');
        if ($newDate != $originalDate) {
            $this->setNextOccurrenceDate($utcDate);
            $comment = __("Changed next billing date $newDate from $originalDate");
            $this->subscriptionHistory->create()->addSubscriptionHistory(
                $this->getId(),
                $updateBy,
                $comment
            );
            $this->sendUpdateEmail($comment, true);
        }
    }

    public function updateSubscriptionQty($qty, $updateBy)
    {
        $itemInfo = $this->getOrderItemInfo();
        $originalQty = $itemInfo['qty'];
        if ($qty != $originalQty) {
            if ($this->validateQty($qty)) {
                $itemInfo['qty'] = $qty;
                $this->setOrderItemInfo($itemInfo);
                $comment = __("Update product qty $qty from  $originalQty");
                $this->subscriptionHistory->create()->addSubscriptionHistory(
                    $this->getId(),
                    $updateBy,
                    $comment
                );
                $this->sendUpdateEmail($comment);
            }
        }
    }
    
    /**
     * Validate Item Qty
     *
     * @param type $itemQty
     * @throws LocalizedException
     */
    public function validateQty($itemQty)
    {
        if ($itemQty) {
            $allowedQty = $this->subscribeHelper->getMaxAllowedQty();

            if ($allowedQty && $itemQty > $allowedQty) {
                $errorMessage = $this->subscribeHelper->getQtyErrorMessage();
                throw new \Magento\Framework\Exception\LocalizedException($errorMessage);
            }
        }
        return true;
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

    public function sendUpdateEmail($comment, $isSkip = false)
    {
        $emailVars = [
            'update_message' => $comment,
            'subscriber_name' => $this->getSubscriberName(),
            'profile_id' => $this->getProfileId(),
            'created_at' => $this->setEmailDateFormat($this->getCreatedAt()),
            'store' => $this->getStoreId(),
        ];
        
        if ($isSkip) {
            $emailVars['next_date'] = $this->setEmailDateFormat($this->getNextOccurrenceDate());
        }
        
        $this->sendEmail($emailVars, EmailService::EMAIL_PROFILE_UPADATE);
    }
    
    public function sendEmail($emailVariable, $type)
    {
        $emailService = $this->emailService->create();
        $emailService->setStoreId($this->getStoreId());
        $emailService->setTemplateVars($emailVariable);
        $emailService->setType($type);
        $email = $this->getSubscriberEmail();
        $emailService->setSendTo($email);
        $emailService->send();
    }
    
    public function updateSubscriptionFailedCount()
    {
        $failedCount = $this->getSuspensionThreshold() + 1;
        $this->setSuspensionThreshold($failedCount)->save();
    }

    private function setEmailDateFormat($date)
    {
        return $this->timezone->date($date)->format('F j, Y');
    }
    
    /**
     * @param Order $order
     * @return string|null
     */
    public function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * @param $order
     * @return string
     * @throws \Exception
     */
    public function getPaymentHtml($order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStore()->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isTrialPeriod()
    {
        return $this->getTrialPeriodMaxCycle() != $this->getTrialCount();
    }

    /**
     * Set setPeriodMaxCycles.
     *
     * @param $cycle
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setPeriodMaxCycles($cycle)
    {
        return $this->setData(self::PERIOD_MAX_CYCLES, $cycle);
    }

    /**
     * @param $count
     * @return $this
     */
    public function setTotalBillCount($count)
    {
        return $this->setData(self::TOTAL_BILL_COUNT, $count);
    }

    /**
     * @param $count
     * @return $this
     */
    public function setTrialCount($count)
    {
        return $this->setData(self::TRIAL_COUNT, $count);
    }

    /**
     * Set Period Max Cycles.
     *
     * @param $cycle
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setTrialPeriodMaxCycle($cycle)
    {
        return $this->setData(self::TRIAL_PERIOD_MAX_CYCLE, $cycle);
    }

    /**
     * Set Trial Billing Amount.
     *
     * @param $amount
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setTrialBillingAmount($amount)
    {
        return $this->setData(self::TRIAL_BILLING_AMOUNT, $amount);
    }

    /**
     * Set Base Trial Billing Amount.
     *
     * @param $amount
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBaseTrialBillingAmount($amount)
    {
        return $this->setData(self::BASE_TRIAL_BILLING_AMOUNT, $amount);
    }

    /**
     * Set Subscription Item Info.
     *
     * @param $info
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setSubscriptionItemInfo($info)
    {
        return $this->setData(self::SUBSCRIPTION_ITEM_INFO, $info);
    }

    /**
     * Set Subscription Start Date Time.
     *
     * @param $datetime
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setSubscriptionStartDate($datetime)
    {
        return $this->setData(self::SUBSCRIPTION_START_DATE, $datetime);
    }

    /**
     * Set Subscription Initial Order Id.
     *
     * @param $ordeId
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setInitialOrderId($ordeId)
    {
        return $this->setData(self::INITIAL_ORDER_ID, $ordeId);
    }

    /**
     * Set Subscription Payment Method code.
     *
     * @param $code
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setPaymentMethodCode($code)
    {
        return $this->setData(self::PAYMENT_METHOD_CODE, $code);
    }

    /**
     * Set Subscription Status.
     *
     * @param $status
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setSubscriptionStatus($status)
    {
        return $this->setData(self::SUBSCRIPTION_STATUS, $status);
    }

    /**
     * Set Subscription Order Info.
     *
     * @param $info
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setOrderInfo($info)
    {
        return $this->setData(self::ORDER_INFO, $info);
    }

    /**
     * Set Subscription Billing Address Info.
     *
     * @param $info
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBillingAddressInfo($info)
    {
        return $this->setData(self::BILLING_ADDRESS_INFO, $info);
    }

    /**
     * Set Subscription Shipping Address Info.
     *
     * @param $info
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setShippingAddressInfo($info)
    {
        return $this->setData(self::SHIPPING_ADDRESS_INFO, $info);
    }

    /**
     * Set Order Currency Code.
     *
     * @param $code
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setCurrencyCode($code)
    {
        return $this->setData(self::CURRENCY_CODE, $code);
    }

    /**
     * Set Customer ID.
     *
     * @param $id
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setCustomerId($id)
    {
        return $this->setData(self::CUSTOMER_ID, $id);
    }

    /**
     * Set Subscriber Name.
     *
     * @param $name
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setSubscriberName($name)
    {
        return $this->setData(self::SUBSCRIBER_NAME, $name);
    }

    /**
     * Set Subscriber Email.
     *
     * @param $email
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setSubscriberEmail($email)
    {
        return $this->setData(self::SUBSCRIBER_EMAIL, $email);
    }

    /**
     * Set Subscriber Email.
     *
     * @param $storeId
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set Shipping amount.
     *
     * @param $shippingamt
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setShippingAmount($shippingamt)
    {
        return $this->setData(self::SHIPPING_AMOUNT, $shippingamt);
    }

    /**
     * Set Billing Period Label.
     *
     * @param $label
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBillingPeriodLabel($label)
    {
        return $this->setData(self::BILLING_PERIOD_LABEL, $label);
    }

    /**
     * Set Billing Frequency.
     *
     * @param $frequecny
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBillingFrequency($frequecny)
    {
        return $this->setData(self::BILLING_FREQUENCY, $frequecny);
    }

    /**
     * Set Billing Period.
     *
     * @param $period
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBillingPeriod($period)
    {
        return $this->setData(self::BILLING_PERIOD, $period);
    }

    /**
     * Set Trial Period Label.
     *
     * @param $label
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setTrialPeriodLabel($label)
    {
        return $this->setData(self::TRIAL_PERIOD_LABEL, $label);
    }

    /**
     * Set Trial Period Unit.
     *
     * @param $unit
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setTrialPeriodUnit($unit)
    {
        return $this->setData(self::TRIAL_PERIOD_UNIT, $unit);
    }

    /**
     * Set Trial Period Frequecny.
     *
     * @param $frequecny
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setTrialPeriodFrequency($frequecny)
    {
        return $this->setData(self::TRIAL_PERIOD_FREQUENCY, $frequecny);
    }

    /**
     * Set Billing amount.
     *
     * @param $amount
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBillingAmount($amount)
    {
        return $this->setData(self::BILLING_AMOUNT, $amount);
    }

    /**
     * Set BaseBilling amount.
     *
     * @param $amount
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setBaseBillingAmount($amount)
    {
        return $this->setData(self::BASE_BILLING_AMOUNT, $amount);
    }

    /**
     * Set Tax amount.
     *
     * @param $amount
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setTaxAmount($amount)
    {
        return $this->setData(self::TAX_AMOUNT, $amount);
    }

    /**
     * Set Order Item Info.
     *
     * @param $info
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setOrderItemInfo($info)
    {
        return $this->setData(self::ORDER_ITEM_INFO, $info);
    }

    /**
     * Set Product ID.
     *
     * @param $id
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setProductId($id)
    {
        return $this->setData(self::PRODUCT_ID, $id);
    }

    /**
     * Set Suspension threshold
     *
     * @param $maxLimit
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setSuspensionThreshold($maxLimit)
    {
        return $this->setData(self::SUSPENSION_THRESHOLD, $maxLimit);
    }

    /**
     * Set Profile ID.
     *
     * @param $id
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setProfileId($id)
    {
        return $this->setData(self::PROFILE_ID, $id);
    }

    /**
     * Set Created At.
     *
     * @param $createdat
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setCreatedAt($createdat)
    {
        return $this->setData(self::CREATED_AT, $createdat);
    }

    /**
     * Set Updated At.
     *
     * @param $updatedat
     *
     * @return \Magedelight\Subscribenow\Model\ProductSubscribers
     */
    public function setUpdatedAt($updatedat)
    {
        return $this->setData(self::UPDATED_AT, $updatedat);
    }

    /**
     * allow customer to update next occurance date
     * $status.
     */
    public function setAllowUpdateNextDate($status)
    {
        return $this->setData(self::UPDATE_NEXT_DATE, $status);
    }

    /**
     * @return int
     */
    public function getTotalBillCount()
    {
        return $this->getData(self::TOTAL_BILL_COUNT);
    }

    /**
     * @return int
     */
    public function getTrialCount()
    {
        return $this->getData(self::TRIAL_COUNT);
    }

    public function getNextOccurrenceDate()
    {
        return $this->getData(self::NEXT_OCCURRENCE_DATE);
    }

    /**
     * Retrive customer to update next occurance date
     * $status.
     */
    public function getAllowUpdateNextDate()
    {
        return $this->getData(self::UPDATE_NEXT_DATE);
    }

    /**
     * Retrieve PeriodMaxCycles.
     */
    public function getPeriodMaxCycles()
    {
        return $this->getData(self::PERIOD_MAX_CYCLES);
    }

    /**
     * Retrieve Trial Period Max Cycles.
     */
    public function getTrialPeriodMaxCycle()
    {
        return $this->getData(self::TRIAL_PERIOD_MAX_CYCLE);
    }

    /**
     * Retrieve Trial Billing Amount.
     */
    public function getTrialBillingAmount()
    {
        return $this->getData(self::TRIAL_BILLING_AMOUNT);
    }

    /**
     * Retrieve Base Trial Billing Amount.
     */
    public function getBaseTrialBillingAmount()
    {
        return $this->getData(self::BASE_TRIAL_BILLING_AMOUNT);
    }

    /**
     * Retrieve Subscription Item Info.
     */
    public function getSubscriptionItemInfo()
    {
        return $this->getData(self::SUBSCRIPTION_ITEM_INFO);
    }

    /**
     * Retrieve Subscription Start Date Time.
     */
    public function getSubscriptionStartDate()
    {
        return $this->getData(self::SUBSCRIPTION_START_DATE);
    }

    /**
     * Retrieve Subscription Initial Order Id.
     */
    public function getInitialOrderId()
    {
        return $this->getData(self::INITIAL_ORDER_ID);
    }

    /**
     * Retrieve Subscription Payment Method code.
     */
    public function getPaymentMethodCode()
    {
        return $this->getData(self::PAYMENT_METHOD_CODE);
    }

    /**
     * Retrieve Subscription Status.
     */
    public function getSubscriptionStatus()
    {
        return $this->getData(self::SUBSCRIPTION_STATUS);
    }

    /**
     * Retrieve Subscription Order Info.
     */
    public function getOrderInfo()
    {
        return $this->getData(self::ORDER_INFO);
    }

    /**
     * Retrieve Subscription Billing Address Info.
     */
    public function getBillingAddressInfo()
    {
        return $this->getData(self::BILLING_ADDRESS_INFO);
    }

    /**
     * Retrieve Subscription Shipping Address Info.
     */
    public function getShippingAddressInfo()
    {
        return $this->getData(self::SHIPPING_ADDRESS_INFO);
    }

    /**
     * Retrieve Order Currency Code.
     */
    public function getCurrencyCode()
    {
        return $this->getData(self::CURRENCY_CODE);
    }

    /**
     * Retrieve Customer ID.
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Retrieve Subscriber Name.
     */
    public function getSubscriberName()
    {
        return $this->getData(self::SUBSCRIBER_NAME);
    }

    /**
     * Retrieve Subscriber Email.
     */
    public function getSubscriberEmail()
    {
        return $this->getData(self::SUBSCRIBER_EMAIL);
    }

    /**
     * Retrieve Subscriber Email.
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Retrieve Shipping amount.
     */
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * Retrieve Billing Period Label.
     */
    public function getBillingPeriodLabel()
    {
        return $this->getData(self::BILLING_PERIOD_LABEL);
    }

    /**
     * Retrieve Billing Frequency.
     */
    public function getBillingFrequency()
    {
        return $this->getData(self::BILLING_FREQUENCY);
    }

    /**
     * Retrieve Billing Period.
     */
    public function getBillingPeriod()
    {
        return $this->getData(self::BILLING_PERIOD);
    }

    /**
     * Retrieve Trial Period Label.
     */
    public function getTrialPeriodLabel()
    {
        return $this->getData(self::TRIAL_PERIOD_LABEL);
    }

    /**
     * Retrieve Trial Period Unit.
     */
    public function getTrialPeriodUnit()
    {
        return $this->getData(self::TRIAL_PERIOD_UNIT);
    }

    /**
     * Retrieve Trial Period Frequecny.
     */
    public function getTrialPeriodFrequency()
    {
        return $this->getData(self::TRIAL_PERIOD_FREQUENCY);
    }

    /**
     * Retrieve Billing amount.
     */
    public function getBillingAmount()
    {
        return $this->getData(self::BILLING_AMOUNT);
    }

    /**
     * Retrieve Discount amount.
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * Retrieve Base Discount amount.
     */
    public function getBaseDiscountAmount()
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Retrieve Billing amount.
     */
    public function getBaseBillingAmount()
    {
        return $this->getData(self::BASE_BILLING_AMOUNT);
    }

    /**
     * Retrieve Tax amount.
     */
    public function getTaxAmount()
    {
        return $this->getData(self::TAX_AMOUNT);
    }

    /**
     * Retrieve Order Item Info.
     */
    public function getOrderItemInfo()
    {
        return $this->getData(self::ORDER_ITEM_INFO);
    }

    /**
     * Retrieve Product ID.
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Retrieve Profile ID.
     */
    public function getProfileId()
    {
        return $this->getData(self::PROFILE_ID);
    }

    /**
     * Retrieve Created At.
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Retrieve Updated At.
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }
}
