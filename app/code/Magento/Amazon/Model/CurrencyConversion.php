<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CurrencyConversion
 */
class CurrencyConversion
{
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var CurrencyFactory */
    protected $currencyFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory
    ) {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * Returns currency code by store id
     *
     * @param int $storeId
     * @return string
     */
    public function getCurrencyCodebyStoreId($storeId)
    {
        try {
            $store = $this->storeManager->getById($storeId);
            $currencyCode = $store->getDefaultCurrencyCode();
        } catch (NoSuchEntityException $e) {
            $currencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        }

        return $currencyCode;
    }

    /**
     * Converts the amount value from one currency to another.
     * If the $currencyCodeFrom is not specified the base currency will be used.
     * If the $currencyCodeTo is not specified the base currency will be used.
     *
     * @param float $amount
     * @param string|null $currencyCodeFrom
     * @param string|null $currencyCodeTo
     * @return float
     */
    public function convert($amount, $currencyCodeFrom = null, $currencyCodeTo = null)
    {
        /** @var float */
        $toBaseRate = 1.0000;
        /** @var float */
        $fromBaseRate = 1.0000;
        /** @var string */
        $baseCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();

        if (!$currencyCodeFrom) {
            $currencyCodeFrom = $baseCode;
        }

        if (!$currencyCodeTo) {
            $currencyCodeTo = $baseCode;
        }

        if ($currencyCodeFrom == $currencyCodeTo) {
            return $amount;
        }

        $baseCurrency = $this->currencyFactory->create()->load($baseCode);

        if ($rate = $baseCurrency->getAnyRate($currencyCodeFrom)) {
            $toBaseRate = $rate;
        }

        if ($rate = $this->currencyFactory->create()->load($baseCode)->getAnyRate($currencyCodeTo)) {
            $fromBaseRate = $rate;
        }

        $amount = ($amount / $toBaseRate) * $fromBaseRate;
        return round((float)$amount, 2, PHP_ROUND_HALF_UP);
    }
}
