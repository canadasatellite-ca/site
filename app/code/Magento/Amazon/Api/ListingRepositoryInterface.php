<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ListingRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ListingRepositoryInterface
{
    /**
     * Saves listing
     *
     * @param ListingInterface $listing
     *
     * @return ListingInterface
     * @throws CouldNotSaveException
     */
    public function save(ListingInterface $listing);

    /**
     * Get listing object by id
     *
     * @param int $id
     * @param boolean $empty
     * @return ListingInterface
     * @throws NoSuchEntityException
     */
    public function getById($id, $empty = false);

    /**
     * Get catalog sku from amazon seller sku
     *
     * @param string $sellerSku
     * @param int $merchantId
     * @return string
     */
    public function getCatalogSkuBySellerSku($sellerSku, $merchantId);

    /**
     * Get listing by from amazon seller sku and merchant id
     *
     * @param string $sellerSku
     * @param int $merchantId
     * @return string
     */
    public function getBySellerSkuAndMerchantId(string $sellerSku, int $merchantId);

    /**
     * Deletes listing
     *
     * @param ListingInterface $listing
     * @return bool
     */
    public function delete(ListingInterface $listing);
}
