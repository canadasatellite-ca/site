<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\Attachment;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;

/**
 * Class SaveHandler
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\Attachment
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
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->tableName = $this->resourceConnection->getTableName(ReviewResourceModel::REVIEW_ATTACHMENT_TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $this
            ->removeOld($entity)
            ->insertNew($entity);

        return $entity;
    }

    /**
     * Remove old attachments
     *
     * @param ReviewInterface $entity
     * @return $this
     * @throws \Exception
     */
    private function removeOld($entity)
    {
        $this
            ->getConnection()
            ->delete($this->tableName, [ReviewAttachmentInterface::REVIEW_ID . ' = ?' => $entity->getId()]);
        return $this;
    }

    /**
     * Insert new attachments
     *
     * @param ReviewInterface $entity
     * @return $this
     * @throws \Exception
     */
    private function insertNew($entity)
    {
        $attachmentsToInsert = [];
        if (is_array($entity->getAttachments())) {
            /** @var ReviewAttachmentInterface $attachment */
            foreach ($entity->getAttachments() as $attachment) {
                $attachmentsToInsert[] = [
                    ReviewAttachmentInterface::REVIEW_ID => $entity->getId(),
                    ReviewAttachmentInterface::NAME => $attachment->getName(),
                    ReviewAttachmentInterface::FILE_NAME => $attachment->getFileName()
                ];
            }
        }
        if ($attachmentsToInsert) {
            $this->getConnection()->insertMultiple($this->tableName, $attachmentsToInsert);
        }
        return $this;
    }

    /**
     * Retrieve connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(ReviewInterface::class)->getEntityConnectionName()
        );
    }
}
