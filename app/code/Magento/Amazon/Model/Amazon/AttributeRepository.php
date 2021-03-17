<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\AttributeRepositoryInterface;
use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class AttributeRepository
 */
class AttributeRepository implements AttributeRepositoryInterface
{
    /** @var AttributeFactory $attributeFactory */
    protected $attributeFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param AttributeFactory $attributeFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AttributeInterface $attribute)
    {
        try {
            $this->resourceModel->save($attribute);
        } catch (\Exception $e) {
            $phrase = __('Unable to save the attribute setting. Please try again.');
            throw new CouldNotSaveException($phrase);
        }

        return $attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        /** @var AttributeFactory */
        $attribute = $this->attributeFactory->create();

        return $attribute->load($id);
    }
}
