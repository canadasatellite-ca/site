<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface CommentManagementInterface
 * @package Aheadworks\AdvancedReviews\Api
 */
interface CommentManagementInterface
{
    /**
     * Create new customer comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @param int|null $storeId
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addCustomerComment(\Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment, $storeId = null);

    /**
     * Create new admin comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addAdminComment(\Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment);

    /**
     * Update existing comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateComment(\Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment);

    /**
     * Delete existing comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteComment(\Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment);

    /**
     * Delete existing comment by id
     *
     * @param int $commentId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteCommentById($commentId);
}
