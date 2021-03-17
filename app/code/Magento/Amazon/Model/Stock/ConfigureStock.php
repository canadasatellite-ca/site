<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Stock;

use Magento\Amazon\Msi\MsiApi;
use Magento\Amazon\Msi\MsiChecker;

/**
 * Class ConfigureStock
 */
class ConfigureStock
{
    /**
     * @var MsiChecker
     */
    private $msiChecker;

    /**
     * @var MsiApi
     */
    private $msiApi;

    /**
     * @param MsiChecker $msiChecker
     * @param MsiApi $msiApi
     */
    public function __construct(
        MsiChecker $msiChecker,
        MsiApi $msiApi
    ) {
        $this->msiChecker = $msiChecker;
        $this->msiApi = $msiApi;
    }

    /**
     * Add stock item configuration for temporary product created to place magento order for imported amazon order
     *
     * @param string $sku
     * @param int $websiteId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addStockConfigurationForTempProduct(string $sku, int $websiteId)
    {
        $msiEnabled = $this->msiChecker->isMsiEnabled();
        if (!$msiEnabled) {
            return;
        }

        $stock = $this->msiApi->getStockByWebsiteId($websiteId);

        $stockItemConfiguration = $this->msiApi->createStockItemConfiguration();
        $stockItemConfiguration->setManageStock(false);
        $stockItemConfiguration->setUseConfigManageStock(false);
        $stockItemConfiguration->setMinQty(1);

        $this->msiApi->saveStockItemConfiguration($sku, $stock->getStockId(), $stockItemConfiguration);

        $this->createSourceItemForProductNotManagingStock($sku, $websiteId);
    }

    /**
     * Create source item for the product configured to not manage stock to work around msi bug
     * https://github.com/magento-engcom/msi/issues/2097
     *
     * @param string $sku
     * @param int $websiteId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createSourceItemForProductNotManagingStock(string $sku, int $websiteId)
    {
        $msiEnabled = $this->msiChecker->isMsiEnabled();
        if (!$msiEnabled) {
            return;
        }

        $stock = $this->msiApi->getStockByWebsiteId($websiteId);
        $stockId = $stock->getStockId();
        $stockItemConfiguration = $this->getStockItemConfiguration($sku, $stockId);

        if (!$stockItemConfiguration->isManageStock()) {
            $sourceItems = $this->msiApi->getSourceItemsBySku($sku);
            $sourceCodesForSku = [];
            foreach ($sourceItems as $sourceItem) {
                $sourceCodesForSku[] = $sourceItem->getSourceCode();
            }

            $sourcesAssignedToStock = $this->msiApi->getSourceAssignedToStock($stockId);
            $sourceCodesForStock = [];
            foreach ($sourcesAssignedToStock as $source) {
                if (in_array($source->getSourceCode(), $sourceCodesForSku, true)) {
                    return;
                }
                $sourceCodesForStock[] = $source->getSourceCode();
            }

            if (!empty($sourceCodesForStock)) {
                $sourceItem = $this->msiApi->createSourceItem();
                $sourceItem->setSourceCode($sourceCodesForStock[0]);
                $sourceItem->setSku($sku);
                $sourceItem->setQuantity(0.00);
                $sourceItem->setStatus(1);

                $this->msiApi->saveSourceItems([$sourceItem]);
            }
        }
    }

    /**
     * @param string $sku
     * @param int $stockId
     * @return \Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    private function getStockItemConfiguration(
        string $sku,
        int $stockId
    ) {
        $stockItemConfiguration = null;
        try {
            $stockItemConfiguration = $this->msiApi->getStockItemConfiguration($sku, $stockId);
        } catch (\Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException $e) {
            $defaultStockId = $this->msiApi->getDefaultStockId();
            $stockItemConfiguration = $this->msiApi->getStockItemConfiguration($sku, $defaultStockId);
        }

        return $stockItemConfiguration;
    }
}
