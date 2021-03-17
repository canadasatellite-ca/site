<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Msi;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Validation\ValidationException;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\StockInterface;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\SaveStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;

/**
 * Class MsiApi
 */
class MsiApi
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * @var StockIndexTableNameResolverInterface
     */
    private $stockIndexTableNameResolver;

    /**
     * @var PlaceReservationsForSalesEventInterface
     */
    private $placeReservationsForSalesEvent;

    /**
     * @var SaveStockItemConfigurationInterface
     */
    private $saveStockItemConfiguration;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProvider;

    /**
     * @var GetSourceItemsBySkuInterface
     */
    private $getSourceItemsBySku;

    /**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    private $getSourcesAssignedToStock;

    /**
     * @var SourceItemsSaveInterface
     */
    private $sourceItemsSave;

    /**
     * MsiApi constructor.
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $websiteId
     * @return StockInterface
     */
    public function getStockByWebsiteId(int $websiteId): StockInterface
    {
        if ($this->stockByWebsiteIdResolver === null) {
            $this->stockByWebsiteIdResolver = $this->objectManager->get(
                StockByWebsiteIdResolverInterface::class
            );
        }

        return $this->stockByWebsiteIdResolver->execute($websiteId);
    }

    /**
     * @param int $stockId
     * @return string
     */
    public function getStockIndexTableName(int $stockId): string
    {
        if ($this->stockIndexTableNameResolver === null) {
            $this->stockIndexTableNameResolver = $this->objectManager->get(
                StockIndexTableNameResolverInterface::class
            );
        }
        return $this->stockIndexTableNameResolver->execute($stockId);
    }

    /**
     * @param string $sku
     * @param float $qty
     * @return ItemToSellInterface
     */
    public function createItemsToSell(string $sku, float $qty): ItemToSellInterface
    {
        return $this->objectManager->create(
            ItemToSellInterface::class,
            [
                'sku' => $sku,
                'qty' => $qty
            ]
        );
    }

    /**
     * @param string $websiteCode
     * @return SalesChannelInterface
     */
    public function createSalesChannel(string $websiteCode): SalesChannelInterface
    {
        return $this->objectManager->create(
            SalesChannelInterface::class,
            [
                'data' => [
                    'type' => SalesChannelInterface::TYPE_WEBSITE,
                    'code' => $websiteCode
                ]
            ]
        );
    }

    /**
     * @param int $magentoOrderId
     * @return SalesEventInterface
     */
    public function createSalesEvent(int $magentoOrderId): SalesEventInterface
    {
        return $this->objectManager->create(
            SalesEventInterface::class,
            [
                'type' => 'revert_fba_reservation',
                'objectType' => 'order',
                'objectId' => $magentoOrderId
            ]
        );
    }

    /**
     * @param ItemToSellInterface[] $items
     * @param SalesChannelInterface $salesChannel
     * @param SalesEventInterface $salesEvent
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     */
    public function placeReservationsForSalesEvent(
        array $items,
        SalesChannelInterface $salesChannel,
        SalesEventInterface $salesEvent
    ) {
        if ($this->placeReservationsForSalesEvent === null) {
            $this->placeReservationsForSalesEvent = $this->objectManager->get(
                PlaceReservationsForSalesEventInterface::class
            );
        }
        $this->placeReservationsForSalesEvent->execute($items, $salesChannel, $salesEvent);
    }

    /**
     * @param string $sku
     * @param int $stockId
     * @param StockItemConfigurationInterface $stockItemConfiguration
     * @return void
     */
    public function saveStockItemConfiguration(
        string $sku,
        int $stockId,
        StockItemConfigurationInterface $stockItemConfiguration
    ) {
        if ($this->saveStockItemConfiguration === null) {
            $this->saveStockItemConfiguration = $this->objectManager->get(
                SaveStockItemConfigurationInterface::class
            );
        }
        $this->saveStockItemConfiguration->execute($sku, $stockId, $stockItemConfiguration);
    }

    /**
     * @return StockItemConfigurationInterface
     */
    public function createStockItemConfiguration(): StockItemConfigurationInterface
    {
        return $this->objectManager->create(StockItemConfigurationInterface::class);
    }

    /**
     * @param string $sku
     * @param int $stockId
     * @return StockItemConfigurationInterface
     * @throws LocalizedException
     * @throws SkuIsNotAssignedToStockException
     */
    public function getStockItemConfiguration(
        string $sku,
        int $stockId
    ): StockItemConfigurationInterface {
        if ($this->getStockItemConfiguration === null) {
            $this->getStockItemConfiguration = $this->objectManager->get(
                GetStockItemConfigurationInterface::class
            );
        }
        return $this->getStockItemConfiguration->execute($sku, $stockId);
    }

    /**
     * @return int
     */
    public function getDefaultStockId(): int
    {
        if ($this->defaultStockProvider === null) {
            $this->defaultStockProvider = $this->objectManager->get(
                DefaultStockProviderInterface::class
            );
        }
        return $this->defaultStockProvider->getId();
    }

    /**
     * @param string $sku
     * @return SourceItemInterface[]
     */
    public function getSourceItemsBySku(string $sku): array
    {
        if ($this->getSourceItemsBySku === null) {
            $this->getSourceItemsBySku = $this->objectManager->get(
                GetSourceItemsBySkuInterface::class
            );
        }
        return $this->getSourceItemsBySku->execute($sku);
    }

    /**
     * @param int $stockId
     * @return SourceInterface[]
     * @throws LocalizedException
     */
    public function getSourceAssignedToStock(int $stockId): array
    {
        if ($this->getSourcesAssignedToStock === null) {
            $this->getSourcesAssignedToStock = $this->objectManager->get(
                GetSourcesAssignedToStockOrderedByPriorityInterface::class
            );
        }
        return $this->getSourcesAssignedToStock->execute($stockId);
    }

    /**
     * @param SourceItemInterface[] $sourceItems
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws ValidationException
     */
    public function saveSourceItems(array $sourceItems)
    {
        if ($this->sourceItemsSave === null) {
            $this->sourceItemsSave = $this->objectManager->get(
                SourceItemsSaveInterface::class
            );
        }
        $this->sourceItemsSave->execute($sourceItems);
    }

    /**
     * @return SourceItemInterface
     */
    public function createSourceItem(): SourceItemInterface
    {
        return $this->objectManager->create(SourceItemInterface::class);
    }
}
