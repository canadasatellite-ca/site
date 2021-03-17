<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Comment;

use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Type as CommentTypeSource;

/**
 * Class Processor
 * @package Aheadworks\AdvancedReviews\Model\Review\Comment
 */
class Processor
{
    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @param CommentManagementInterface $commentManagement
     */
    public function __construct(
        CommentManagementInterface $commentManagement
    ) {
        $this->commentManagement = $commentManagement;
    }

    /**
     * Process comment
     *
     * @param ReviewInterface $review
     * @throws LocalizedException
     */
    public function processAdminComment(ReviewInterface $review)
    {
        $comment = $review->getAdminComment();
        if ($comment) {
            $comment
                ->setReviewId($review->getId())
                ->setType(CommentTypeSource::ADMIN);
            if ($comment && !$this->isCommentContentEmpty($comment->getContent())) {
                $this->commentManagement->addAdminComment($comment);
            }
        }
    }

    /**
     * Check is comment content empty
     *
     * @param string $content
     * @return bool
     */
    private function isCommentContentEmpty($content)
    {
        return !($content && trim($content));
    }
}
