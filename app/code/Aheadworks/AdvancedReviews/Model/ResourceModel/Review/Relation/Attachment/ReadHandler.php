<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\Attachment;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;

/**
 * Class ReadHandler
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Relation\Attachment
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
     * @var ReviewAttachmentInterfaceFactory
     */
    private $reviewAttachmentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param ReviewAttachmentInterfaceFactory $reviewAttachmentFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        ReviewAttachmentInterfaceFactory $reviewAttachmentFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->reviewAttachmentFactory = $reviewAttachmentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->tableName = $this->resourceConnection->getTableName(ReviewResourceModel::REVIEW_ATTACHMENT_TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entity->setAttachments($this->getAttachmentsData($entity));
        return $entity;
    }

    /**
     * Retrieve attachments data
     *
     * @param ReviewInterface $entity
     * @return array
     * @throws \Exception
     */
    private function getAttachmentsData($entity)
    {
        $attachments = [];
        $attachmentsData = $this->extractAttachmentsDataFromDb($entity);
        foreach ($attachmentsData as $attachmentData) {
            $attachmentEntity = $this->reviewAttachmentFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $attachmentEntity,
                $attachmentData,
                ReviewAttachmentInterface::class
            );
            $attachments[] = $attachmentEntity;
        }
        return $attachments;
    }

    /**
     * Extract attachments data from db
     *
     * @param ReviewInterface $entity
     * @return array
     * @throws \Exception
     */
    private function extractAttachmentsDataFromDb($entity)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->tableName)
            ->where(ReviewAttachmentInterface::REVIEW_ID . ' = :id');

        return $connection->fetchAll($select, ['id' => $entity->getId()]);
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
