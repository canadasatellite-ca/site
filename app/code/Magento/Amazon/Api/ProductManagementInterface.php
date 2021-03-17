<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

/**
 * Interface ProductManagementInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ProductManagementInterface
{
    /**
     * Removed product from catalog
     *
     * @return void
     */
    public function removeProduct();

    /**
     * Check that url_keys are not assigned to other products in DB
     *
     * @return string
     * @var string $urlKey
     */
    public function getProductUrlDuplicate(string $urlKey);

    /**
     * Update and insert data in entity table.
     *
     * @param array $entities
     * @return array
     */
    public function saveProductEntity(array $entities);

    /**
     * Update and insert website data.
     *
     * @param array $websiteData
     * @param array $products
     * @return void
     */
    public function saveWebsiteIds(array $websiteData, array $products);

    /**
     * Update and insert category data.
     *
     * @param array $categories
     * @param array $products
     * @return $this
     */
    public function saveProductCategories(array $categories, array $products);

    /**
     * Update and insert attribute data.
     *
     * @param array $attributesData
     * @return void
     */
    public function saveAttributes(array $attributesData);

    /**
     * Stock item saving.
     *
     * @param array $stockData
     * @param array $products
     * @return void
     */
    public function saveStockItems(array $stockData, array $products);
}
