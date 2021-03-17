<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;
use Magento\Catalog\Model\Product;

/**
 * Class ProductDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend
 */
class ProductDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductResolver
     */
    protected $productResolver;

    /**
     * @param CollectionFactory $productCollectionFactory
     * @param ProductResolver $productResolver
     */
    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductResolver $productResolver
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productResolver = $productResolver;
    }

    /**
     * Get product data by provided product ids within specific store ID
     *
     * @param array $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductsDataByIds($productIds, $storeId)
    {
        if (is_array($productIds) && count($productIds) > 0) {
            /** @var Collection $productCollection */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->setStoreId($storeId);
            $productCollection->addAttributeToFilter('entity_id', $productIds);
            $productCollection->addAttributeToSelect('name');
            $productCollection->addUrlRewrite();
            $productCollection->load();

            /** @var Product $product */
            foreach ($productCollection as $product) {
                $product->setData(
                    'product_url',
                    $this->getProductUrl($product)
                );
                $product->setData(
                    'prepared_name',
                    $this->getPreparedProductName($product)
                );
            }
            return $productCollection->toArray();
        } else {
            return [];
        }
    }

    /**
     * Retrieve product url
     *
     * @param Product $product
     * @return string
     */
    protected function getProductUrl($product)
    {
        return $product->getUrlModel()->getUrl($product);
    }

    /**
     * Retrieve prepared fot output product name
     *
     * @param Product $product
     * @return string
     */
    protected function getPreparedProductName($product)
    {
        return $this->productResolver->getPreparedProductNameByObject($product);
    }
}
