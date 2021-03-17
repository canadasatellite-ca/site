<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Indexer\Attribute;

use Magento\Amazon\Api\AttributeRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value as ValueResourceModel;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AbstractAction
 */
abstract class AbstractAction
{
    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var ValueResourceModel $valueResourceModel */
    protected $valueResourceModel;
    /** @var ResourceConnection $resourceConnection */
    protected $resourceConnection;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ValueResourceModel $valueResourceModel
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        ValueResourceModel $valueResourceModel,
        ResourceConnection $resourceConnection
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->valueResourceModel = $valueResourceModel;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Reindex all
     *
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    public function reindexAll()
    {
        $this->synchronizeAttributes();
    }

    /**
     * Reindex partial by ids
     *
     * @param null | array
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    public function reindexPartial($ids)
    {
        /** @var array */
        $ids = array_unique($ids);
        $this->synchronizeAttributes($ids);
    }

    /**
     * Updates product listing eligibility and stock levels
     *
     *
     * @return void
     * @throws \Zend_Db_Select_Exception
     * @var int | array $ids
     */
    private function synchronizeAttributes($ids = null)
    {

        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();

        $sql = $connection->select()->from(
            ['attribute' => $this->resourceConnection->getTableName('channel_amazon_attribute')],
            []
        )->joinInner(
            ['value' => $this->resourceConnection->getTableName('channel_amazon_attribute_value')],
            'value.parent_id = attribute.id',
            []
        )->where(
            'attribute.is_active = ?',
            (int)1
        )->where(
            'value.status = ?',
            (int)0
        )->columns(
            [
                'id' => 'attribute.id'
            ]
        )->group('attribute.id');

        // if id filter
        if ($ids) {
            $sql->where(
                'attribute.id IN (?)',
                $ids
            );
        }

        // import attributes
        foreach ($connection->fetchAll($sql) as $row) {
            /** @var int */
            $id = (isset($row['id'])) ? $row['id'] : false;

            if (!$id) {
                continue;
            }

            try {
                $attribute = $this->attributeRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $this->valueResourceModel->importAmazonAttributeValues($attribute);
        }
    }
}
