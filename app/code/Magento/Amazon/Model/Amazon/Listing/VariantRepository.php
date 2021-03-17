<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Listing;

use Magento\Amazon\Api\VariantRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Variant as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Variant\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class VariantRepository
 */
class VariantRepository implements VariantRepositoryInterface
{
    /** @var VariantFactory $variantFactory */
    private $variantFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param VariantFactory $variantFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        VariantFactory $variantFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->variantFactory = $variantFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id, $flag = false)
    {
        $variant = $this->variantFactory->create();

        $variant->load($id);
        if (!$variant->getId()) {
            throw new NoSuchEntityException(
                __('The requested multiple was not found')
            );
        }

        return $variant;
    }

    /**
     * {@inheritdoc}
     */
    public function getByParentId($parentId)
    {
        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('parent_id', $parentId);

        return $collection;
    }
}
