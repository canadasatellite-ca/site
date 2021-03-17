<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Comment CRUD interface
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Save comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment);

    /**
     * Delete comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment);

    /**
     * Retrieve comment by id
     *
     * @param int $commentId
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($commentId);

    /**
     * Retrieve comment ids by review ids
     *
     * @param array $reviewIds
     * @return array
     */
    public function getIdsByReviewIds($reviewIds);

    /**
     * Retrieve comments matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
