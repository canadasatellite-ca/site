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
 * Class ReadHandler
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\SharedStores
 */
class ReadHandler implements ExtensionInterface
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
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->getConnection();
            $tableName = $this->getSharedStoresTableName();
            $select = $connection->select()
                ->from($tableName, 'store_id')
                ->where('review_id = :id');
            $sharedStoreIds = $connection->fetchCol($select, ['id' => $entityId]);
            $entity->setSharedStoreIds($sharedStoreIds);
        }
        return $entity;
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
