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

namespace Magedelight\Subscribenow\Block\Customer\Account\View;

use Magedelight\Subscribenow\Block\Customer\Account\View;
use Magedelight\Subscribenow\Helper\Data as subscribeHelper;
use Magedelight\Subscribenow\Model\Service\PaymentService;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Payment extends View
{

    protected $_template = 'customer/account/view/payment.phtml';

    /**
     * @var subscribeHelper
     */
    private $subscriberHelper;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var Currency
     */
    private $orderCurrency = null;

    /**
     * @var Currency|null
     */
    private $baseCurrency = null;

    /**
     * Payment constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param subscribeHelper $subscribeHelper
     * @param TimezoneInterface $timezone
     * @param ProductRepositoryInterface $productRepository
     * @param PaymentHelper $paymentHelper
     * @param PriceHelper $priceHelper
     * @param PaymentService $paymentService
     * @param CurrencyFactory $currencyFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        subscribeHelper $subscribeHelper,
        TimezoneInterface $timezone,
        ProductRepositoryInterface $productRepository,
        DataObjectFactory $dataObject,
        Json $serialize,
        PaymentHelper $paymentHelper,
        PriceHelper $priceHelper,
        PaymentService $paymentService,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
    
        parent::__construct(
            $context,
            $registry,
            $subscribeHelper,
            $timezone,
            $productRepository,
            $dataObject,
            $serialize,
            $data
        );
        
        $this->paymentService = $paymentService;
        $this->paymentHelper = $paymentHelper;
        $this->priceHelper = $priceHelper;
        $this->currencyFactory = $currencyFactory;
        $this->subscriberHelper = $subscribeHelper;
    }

    /**
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        $subscription = $this->getSubscription();
        return $subscription->getCurrencyCode() != $subscription->getBaseCurrencyCode();
    }
    
    private function isDynamicPrice()
    {
        return (bool) $this->subscriberHelper->useDynamicPrice();
    }
    
    private function getCurrentCurrencyPrice($price)
    {
        if ($this->isCurrencyDifferent()) {
            $code = $this->getSubscription()->getCurrencyCode();
            $price = $this->getBaseCurrency()->convert($price, $code);
        }
        return $price;
    }
    
    /**
     * @return string
     */
    public function getBaseBillingAmount()
    {
        if ($this->isDynamicPrice() && $this->getSubscriptionProduct()) {
            return $this->formatBasePrice($this->getSubscriptionProduct()->getFinalPrice());
        } else {
            return $this->formatBasePrice($this->getSubscription()->getBaseBillingAmount());
        }
    }
    
    /**
     * @return float|string
     */
    public function getBillingAmount()
    {
        if ($this->isDynamicPrice() && $this->getSubscriptionProduct()) {
            $billingamount = $this->getCurrentCurrencyPrice($this->getSubscriptionProduct()->getFinalPrice());
        } else {
            $billingamount = $this->getSubscription()->getBillingAmount();
        }
        
        $amount = $this->formatPrice($billingamount);
        if ($this->isCurrencyDifferent()) {
            $amount .= ' (' . $this->getBaseBillingAmount() . ')';
        }
        return $amount;
    }

    /**
     * @return float|string
     */
    public function getTrialAmount()
    {
        $amount =  $this->formatPrice($this->getSubscription()->getTrialBillingAmount());
        if ($this->isCurrencyDifferent()) {
            $amount .= ' (' . $this->formatBasePrice($this->getSubscription()->getBaseTrialBillingAmount()) . ')';
        }
        return $amount;
    }

    /**
     * @return float|string
     */
    public function getInitialAmount()
    {
        $amount = $this->formatPrice($this->getSubscription()->getInitialAmount());
        if ($this->isCurrencyDifferent()) {
            $amount .= ' (' . $this->formatBasePrice($this->getSubscription()->getBaseInitialAmount()) . ')';
        }
        return $amount;
    }

    /**
     * @return bool
     */
    public function canDisplayCardInfo()
    {
        $code = $this->getSubscription()->getPaymentMethodCode();
        if ($code == "magedelight_ewallet" || $code == "cashondelivery") {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getCardInfo()
    {
        $code = $this->getSubscription()->getPaymentMethodCode();
        $token = $this->getSubscription()->getPaymentToken();
        $customerId = $this->getSubscription()->getCustomerId();
        $paymentService = $this->paymentService->getByPaymentCode($code, $token, $customerId);
        
        if($paymentService){
            return $paymentService->getCardInfo();
        }
        
        return null;
    }

    /**
     * @return mixed
     */
    public function getSaveCards()
    {
        $code = $this->getSubscription()->getPaymentMethodCode();
        $token = $this->getSubscription()->getPaymentToken();
        $customerId = $this->getSubscription()->getCustomerId();
        return $this->paymentService->getByPaymentCode($code, $token, $customerId)->getSavedCards();
    }

    /**
     * @param $amount
     * @return float|string
     */
    public function formatBasePrice($amount)
    {
        return $this->getBaseCurrency()->formatPrecision($amount, 2);
    }

    /**
     * @param $amount
     * @return float|string
     */
    public function formatPrice($amount)
    {
        return $this->getOrderCurrency()->formatPrecision($amount, 2);
    }

    /**
     * @return Currency|\Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        if ($this->baseCurrency == null) {
            $this->baseCurrency = $this->currencyFactory->create();
            $this->baseCurrency->load($this->getSubscription()->getBaseCurrencyCode());
        }
        return $this->baseCurrency;
    }

    /**
     * @return Currency|\Magento\Directory\Model\Currency
     */
    public function getOrderCurrency()
    {
        if ($this->orderCurrency == null) {
            $this->orderCurrency = $this->currencyFactory->create();
            $this->orderCurrency->load($this->getSubscription()->getCurrencyCode());
        }
        return $this->orderCurrency;
    }
}
