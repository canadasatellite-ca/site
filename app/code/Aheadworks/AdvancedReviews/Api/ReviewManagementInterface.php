<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface ReviewManagementInterface
 *
 * @package Aheadworks\AdvancedReviews\Api
 */
interface ReviewManagementInterface
{
    /**
     * Create new review
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createReview(\Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review);

    /**
     * Update existing review
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function updateReview(\Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review);

    /**
     * Delete existing review
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteReview(\Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review);

    /**
     * Delete existing review by id
     *
     * @param int $reviewId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteReviewById($reviewId);
}
