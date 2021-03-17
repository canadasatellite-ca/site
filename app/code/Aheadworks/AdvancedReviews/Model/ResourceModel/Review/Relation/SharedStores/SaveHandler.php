<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\SharedStores;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResource;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\SharedStore
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $sharedStoreIds = $entity->getSharedStoreIds() ? $entity->getSharedStoreIds() : [];
        $sharedStoreIdsOrig = $this->getSharedStoreIds($entityId);

        $toInsert = array_diff($sharedStoreIds, $sharedStoreIdsOrig);
        $toDelete = array_diff($sharedStoreIdsOrig, $sharedStoreIds);

        if (!empty($toDelete)) {
            $this->removeOldSharedStoresData($entityId, $toDelete);
        }
        if (!empty($toInsert)) {
            $this->insertNewSharedStoresData($entityId, $toInsert);
        }

        return $entity;
    }

    /**
     * Get shared store IDs
     *
     * @param $entityId
     * @return array
     * @throws \Exception
     */
    private function getSharedStoreIds($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getSharedStoresTableName(),
                'store_id'
            )->where('review_id = :id');
        return $connection->fetchCol($select, ['id' => $entityId]);
    }

    /**
     * Remove old shared stores
     *
     * @param int $entityId
     * @param array $toDelete
     * @throws \Exception
     */
    private function removeOldSharedStoresData($entityId, $toDelete)
    {
        $connection = $this->getConnection();
        $tableName = $this->getSharedStoresTableName();

        $connection->delete(
            $tableName,
            ['review_id = ?' => $entityId, 'store_id IN (?)' => $toDelete]
        );
    }

    /**
     * @param int $entityId
     * @param array $toInsert
     * @throws \Exception
     */
    private function insertNewSharedStoresData($entityId, $toInsert)
    {
        $connection = $this->getConnection();
        $tableName = $this->getSharedStoresTableName();
        $data = [];

        foreach ($toInsert as $storeId) {
            $data[] = [
                'review_id' => (int)$entityId,
                'store_id' => (int)$storeId,
            ];
        }

        $connection->insertMultiple($tableName, $data);
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(ReviewInterface::class)->getEntityConnectionName()
        );
    }

    /**
     * Get shared stores table name
     *
     * @return string
     */
    private function getSharedStoresTableName()
    {
        return $this->resourceConnection->getTableName(ReviewResource::SHARED_STORE_TABLE_NAME);
    }
}
