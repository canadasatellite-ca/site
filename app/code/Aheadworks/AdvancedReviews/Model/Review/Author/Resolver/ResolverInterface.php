<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author\Resolver;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Interface ResolverInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Author\Resolver
 */
interface ResolverInterface
{
    /**
     * Get author backend label
     *
     * @param int|null $customerId
     * @return \Magento\Framework\Phrase|string
     */
    public function getBackendLabel($customerId);

    /**
     * Get author backend url
     *
     * @param int|null $customerId
     * @return string
     */
    public function getBackendUrl($customerId);

    /**
     * Get author name
     *
     * @param ReviewInterface $review
     * @return string|null
     */
    public function getName($review);

    /**
     * Get author email
     *
     * @param ReviewInterface $review
     * @return string|null
     */
    public function getEmail($review);
}
