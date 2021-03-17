<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Store;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Store
 */
class Resolver
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get website id by store id
     *
     * @param int $storeId
     * @return int|null
     */
    public function getWebsiteIdByStoreId($storeId)
    {
        try {
            /** @var StoreInterface $store */
            $store = $this->storeManager->getStore($storeId);
            $websiteId = $store->getWebsiteId();
        } catch (NoSuchEntityException $e) {
            $websiteId = null;
        }

        return $websiteId;
    }
}
