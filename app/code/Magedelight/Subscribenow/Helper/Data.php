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

namespace Magedelight\Subscribenow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magedelight\Subscribenow\Model\System\Config\Backend\PaymentMethod;

class Data extends AbstractHelper
{

    const XML_PATH_SUBSCRIBENOW_ACTIVE = 'md_subscribenow/general/enabled';
    const XML_PATH_SUBSCRIPTION_INTERVAL = 'md_subscribenow/general/manage_subscription_interval';
    const XML_PATH_ALLOWED_PAYMENT_METHODS = 'md_subscribenow/general/payment_gateway';
    const XML_PATH_ALLOWED_SHIPPING_METHODS = 'md_subscribenow/general/shipping_method';
    const XML_PATH_SUBSCRIBENOW_PRODUCT_MAX_QTY = 'md_subscribenow/product_subscription/maximum_quantity_subscribe';
    const XML_PATH_UPDATE_PROFILE_DAY_LIMIT = 'md_subscribenow/general/update_profile_before';
    const XML_PATH_CAN_CANCEL_SUBSCRIPTION = 'md_subscribenow/product_subscription/allow_cancel_subscription';
    const XML_PATH_CAN_SKIP_SUBSCRIPTION = 'md_subscribenow/product_subscription/allow_skip_subscription';
    const XML_PATH_CAN_PAUSE_SUBSCRIPTION = 'md_subscribenow/product_subscription/allow_pause_subscription';
    const XML_PATH_SUBSCRIPTION_PRODUCT_LIST_TEXT = 'md_subscribenow/product_subscription/subscription_list_text';
    const XML_PATH_SUBSCRIPTION_SUMMARY_ENABLED = 'md_subscribenow/product_subscription/enabled';
    const XML_PATH_SUBSCRIPTION_SUMMARY_HEADER = 'md_subscribenow/product_subscription/header_summary_text';
    const XML_PATH_SUBSCRIPTION_SUMMARY_CONTENT = 'md_subscribenow/product_subscription/content_summary_text';
    const XML_PATH_SUBSCRIPTION_FREE_SHIPPING = 'md_subscribenow/product_subscription/free_shipping_subscription';
    const XML_PATH_DYNAMIC_PRICE = 'md_subscribenow/general/dynamic_price';
    const XML_PATH_ALLOWED_BILLING_EDIT = 'general/update_billing_address';
    const XML_PATH_ALLOWED_SHIPPING_EDIT = 'general/update_shipping_address';

    const XML_PATH_CUSTOMER_ADDRESS_TEMPLATE = 'customer/address_templates/html';
    
    /**
     * Email Configuration.
     */
    const XML_PATH_SUBSCRIPTION_SENDER = 'email/subscription_email_sender';
    const XML_PATH_SUBSCRIPTION_EMAIL = 'email/new_subscription_template';
    const XML_PATH_SUBSCRIPTION_EMAIL_BCC = 'email/new_subscription_copyto';
    const XML_PATH_PROFILE_UPDATE_EMAIL = 'email/subscription_update_template';
    const XML_PATH_PROFILE_UPDATE_EMAIL_BCC = 'email/subscription_update_copyto';
    const XML_PATH_PAYMENT_FAILED_EMAIL = 'email/payment_fail_template';
    const XML_PATH_PAYMENT_FAILED_EMAIL_BCC = 'email/payment_fail_template_copyto';
    const XML_PATH_NEW_CARD_ADD_EMAIL = 'email/subscription_card_template';
    const XML_PATH_NEW_CARD_ADD_EMAIL_BCC = 'email/subscription_card_template_copyto';
    const XML_PATH_REMINDER_EMAIL = 'email/subscription_reminder_template';
    const XML_PATH_REMINDER_EMAIL_BCC = 'email/subscription_reminder_template_copyto';
    const XML_PATH_EWALLET_TOPUP_REMINDER_EMAIL = 'email/subscription_ewallet_topup_reminder_template';
    const XML_PATH_EWALLET_TOPUP_REMINDER_EMAIL_BCC = 'email/subscription_ewallet_reminder_template_copyto';

    private $serialize;

    private $paymentMethodsArray = [];

    protected $_resource;

    /**
     * Period units.
     *
     * @var string
     */
    const PERIOD_DAY = 'day';
    const PERIOD_WEEK = 'week';
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR = 'year';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magedelight\Subscribenow\Model\ProductAssociatedOrders
     */
    protected $_productAssociatedOrders;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * @var objectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Json $serializer
     * @param \Magento\Framework\App\State $_state
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        Context $context,
        Json $serializer,
        PaymentHelper $paymentHelper,
        PaymentMethod $paymentMethod,
        \Magento\Framework\App\State $_state,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->serialize = $serializer;
        $this->paymentHelper = $paymentHelper;
        $this->paymentMethod = $paymentMethod;
        $this->_state = $_state;
        $this->_resource = $resource;
        parent::__construct($context);
    }

    public function isModuleEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SUBSCRIBENOW_ACTIVE, ScopeInterface::SCOPE_STORE);
    }

    public function getSubscriptionInterval($toArray = true, $field = null)
    {
        $interval = $this->scopeConfig->getValue(self::XML_PATH_SUBSCRIPTION_INTERVAL, ScopeInterface::SCOPE_STORE);
        if ($interval && ($toArray || $field)) {
            $interval = $this->serialize->unserialize($interval);
            if ($field) {
                return array_combine(array_keys($interval), array_column($interval, $field));
            }
        }
        return $interval;
    }

    public function getAllowedPaymentMethods()
    {
        $allowedMethods = $this->scopeConfig->getValue(
            self::XML_PATH_ALLOWED_PAYMENT_METHODS,
            ScopeInterface::SCOPE_STORE
        );
        $methodList = ($allowedMethods)?explode(',', $allowedMethods):[];
        array_push($methodList, 'free');
        return $methodList;
    }
    
    public function getAllowedShippingMethods()
    {
        $allowedMethods = $this->scopeConfig->getValue(
            self::XML_PATH_ALLOWED_SHIPPING_METHODS,
            ScopeInterface::SCOPE_WEBSITE
        );
        $methodList = ($allowedMethods)?explode(',', $allowedMethods):[];
        return $methodList;
    }

    /**
     * @return mixed
     */
    public function useDynamicPrice()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_DYNAMIC_PRICE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getUpdateProfileDayLimit()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_UPDATE_PROFILE_DAY_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function canCancelSubscription()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_CAN_CANCEL_SUBSCRIPTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function canSkipSubscription()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_CAN_SKIP_SUBSCRIPTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function canPauseSubscription()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_CAN_PAUSE_SUBSCRIPTION, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * @return boolean
     */
    public function isShowCartSummaryBlock()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_SUBSCRIPTION_SUMMARY_ENABLED, ScopeInterface::SCOPE_WEBSITE);
    }
    
    /**
     * @return string
     */
    public function getSummaryBlockTitle()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_SUBSCRIPTION_SUMMARY_HEADER, ScopeInterface::SCOPE_WEBSITE);
    }
    
    /**
     * @return string
     */
    public function getSummaryBlockContetnt()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_SUBSCRIPTION_SUMMARY_CONTENT, ScopeInterface::SCOPE_WEBSITE);
    }
    
    /**
     * @return string
     */
    public function getListPageText()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_SUBSCRIPTION_PRODUCT_LIST_TEXT, ScopeInterface::SCOPE_WEBSITE);
    }
    
    /**
     * @return boolean
     */
    public function isSubscriptionWithFreeShipping()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_SUBSCRIPTION_FREE_SHIPPING, ScopeInterface::SCOPE_WEBSITE);
    }
    
    /**
     * @return boolean
     */
    public function getCustomerAddressTemplate()
    {
        return $this->scopeConfig->getValue(Self::XML_PATH_CUSTOMER_ADDRESS_TEMPLATE, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Return subscription status array
     *
     * @return array
     */
    public function getStatusLabel()
    {
        return [
            'unknown' => 'Unknown',
            '0' => __('Pending'),
            '1' => __('Active'),
            '2' => __('Paused'),
            '3' => __('Expired'),
            '4' => __('Cancelled'),
            '5' => __('Suspended'),
            '6' => __('Failed'),
            '7' => __('Complete'),
            '8' => __('Renew'),
        ];
    }
    
    /**
     * @param \Magento\Shipping\Model\Rate\Result $shippingModel
     *
     * @return bool
     */
    public function isMethodRestricted($shippingModel)
    {
        $code = $shippingModel->getCarrier();
        $restrictedMethod = $this->getAllowedShippingMethods();
        
        if ($restrictedMethod && !in_array($code, $restrictedMethod)) {
            return true;
        }

        return false;
    }
    
    /**
     * @param type $item
     * @param type $t
     *
     * @return type
     */
    public function getCustomOptionPrice($item, $t)
    {
        $CustomOptionprice = 0;

        $productOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

        if (isset($productOptions['options'])) {
            foreach ($productOptions['options'] as $key => $value) {
                $optionType = $value['option_type'];
                if ($optionType == 'drop_down' || $optionType == 'multiple' || $optionType == 'radio' || $optionType == 'checkbox' || $optionType == 'multiple') {
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
                            ->get('Magento\Framework\App\ResourceConnection');
                    $connection = $this->_resources->getConnection();
                    $optionValue = explode(',', $value['option_value']);
                    for ($count = 0; $count < count($optionValue); ++$count) {
                        $select = $connection->select()
                                ->from(
                                    ['mdsub' => $this->_resources->getTableName('catalog_product_option_type_price')]
                                )
                                ->where('mdsub.option_type_id=?', $optionValue[$count]);
                        $result = $connection->fetchAll($select);
                        if (isset($result)) {
                            if ($result[0]['price_type'] == 'fixed') {
                                $CustomOptionprice += $result[0]['price'];
                            } else {
                                $CustomOptionprice += ($t * ($result[0]['price'] / 100));
                            }
                        }
                    }
                } else {
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
                            ->get('Magento\Framework\App\ResourceConnection');
                    $connection = $this->_resources->getConnection();

                    $select = $connection->select()
                            ->from(
                                ['mdsub' => $this->_resources->getTableName('catalog_product_option_price')]
                            )
                            ->where('mdsub.option_id=?', $value['option_id']);

                    $result = $connection->fetchAll($select);
                    if (isset($result)) {
                        if ($result[0]['price_type'] == 'fixed') {
                            $CustomOptionprice += $result[0]['price'];
                        } else {
                            $CustomOptionprice += ($t * ($result[0]['price'] / 100));
                        }
                    }
                }
            }
        }

        return $CustomOptionprice;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->_state->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE ? true : false;
    }
    
    /**
     * Get Initial Amount Title
     * @return string
     */
    public function getInitAmountTitle()
    {
        return __('Initial Fee');
    }
    
    /**
     * Get Trial Amount Title
     * @return string
     */
    public function getTrialAmountTitle()
    {
        return __('Trial Billing Amount');
    }

    /**
     * Get Billing Period Title
     * @return string
     */
    public function getBillingPeriodTitle()
    {
        return __('Billing Period');
    }
    
    /**
     * Get Billing Cycle Title
     * @return string
     */
    public function getBillingCycleTitle()
    {
        return __('Billing Cycle');
    }
    
    /**
     * Get Trial Period Title
     * @return string
     */
    public function getTrialPeriodTitle()
    {
        return __('Trial Period');
    }
    
    /**
     * Get Trial Cycle Title
     * @return string
     */
    public function getTrialCycleTitle()
    {
        return __('Trial Cycle');
    }
    
    /**
     * Get Subscription Start Date Title
     * @return string
     */
    public function getSubscriptionStartDateTitle()
    {
        return __('Subscription Start Date');
    }
    
    /**
     * Get Allowed Maximum Quantity To Subscribe Per Product
     * @return int
     */
    public function getMaxAllowedQty()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SUBSCRIBENOW_PRODUCT_MAX_QTY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
    
    /**
     * Get Max Quantity Error Message
     * @return string
     */
    public function getQtyErrorMessage()
    {
        return __('Subscription Product quantity should be %1 or less.', $this->getMaxAllowedQty());
    }
    
    /**
     * Get Interval Label
     * @return string|null
     */
    public function getIntervalLabel($key)
    {
        $interval = $this->getSubscriptionInterval();

        if (!empty($interval) && array_key_exists($key, $interval)) {
            $result = $interval[$key];
        } elseif (!empty($interval)) {
            $result = reset($interval);
        }
        
        if ($result && $result['interval_label']) {
            return $result['interval_label'];
        }
        
        return null;
    }
    
    public function getScopeValue($scopePath, $storeId = 0)
    {
        return $this->scopeConfig->getValue('md_subscribenow/'.$scopePath, ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getSubscriptionListingText($product)
    {
        $html = null;
        
        if ($this->isModuleEnable() &&
            $product instanceof \Magento\Catalog\Model\Product &&
            $product->getIsSubscription()
        ) {
            $text = $this->getListPageText();
            if ($text) {
                $url = $product->getUrlInStore() . "#md_subscription_content";
                $html = __('<span class="subscription_product_text"><a href="%1">%2</a></span>', $url, $text);
            }
        }
        
        return $html;
    }
    
    public function getMaxFailedAllowedTimes($storeId = 0)
    {
        return $this->getScopeValue('general/maximum_payment_failed', $storeId);
    }
    
    public function getEWalletPaymentTitle($storeId = 0) {
        $wallteTitle = $this->getScopeValue('md_wallet/general/ewallet_title', $storeId);
        if(!$wallteTitle) {
            $wallteTitle = "Magedelight EWallet";
        }
        return $wallteTitle;
    }
    
    public function getPaymentTitle($code = null) {
        if ($code) {
            if ($code == 'magedelight_ewallet') {
                $title = $this->getEWalletPaymentTitle();
            }
            try {
                $title = $this->paymentHelper->getMethodInstance($code)->getTitle();
            } catch (\Exception $ex) {
                $title = $this->getPaymentMethodTitle($code);
            }
        }
        return $title;
    }

    public function getPaymentMethodTitle($code) {
        if(!$this->paymentMethodsArray) {
            $paymentMethods = $this->paymentMethod->toOptionArray();
            $methods = [];
            foreach ($paymentMethods as $method) {
                $methods[$method['value']] = $method['label'];
            }
            $this->paymentMethodsArray = $methods;
        }
        
        return isset($this->paymentMethodsArray[$code]) 
                ? $this->paymentMethodsArray[$code]->getText() : null;
    }
}
