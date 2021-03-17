<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Collection;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Zend_Db_Expr;

class Stores
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    private $allStoresLoaded = false;

    /** @var AccountInterface[] */
    private $stores;

    private $storeIdToUuidMap;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param array $uuids
     * @param \Magento\Amazon\GraphQl\Context $context
     * @return array
     */
    public function getStores(array $uuids, \Magento\Amazon\GraphQl\Context $context): array
    {
        $this->loadRequestedStores($context);
        return !$uuids ? $this->stores : array_intersect_key($this->stores, $uuids);
    }

    private function loadRequestedStores(\Magento\Amazon\GraphQl\Context $context)
    {
        $uuids = $context->stores()->ids()->getAll();
        $fields = $context->stores()->fields()->getAll();
        $isFetchAll = $context->stores()->ids()->isFetchAll() || !$uuids;
        if ($isFetchAll) {
            $uuids = [];
        }
        if ($this->stores === null) {
            // since we don't have any stores loaded, loading all requested stores
            $this->loadStores($uuids, $fields);
        } elseif ($isFetchAll && !$this->allStoresLoaded) {
            $this->loadStores([], $fields);
        } elseif (!$this->allStoresLoaded) {
            $uuids = array_diff_key($uuids, $this->stores);
            if ($uuids) {
                $this->loadStores($uuids, $fields);
            }
        }
    }

    private function loadStores(array $uuids, array $fields): void
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $fieldsMap = [
            'name' => 'name',
            'email' => 'email',
            'authenticationStatus' => 'authentication_status',
            'marketplace' => 'country_code',
            'url' => 'base_url',
            'created' => new Zend_Db_Expr(
                "DATE_FORMAT( CONVERT_TZ(`created_on`, @@session.time_zone, '+00:00')  ,'%Y-%m-%dT%TZ')"
            ),
            'updated' => new Zend_Db_Expr(
                "DATE_FORMAT( CONVERT_TZ(`last_updated`, @@session.time_zone, '+00:00')  ,'%Y-%m-%dT%TZ')"
            ),
        ];
        // merge always loaded fields with requested fields
        $selectFields = array_merge([
            'id' => 'merchant_id',
            'merchant_id' => 'merchant_id',
            'uuid' => 'uuid',
            'country_code' => 'country_code',
            'is_active' => 'is_active',
        ], array_intersect_key($fieldsMap, $fields));
        $collection->addFieldToSelect($selectFields);
        if ($uuids) {
            $collection->addFieldToFilter('uuid', ['in' => $uuids]);
        }
        if (!empty($fields['magentoWebsite'])) {
            $collection->getSelect()->joinLeft(
                ['rule' => $collection->getResource()->getTable('channel_amazon_listing_rule')],
                'main_table.merchant_id = rule.merchant_id',
                ['magentoWebsite' => 'rule.website_id']
            );
        }
        $data = $this->prepareStores($collection->getItems(), $fields);
        $data = array_combine(array_column($collection->getData(), 'uuid'), $data);
        $this->stores = array_replace_recursive((array)$this->stores, $data);
        if (!$uuids) {
            $this->allStoresLoaded = true;
        }
    }

    /**
     * @param AccountInterface[] $accounts
     * @param array $fields
     * @return AccountInterface[]
     */
    public function prepareStores(array $accounts, array $fields): array
    {
        $result = [];
        $statuses = [
            Definitions::ACCOUNT_STATUS_INACTIVE => 'inactive',
            Definitions::ACCOUNT_STATUS_ACTIVE => 'active',
            Definitions::ACCOUNT_STATUS_INCOMPLETE => 'incomplete',
        ];
        $setStatus = isset($fields['status']);
        foreach ($accounts as $account) {
            if ($setStatus) {
                $status = $statuses[$account->getIsActive()] ?? $statuses[Definitions::ACCOUNT_STATUS_INACTIVE];
                $account->setData('status', $status);
            }
            $result[$account->getUuid()] = $account;
        }
        return $result;
    }

    public function getSingleStore(string $uuid, \Magento\Amazon\GraphQl\Context $context): ?AccountInterface
    {
        $this->loadRequestedStores($context);
        return $this->stores[$uuid] ?? null;
    }

    public function getSingleStoreById(int $id, \Magento\Amazon\GraphQl\Context $context): ?AccountInterface
    {
        $this->loadRequestedStores($context);
        if (null === $this->storeIdToUuidMap) {
            $this->storeIdToUuidMap = [];
            foreach ($this->stores as $store) {
                $this->storeIdToUuidMap[$store->getMerchantId()] = $store->getUuid();
            }
        }

        if (!isset($this->storeIdToUuidMap[$id])) {
            return null;
        }
        $uuid = $this->storeIdToUuidMap[$id];

        return $this->stores[$uuid] ?? null;
    }
}
