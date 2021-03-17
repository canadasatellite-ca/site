<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Output;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Product
 */
class Resolver
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Output
     */
    private $outputHelper;

    /**
     * @param Registry $registry
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param Output $outputHelper
     */
    public function __construct(
        Registry $registry,
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        Output $outputHelper
    ) {
        $this->registry = $registry;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->outputHelper = $outputHelper;
    }

    /**
     * Extract current product id
     *
     * @return int|null
     */
    public function getCurrentProductId()
    {
        $currentProductId = null;
        $currentProduct = $this->registry->registry('product');
        if (empty($currentProduct) || !($currentProduct instanceof ProductInterface)) {
            $requestId = $this->request->getParam('id');
            try {
                /** @var Product|ProductInterface $product */
                $product = $this->productRepository->getById($requestId);
                $currentProductId = $product->getId();
            } catch (\Exception $e) {
            }
        } else {
            $currentProductId = $currentProduct->getId();
        }
        return $currentProductId;
    }

    /**
     * Retrieve prepared product name by product object
     *
     * @param Product $product
     * @return string
     */
    public function getPreparedProductNameByObject($product)
    {
        try {
            $preparedProductName=$this->outputHelper->productAttribute(
                $product,
                $product->getName(),
                'name'
            );
            return $preparedProductName;
        } catch (LocalizedException $ex) {
            return '';
        }
    }

    /**
     * Retrieve prepared product name by its id
     *
     * @param int $productId
     * @return string
     */
    public function getPreparedProductName($productId)
    {
        try {
            /** @var Product|ProductInterface $product */
            $product=$this->productRepository->getById($productId);
            $preparedProductName=$this->getPreparedProductNameByObject($product);
            return $preparedProductName;
        } catch (NoSuchEntityException $ex) {
            return '';
        }
    }

    /**
     * Retrieve product review url by product object
     *
     * @param Product $product
     * @return string
     */
    public function getProductReviewUrlByObject($product)
    {
        return $product->getUrlModel()->getUrl(
            $product,
            [
                '_fragment' => 'product_aw_reviews_tab',
                '_secure' => true
            ]
        );
    }

    /**
     * Retrieve product review url
     *
     * @param int $productId
     * @return string
     */
    public function getProductReviewUrl($productId)
    {
        try {
            /** @var Product|ProductInterface $product */
            $product=$this->productRepository->getById($productId);
            return $this->getProductReviewUrlByObject($product);
        } catch (NoSuchEntityException $ex) {
            return '';
        }
    }
}
