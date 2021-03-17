<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\CustomProductCategoryUrl\Model;

class ProductUrlPathGenerator extends \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator
{
    protected $use_volusion = false;

    /**
     * Retrieve Product Url path (with category if exists)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return string
     */
    public function getUrlPath($product, $category = null)
    {
        $path = $product->getData('url_path');
        if ($path === null) {
            $path = $this->getProductVolusionUrlKey($product)
                ? $this->prepareProductUrlKeyCustom($product)
                : $this->prepareProductDefaultUrlKey($product);
        }
        return $category === null
            ? $path
            : $this->categoryUrlPathGenerator->getUrlPath($category) . '/' . $path;
    }

    /**
     * Prepare URL Key with stored product data (fallback for "Use Default Value" logic)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function prepareProductDefaultUrlKey(\Magento\Catalog\Model\Product $product)
    {
        $storedProduct = $this->productRepository->getById($product->getId());
        //$storedUrlKey = $storedProduct->getUrlKey();
        $storedUrlKey = $this->getProductVolusionUrlKey($storedProduct);
        return $storedUrlKey ?: $product->formatUrlKey($storedProduct->getName());
    }

    /**
     * Prepare url key for product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function prepareProductUrlKeyCustom(\Magento\Catalog\Model\Product $product)
    {
        //$urlKey = $product->getUrlKey();
        $urlKey = $this->getProductVolusionUrlKey($product);
        return $product->formatUrlKey($urlKey === '' || $urlKey === null ? $product->getName() : $urlKey);
    }

    /**
     * Prepare url key for product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getProductVolusionUrlKey($product)
    {
        if ($product->getVolusionUrl() === '' || $product->getVolusionUrl() === null) {
            $urlKey = $product->getUrlKey();
        } else {
            $urlKey = $product->getVolusionUrl();
            $this->use_volusion = true;
        }
        return $urlKey;
    }

    public function getUrlPathWithSuffix($product, $storeId, $category = null)
    {
        $path = $this->getUrlPath($product, $category);
        $path .= $this->getProductUrlSuffix($storeId);
        return $path;
    }
}
