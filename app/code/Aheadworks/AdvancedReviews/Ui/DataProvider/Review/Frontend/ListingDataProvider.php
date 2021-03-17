<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Ui\DataProvider\AbstractDataProvider as UiAbstractDataProvider;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Frontend\CollectionFactory as FrontendReviewCollectionFactory;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend
 */
class ListingDataProvider extends UiAbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param FrontendReviewCollectionFactory $frontendReviewCollectionFactory
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param PoolInterface $pool
     * @param ProductDataProvider $productDataProvider
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        FrontendReviewCollectionFactory $frontendReviewCollectionFactory,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        PoolInterface $pool,
        ProductDataProvider $productDataProvider,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $this->createReviewCollection($frontendReviewCollectionFactory);
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->pool = $pool;
        $this->productDataProvider = $productDataProvider;
    }

    /**
     * Create review collection
     *
     * @param FrontendReviewCollectionFactory $frontendReviewCollectionFactory
     * @return Collection
     */
    protected function createReviewCollection($frontendReviewCollectionFactory)
    {
        return $frontendReviewCollectionFactory->create();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->prepareCollectionForLoading();

        $this->getCollection()->load();
        $data = $this->getCollection()->toArray();
        $data['items'] = $this->extendDataWithProductData($data['items']);
        $data['items'] = $this->prepareData($data['items']);
        $data['imageByPages'] = $this->getCollection()->getImageCountByPages();
        return $data;
    }

    /**
     * Perform all necessary operations to prepare collection for loading
     *
     * @return $this
     */
    private function prepareCollectionForLoading()
    {
        $this->applyFiltersFromRequest();
        $this->applyDefaultFilters();
        return $this;
    }

    /**
     * Prepare data
     *
     * @param array $items
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareData($items)
    {
        foreach ($items as &$item) {
            /** @var ModifierInterface $modifier */
            foreach ($this->pool->getModifiersInstances() as $modifier) {
                $item = $modifier->modifyData($item);
            }
        }

        return $items;
    }

    /**
     * Apply to the collection filters from the current request
     *
     * @return $this
     */
    protected function applyFiltersFromRequest()
    {
        return $this;
    }

    /**
     * Apply default filters to the collection
     *
     * @return $this
     */
    protected function applyDefaultFilters()
    {
        $this->getCollection()->addStoreFilter($this->getCurrentStoreId());
        return $this;
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    protected function getCurrentStoreId()
    {
        return $this->storeManager->getStore(true)->getId();
    }

    /**
     * Fill up reviews data with additional product data
     *
     * @param array $data
     * @return array
     */
    private function extendDataWithProductData($data)
    {
        $productData = $this->getProductData($data);
        foreach ($data as &$item) {
            $foundData = array_filter(
                $productData,
                function ($product) use ($item) {
                    return $product['entity_id'] == $item[ReviewInterface::PRODUCT_ID];
                }
            );
            if (!empty($foundData)) {
                $product = reset($foundData);
                $item[Collection::PRODUCT_NAME_COLUMN_NAME . '_url'] = $product['product_url'];
                $item[Collection::PRODUCT_NAME_COLUMN_NAME . '_label'] = $product['prepared_name'];
            }
        }

        return $data;
    }

    /**
     * Retrieve product data related to loaded reviews
     *
     * @param array $data
     * @return array
     */
    private function getProductData($data)
    {
        $productIds = [];
        foreach ($data as $item) {
            $productIds[] = $item[ReviewInterface::PRODUCT_ID];
        }
        $preparedProductIds = array_unique($productIds);
        return $this->productDataProvider->getProductsDataByIds($preparedProductIds, $this->getCurrentStoreId());
    }
}
