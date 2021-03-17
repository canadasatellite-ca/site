<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\OrderItem;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResource;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class ReadHandler
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\OrderItem
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
            $tableName = $this->getReminderOrderItemTable();

            $select = $connection->select()
                ->from($tableName, 'order_item_id')
                ->where('review_id = :id');
            $orderItemId = $connection->fetchOne($select, ['id' => $entityId]);
            $entity->setOrderItemId($orderItemId);
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
     * Get reminder order item table name
     *
     * @return string
     */
    private function getReminderOrderItemTable()
    {
        return $this->resourceConnection->getTableName(ReviewResource::REMINDER_ORDER_ITEM_TABLE_NAME);
    }
}
