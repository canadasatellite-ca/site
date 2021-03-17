<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassDelete
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class MassDelete extends AbstractMassAction
{
    /**
     * Delete comments
     *
     * @param CommentInterface[] $comments
     */
    protected function massAction($comments)
    {
        $deletedRecords = 0;
        /** @var CommentInterface $item */
        foreach ($comments as $item) {
            try {
                $this->commentManagement->deleteComment($item);
                $deletedRecords++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        if ($deletedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $deletedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records were deleted.'));
        }
    }
}
