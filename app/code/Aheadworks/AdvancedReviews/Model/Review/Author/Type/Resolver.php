<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author\Type;

use Magento\Store\Model\Store;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\AuthorType
 */
class Resolver
{
    /**
     * Retrieve author type
     *
     * @param int $storeId
     * @param int|null $customerId
     * @return int
     */
    public function resolveAuthorType($storeId, $customerId)
    {
        $authorType = AuthorType::GUEST;
        if (!empty($customerId)) {
            $authorType = AuthorType::CUSTOMER;
        } elseif ($storeId == Store::DEFAULT_STORE_ID) {
            $authorType = AuthorType::ADMIN;
        }
        return $authorType;
    }
}
