<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\GraphQl\CollectionFilterApplier;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Collection;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Amazon\Ui\AdminStorePageUrl;
use Zend_Db_Expr;

class StoreOrders
{
    /**
     * @var array|null
     */
    private $orders;
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;
    /**
     * @var AdminStorePageUrl
     */
    private $adminStorePageUrl;
    /**
     * @var Stores
     */
    private $stores;
    /**
     * @var CollectionFilterApplier
     */
    private $filterApplier;

    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        AdminStorePageUrl $adminStorePageUrl,
        Stores $stores,
        CollectionFilterApplier $filterApplier
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->adminStorePageUrl = $adminStorePageUrl;
        $this->stores = $stores;
        $this->filterApplier = $filterApplier;
    }

    private function loadOrders(array $storeIds, array $fields, array $filters, string $hashKey, Context $context): void
    {
        $data = [];
        foreach ($storeIds as $storeId) {
            /** @var array $order */
            $data[$hashKey] = $this->getOrders(
                $storeId,
                $fields,
                $filters,
                $hashKey,
                $context
            );
        }

        $this->orders = is_array($this->orders) ? array_merge($this->orders, $data) : $data;
    }

    public function getOrdersForStore(array $filters, string $hashKey, Context $context): StoreOrdersResult
    {
        if (null === $this->orders || !array_key_exists($hashKey, $this->orders)) {
            $this->loadOrders(
                $context->storeOrders()->ids()->getAll(),
                $context->storeOrders()->orderFields()->getAll(),
                $filters,
                $hashKey,
                $context
            );
        }
        return isset($this->orders[$hashKey]) ? $this->orders[$hashKey] : new StoreOrdersResult([], 0, false);
    }

    /**
     * @param int $storeId
     * @param array $fields
     * @param array $filters
     * @param string $hashKey
     * @param Context $context
     * @return StoreOrdersResult
     * @throws \Magento\Amazon\GraphQl\ValidationException
     */
    private function getOrders(
        int $storeId,
        array $fields,
        array $filters,
        string $hashKey,
        Context $context
    ): StoreOrdersResult {
        $limit = (int)$context->storeOrders()->limits()->get($hashKey);
        $cursor = $context->storeOrders()->cursors()->get($hashKey);
        $sort = $context->storeOrders()->sorts()->get($hashKey);
        $isCalculateTotalCount = $context->storeOrders()->isCalculateTotalCounts()->get($hashKey);

        /** @var Collection $collection */
        $collection = $this->orderCollectionFactory->create();
        $fieldsMap = [
            'orderId' => 'order_id',
            'magentoOrderNumber' => 'sales_order_number',
            'status' => 'status',
            'buyerName' => 'buyer_name',
            'currency' => 'currency',
            'total' => 'total',
            'notes' => 'notes',
            'magentoOrderId' => 'sales_order_id',
            'purchaseDate' => 'purchase_date',
            'fulfillmentChannel' => 'fulfillment_channel',
            'latestShipDate' => 'latest_ship_date',
            'id' => 'id',
            'buyerEmail' => 'buyer_email',
            'reserved' => 'reserved',
            'isPremium' => 'is_premium',
            'isPrime' => 'is_prime',
            'shipServiceLevel' => 'ship_service_level',
            'shipService' => 'service_level',
            'shippedByAmazon' => 'shipped_by_amazon',
            'isBusiness' => 'is_business',
            'itemsShipped' => 'items_shipped',
            'itemsUnshipped' => 'items_unshipped',
            'shipAddressOne' => 'ship_address_one',
            'shipAddressTwo' => 'ship_address_two',
            'shipAddressThree' => 'ship_address_three',
            'shipCity' => 'ship_city',
            'shipRegion' => 'ship_region',
            'shipPostalCode' => 'ship_postal_code',
            'shipCountry' => 'ship_country',
            'shipPhone' => 'ship_phone'
        ];
        // merge always loaded fields with requested fields
        $selectedFields = array_merge(
            array_intersect_key($fieldsMap, $fields),
            [
                'id' => 'id',
                'merchant_id' => 'merchant_id',
                'magentoOrderId' => 'sales_order_id',
                'purchaseDate' => 'purchase_date',
                'orderId' => 'order_id'
            ]
        );
        $collection->addFieldToFilter('merchant_id', ['eq' => $storeId]);
        $this->filterApplier->apply($collection, $filters, $selectedFields);
        $selectedFields = $this->applyDateMask($selectedFields);
        $collection->addFieldToSelect($selectedFields);
        if (!empty($fields['magentoWebsite'])) {
            $collection->getSelect()->joinLeft(
                ['rule' => $collection->getResource()->getTable('channel_amazon_listing_rule')],
                'main_table.merchant_id = rule.merchant_id',
                ['rule.website_id']
            );
        }
        $totalCount = $isCalculateTotalCount ? $this->getTotalCount($collection) : null;
        $this->applyPagination($collection, $cursor, $limit);
        $this->applySorting($collection, $sort);
        $ordersData = $collection->getData();
        $ordersData = $this->addUrls($storeId, $fields, $context, $ordersData);
        $ordersData = $this->generateResponseSchema($ordersData, $context, (int)$this->getOffset($cursor));

        return new StoreOrdersResult($ordersData, $totalCount, 1 < $this->getLastPageNumber($collection, $limit));
    }

    /**
     * @param int $storeId
     * @param array $fields
     * @param Context $context
     * @param array $ordersData
     * @return array
     */
    private function addUrls(int $storeId, array $fields, Context $context, array $ordersData): array
    {
        $needsMagentoOrderUrl = !empty($fields['magentoOrderUrl']);
        $needsOrderDetailsUrl = !empty($fields['orderDetailsUrl']);

        if (!$needsMagentoOrderUrl && !$needsOrderDetailsUrl) {
            return $ordersData;
        }

        $store = $this->stores->getSingleStoreById($storeId, $context);

        foreach ($ordersData as $key => $data) {
            if ($needsMagentoOrderUrl) {
                $ordersData[$key]['magentoOrderUrl'] = !empty($data['magentoOrderId'])
                    ? $this->adminStorePageUrl->magentoOrderUrl($data['magentoOrderId'])
                    : null;
            }
            if ($needsOrderDetailsUrl) {
                $ordersData[$key]['orderDetailsUrl'] = $store
                    ? $this->adminStorePageUrl->orderDetails($store, (int)$data['id'])
                    : null;
            }
        }

        return $ordersData;
    }

    private function getLastPageNumber(
        \Magento\Amazon\Model\ResourceModel\Amazon\Order\Collection $collection,
        int $limit
    ): int {
        $collection->setPageSize($limit);
        return (int)$collection->getLastPageNumber();
    }

    private function getCursor(int $curOffset, int $startOffset): string
    {
        return base64_encode((string)($curOffset + $startOffset + 1));
    }

    private function getTotalCount(Collection $collection): int
    {
        return $collection->getSize();
    }

    private function getOffset(?string $cursor): string
    {
        if ($cursor && base64_decode($cursor, true) === false) {
            throw new \Magento\Amazon\GraphQl\ValidationException('Cursor must be a valid base64 string');
        }
        return base64_decode($cursor);
    }

    private function applyPagination(Collection $collection, ?string $cursor, int $limit): void
    {
        $collection->getSelect()->limit($limit, $this->getOffset($cursor));
    }

    private function applySorting(Collection $collection, array $sort): void
    {
        $collection->addOrder($sort['field'], $sort['order']);
        if ($sort['field'] !== 'id') {
            $collection->addOrder('id', 'DESC');
        }
    }

    /**
     * This transformation must be done after filters are applied or the filters won't work
     * and before the field selection is added to the $collection or the date fields won't be formatted correctly
     *
     * @param array $selectedFields
     * @return array
     */
    private function applyDateMask(array $selectedFields): array
    {
        $dateFields = [
            'purchaseDate',
            'latestShipDate'
        ];
        foreach ($selectedFields as $field => $value) {
            if (in_array($field, $dateFields, true)) {
                $selectedFields[$field] = new Zend_Db_Expr(
                    sprintf("DATE_FORMAT(CONVERT_TZ(`%s`, @@session.time_zone, '+00:00'),'%s')", $value, '%Y-%m-%dT%TZ')
                );
            }
        }
        return $selectedFields;
    }

    private function generateResponseSchema(array $ordersData, Context $context, int $startingOffset)
    {
        $edgeFields = $context->storeOrders()->edgeFields()->getAll();
        $needsCursor = isset($edgeFields['cursor']);
        $results = [];
        $lastId = null;
        foreach ($ordersData as $key => $order) {
            $data = [
                'node' => $order,
            ];
            if ($needsCursor) {
                $data['cursor'] = $this->getCursor($key, $startingOffset);
            }
            $results[$key] = $data;
            $lastId = $key;
        }
        if (!$needsCursor
            && $lastId
            && $context->storeOrders()->pageFields()->getAll()
        ) {
            $results[$lastId]['cursor'] = $this->getCursor($lastId, $startingOffset);
        }
        return $results;
    }
}
