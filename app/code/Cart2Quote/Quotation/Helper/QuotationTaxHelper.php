<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class QuotationTaxHelper
 * @package Cart2Quote\Quotation\Helper
 */
class QuotationTaxHelper extends \Magento\Tax\Helper\Data
{
    /**
     * Percent calculation value, equals 100%
     */
    const NO_TAX_RATE = 1.00;

    /**
     * @var \Magento\Tax\Api\TaxCalculationInterface
     */
    private $taxCalculationService;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxHelper;

    /**
     * @var \Magento\Tax\Model\Calculation
     */
    private $calculationTool;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @var \Magento\Customer\Api\GroupManagementInterface
     */
    private $customerGroupManagement;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data\Metadata
     */
    private $quotationMetadataHelper;

    /**
     * QuotationTaxHelper constructor.
     *
     * @param \Magento\Customer\Api\GroupManagementInterface $customerGroupManagement
     * @param \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Tax\Model\Calculation $calculationTool
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Api\TaxCalculationInterface $taxCalculationService
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $orderTaxCollectionFactory
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $quotationMetadataHelper
     * @param null $serializer
     */
    public function __construct(
        \Magento\Customer\Api\GroupManagementInterface $customerGroupManagement,
        \Magento\Customer\Api\GroupRepositoryInterface $customerGroupRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Tax\Model\Calculation $calculationTool,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Api\TaxCalculationInterface $taxCalculationService,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $orderTaxCollectionFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Cart2Quote\Quotation\Helper\Data\Metadata $quotationMetadataHelper,
        $serializer = null
    ) {
        $this->quotationMetadataHelper = $quotationMetadataHelper; //we need this before the parentConstruct
        $this->parentConstruct(
            $context,
            $jsonHelper,
            $taxConfig,
            $storeManager,
            $localeFormat,
            $orderTaxCollectionFactory,
            $localeResolver,
            $catalogHelper,
            $orderTaxManagement,
            $priceCurrency,
            $serializer
        );
        $this->customerGroupManagement = $customerGroupManagement;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->customerRepository = $customerRepository;
        $this->calculationTool = $calculationTool;
        $this->taxHelper = $taxHelper;
        $this->taxCalculationService = $taxCalculationService;
    }

    /**
     * @param $tierItem
     * @param null|float|int $price
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceExclTax($tierItem, $price = null)
    {
        $item = $tierItem->getItem();
        if ($price === null) {
            $price = $tierItem->getCustomPrice();
        }

        if ($this->priceIncludesTax($item->getStoreId())) {
            if (!$item->getQuote() === null) {
                if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $price -= $this->getBundleTaxAmount($item);
                } else {
                    $rate = $this->getTaxCalculationRate($item);

                    if ($rate == self::NO_TAX_RATE) {
                        $rate = $this->getTaxCalculationRate($item, true);
                    }
                    $price = $price / $rate;
                }
           }
        }

        return $price;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getFinalPriceExclTax(
        \Magento\Quote\Model\Quote\Item $item,
        \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem,
        \Magento\Catalog\Model\Product $product
    ) {
        //make sure the item has a quote
        if (!$item->getQuote()) {
            $item->setQuote($tierItem->getQuote());
        }

        $rate = $this->getTaxCalculationRate($item);
        if ($rate == self::NO_TAX_RATE) {
            $rate = $this->getTaxCalculationRate($item, true);
        }
        $finalPrice = $product->getPriceModel()->getFinalPrice($tierItem->getQty(), $product);

        return $finalPrice = $finalPrice / $rate;
    }

    /**
     * Get bundle Tax amount
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param boolean $base
     * @return float
     */
    public function getBundleTaxAmount(\Magento\Quote\Model\Quote\Item $item, $base = false)
    {
        $taxAmount = 0;
        $currentTierItem = $item->getCurrentTierItem();
        if (isset($currentTierItem)) {
            $taxAmount = $item->getTaxAmount();
            if ($base) {
                $taxAmount = $item->getBaseTaxAmount();
            }
        }

        return $taxAmount;
    }

    /**
     * Get original bundle Tax amount
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param boolean $base
     * @return float
     */
    public function getOriginalBundleTaxAmount(\Magento\Quote\Model\Quote\Item $item, $base = false)
    {
        $taxAmount = 0;
        $currentTierItem = $item->getCurrentTierItem();
        if (isset($currentTierItem)) {
            $taxAmount = $currentTierItem->getOriginalTaxAmount();
            if ($base) {
                $taxAmount = $currentTierItem->getOriginalBaseTaxAmount();
            }
        }

        return $taxAmount;
    }

    /**
     * Get Item Tax Rate from the Selected Tier
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return float
     */
    public function getProductTaxCalculationRate(\Magento\Quote\Model\Quote\Item $item)
    {
        $rate = self::NO_TAX_RATE;
        /**
         * @var TierItem $currentTierItem
         */
        $currentTierItem = $item->getCurrentTierItem();
        if (isset($currentTierItem)) {
            $rate = $this->getTaxCalculationRate($item);
        }

        return $rate;
    }

    /**
     * Function that gets the tax rate in a calculation value (x.xx)
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param bool $storeDefaultTax
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTaxCalculationRate(\Magento\Quote\Model\Quote\Item $item, $storeDefaultTax = false)
    {
        $rate = self::NO_TAX_RATE;
        $quote = $item->getQuote();
        $customerId = $quote->getCustomerId();

        if ($storeDefaultTax) {
            $customerId = false;
        }
        $customerTax = $this->getCustomerTaxClass($customerId);
        $shippingAddress = $quote->getShippingAddress();
        $billingAddress = $quote->getBillingAddress();
        $taxClassId = $item->getProduct()->getTaxClassId();

        if (!$customerId) {
            $customerId = null;
        }
        $storeId = $item->getQuote()->getStoreId();
        $rateRequest = $this->calculationTool->getRateRequest($shippingAddress, $billingAddress, $customerTax, $storeId, $customerId);
        $rateRequest->setProductClassId($taxClassId);
        $taxRate = $this->calculationTool->getRate($rateRequest);
        $rate = ($taxRate / 100) + $rate;

        return $rate;
    }

    /**
     * @param int $customerId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerTaxClass($customerId)
    {
        if ($customerId) {
            $customerData = $this->customerRepository->getById($customerId);
            $customerTaxClass = $this->customerGroupRepository
                ->getById($customerData->getGroupId())
                ->getTaxClassId();
        } else {
            $customerTaxClass = $this->customerGroupManagement->getNotLoggedInGroup()->getTaxClassId();
        }

        return $customerTaxClass;
    }

    /**
     * Returns the product price, including tax if applicable
     *
     * @param int|float $price
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param bool $base
     * @return float
     */
    public function getOriginalPriceAfterTax($price, \Magento\Quote\Model\Quote\Item $item, $base = false)
    {
        if ($item) {
            if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $price += $this->getOriginalBundleTaxAmount($item, $base);
            } else {
                $price *= $this->getProductTaxCalculationRate($item);
            }
        }

        return $price;
    }

    /**
     * Get quote base original subtotal including tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return float
     */
    public function getQuoteBaseOriginalSubtotalInclTax($quote)
    {
        $total = 0;
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $currentTierItem = $quoteItem->getCurrentTierItem();

            if (isset($currentTierItem)) {
                $qty = $currentTierItem->getQty();
                $baseOriginalPriceInclTax = $this->getBaseOriginalPriceInclTax($currentTierItem);
                $total += $baseOriginalPriceInclTax * $qty;
            }
        }

        return $total;
    }

    /**
     * Get quote original subtotal including tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return float
     */
    public function getQuoteOriginalSubtotalInclTax($quote)
    {
        $total = 0;
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $currentTierItem = $quoteItem->getCurrentTierItem();

            if (isset($currentTierItem)) {
                $qty = $currentTierItem->getQty();
                $originalPriceInclTax = $this->getOriginalPriceInclTax($currentTierItem);
                $total += $originalPriceInclTax * $qty;
            }
        }

        return $total;
    }

    /**
     * Get original price including tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @return float
     */
    public function getOriginalPriceInclTax($tierItem)
    {
        $originalPrice = $tierItem->getOriginalPrice();

        return $this->getOriginalPriceAfterTax($originalPrice, $tierItem->getItem());
    }

    /**
     * Get base original price including tax
     *
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
     * @return float
     */
    public function getBaseOriginalPriceInclTax($tierItem)
    {
        $baseOriginalPrice = $tierItem->getBaseOriginalPrice();

        return $this->getOriginalPriceAfterTax($baseOriginalPrice, $tierItem->getItem(), true);
    }

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $orderTaxCollectionFactory
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param $serializer
     */
    protected function parentConstruct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $orderTaxCollectionFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        $serializer
    ) {
        $version = $this->getMagentoVersion();
        if (version_compare($version, "2.2.0", "<")) {
            parent::__construct(
                $context,
                $jsonHelper,
                $taxConfig,
                $storeManager,
                $localeFormat,
                $orderTaxCollectionFactory,
                $localeResolver,
                $catalogHelper,
                $orderTaxManagement,
                $priceCurrency
            );
        } else {
            parent::__construct(
                $context,
                $jsonHelper,
                $taxConfig,
                $storeManager,
                $localeFormat,
                $orderTaxCollectionFactory,
                $localeResolver,
                $catalogHelper,
                $orderTaxManagement,
                $priceCurrency,
                $serializer
            );
        }
    }

    /**
     * Getter for the Magento version
     *
     * @return string
     */
    public function getMagentoVersion() {
        return $this->quotationMetadataHelper->getMagentoVersion();
    }

    /**
     * Function that checks if custom price is including tax for the given store and settings
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function customPriceIncludesTax($store = null)
    {
        if ($this->priceIncludesTax($store) && !$this->applyTaxOnCustomPrice($store)) {
            //check this until MC-30483 is fixed: https://github.com/magento/magento2/issues/26394
            $magentoVersion = $this->getMagentoVersion();
            if (version_compare($magentoVersion, "2.3.1", ">")) {
                //assume custome price excluding tax
                return false;
            } else {
                return true;
            }
        }

        return false;
    }
}
