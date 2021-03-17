<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Review CRUD interface
 * @api
 */
interface ReviewRepositoryInterface
{
    /**
     * Save review
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review);

    /**
     * Delete review
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\AdvancedReviews\Api\Data\ReviewInterface $review);

    /**
     * Retrieve review by id
     *
     * @param int $reviewId
     * @param bool $isForceLoadEnabled
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($reviewId, $isForceLoadEnabled = false);

    /**
     * Retrieve reviews matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
