<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class MassStatus
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 */
class MassStatus extends AbstractMassAction
{
    /**
     * Change status
     *
     * @param ReviewInterface[] $reviews
     */
    protected function massAction($reviews)
    {
        $status = (int)$this->getRequest()->getParam('status');
        $count = 0;
        /** @var ReviewInterface $item */
        foreach ($reviews as $item) {
            $item->setStatus($status);
            try {
                $this->reviewManagement->updateReview($item);
                $count++;
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $count));
    }
}
