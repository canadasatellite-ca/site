<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MassStatus
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class MassStatus extends AbstractMassAction
{
    /**
     * {@inheritdoc}
     */
    protected function massAction($comments)
    {
        $status = (int)$this->getRequest()->getParam('status');
        $count = 0;
        /** @var CommentInterface $item */
        foreach ($comments as $item) {
            $item->setStatus($status);
            try {
                $this->commentManagement->updateComment($item);
                $count++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        if ($count) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $count));
        } else {
            $this->messageManager->addSuccessMessage(__('No records were changed.'));
        }
    }
}
