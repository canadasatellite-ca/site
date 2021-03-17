<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class MassDelete
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 */
class MassDelete extends AbstractMassAction
{
    /**
     * Delete reviews
     *
     * @param ReviewInterface[] $reviews
     */
    protected function massAction($reviews)
    {
        $deletedRecords = 0;
        /** @var ReviewInterface $item */
        foreach ($reviews as $item) {
            try {
                $this->reviewManagement->deleteReview($item);
                $deletedRecords++;
            } catch (CouldNotDeleteException $e) {
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
