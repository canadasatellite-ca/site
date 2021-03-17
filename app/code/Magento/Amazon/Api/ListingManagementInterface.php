<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface ListingManagementInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ListingManagementInterface
{
    /**
     * Returns count by listing status
     *
     * @param array $statuses
     * @param int $merchantId
     * @return int
     */
    public function getCountByListStatus(array $statuses, $merchantId);

    /**
     * Returns listing count that have a sub new condition
     *
     * @param int $merchantId
     * @return bool
     */
    public function isSubNewConditionListing($merchantId);

    /**
     * Returns listing count by overrides
     *
     * @param int $merchantId
     * @return int
     */
    public function getCountByOverrides($merchantId);

    /**
     * Set a list price override for a particular listing
     *
     * @param int $id
     * @param float $override
     * @return void
     */
    public function setPriceOverride($id, $override = null);

    /**
     * Parses listing data in an attempt to locate a matching
     * catalog product based on user search criteria
     *
     * Returns array in the following format or false if no match
     *
     * $productData = [
     *    'product_id' => $productId,
     *    'product_type' => $productIdType
     * ];
     *
     * @param AccountListingInterface $account
     * @param ProductInterface $product
     * @return bool|array
     */
    public function isCatalogMatch(AccountListingInterface $account, ProductInterface $product);

    /** Attempts to match listing to Magento catalog
     *
     * @param string $merchantId
     * @param array $ids
     * @return int
     */
    public function insertUnmatchedListing($merchantId, array $ids = []);

    /**
     * Adds eligible Magento products to Amazon marketplace
     *
     * $ids[] = [
     *     'id' => xxx (where id must be catalog product id)
     * ];
     *
     * @param int[] $ids
     * @param int $merchantId
     * @return void
     */
    public function insertByProductIds(array $ids, $merchantId);

    /**
     * Sets product eligibility according to $eligible flag by
     * catalog product ids
     *
     * @param array $ids
     * @param int $merchantId
     * @param bool $eligible
     * @return void
     */
    public function setEligibilityByProductIds(array $ids, int $merchantId, $eligible = true);

    /**
     * Processes listing removals
     *
     * @param string $sellerSku
     * @param int $merchantId
     * @return void
     */
    public function removeListing(string $sellerSku, int $merchantId);

    /**
     * Schedules new listings for insertion on Amazon marketplace if account
     * setting is to automatically list products
     *
     * @param int $merchantId
     * @return void
     */
    public function scheduleListingInsertions(int $merchantId);
}
