<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Listing;

use Magento\Amazon\Api\MultipleRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Multiple as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Multiple\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MultipleRepository
 */
class MultipleRepository implements MultipleRepositoryInterface
{
    /** @var MultipleFactory $multipleFactory */
    protected $multipleFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param MultipleFactory $multipleFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        MultipleFactory $multipleFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->multipleFactory = $multipleFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        /** @var MultipleFactory $multiple */
        $multiple = $this->multipleFactory->create();
        $multiple->load($id);
        if (!$multiple->getId()) {
            throw new NoSuchEntityException(
                __('The requested multiple was not found')
            );
        }

        return $multiple;
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
