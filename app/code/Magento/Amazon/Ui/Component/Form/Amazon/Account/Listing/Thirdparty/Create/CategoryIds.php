<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class CategoryIds
 */
class CategoryIds implements OptionSourceInterface
{
    /** Category tree cache id */
    const CATEGORY_TREE_ID = 'CATALOG_PRODUCT_CATEGORY_TREE';

    /** @var CacheInterface */
    private $cacheManager;
    /** @var CollectionFactory $categoryCollectionFactory */
    private $categoryCollectionFactory;
    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        SerializerInterface $serializer = null
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(SerializerInterface::class);
    }

    /**
     * Get Magento product attribute options
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $categoryTree = $this->getCacheManager()->load(self::CATEGORY_TREE_ID . '_');

        if ($categoryTree) {
            return $this->serializer->unserialize($categoryTree);
        }

        /** @var CollectionFactory */
        $matchingNamesCollection = $this->categoryCollectionFactory->create();

        $matchingNamesCollection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID]);

        /** @var array */
        $shownCategoriesIds = [];

        /** @var Category */
        foreach ($matchingNamesCollection as $category) {
            foreach (explode('/', $category->getPath()) as $parentId) {
                $shownCategoriesIds[$parentId] = 1;
            }
        }

        /** @var CollectionFactory */
        $collection = $this->categoryCollectionFactory->create();

        $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
            ->addAttributeToSelect(['name', 'is_active', 'parent_id']);

        /** @var array */
        $categoryById = [
            CategoryModel::TREE_ROOT_ID => [
                'value' => CategoryModel::TREE_ROOT_ID,
                'optgroup' => null,
            ],
        ];

        foreach ($collection as $category) {
            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];
                }
            }

            $categoryById[$category->getId()]['is_active'] = $category->getIsActive();
            $categoryById[$category->getId()]['label'] = $category->getName();
            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
        }

        $this->getCacheManager()->save(
            $this->serializer->serialize($categoryById[CategoryModel::TREE_ROOT_ID]['optgroup']),
            self::CATEGORY_TREE_ID . '_',
            [
                Category::CACHE_TAG,
                Block::CACHE_TAG
            ]
        );

        return $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
    }

    /**
     * Retrieve cache interface
     *
     * @return CacheInterface
     */
    private function getCacheManager()
    {
        if (!$this->cacheManager) {
            $this->cacheManager = ObjectManager::getInstance()
                ->get(CacheInterface::class);
        }

        return $this->cacheManager;
    }
}
