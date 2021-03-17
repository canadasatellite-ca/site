<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class MassIsVerifiedBuyer
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 */
class MassIsVerifiedBuyer extends AbstractMassAction
{
    /**
     * Change is verified buyer flag
     *
     * @param ReviewInterface[] $reviews
     */
    protected function massAction($reviews)
    {
        $verifiedBuyerValue = $this->getVerifiedBuyerValue();
        $count = 0;
        /** @var ReviewInterface $item */
        foreach ($reviews as $item) {
            $item->setIsVerifiedBuyer($verifiedBuyerValue);
            try {
                $this->reviewManagement->updateReview($item);
                $count++;
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $count));
    }

    /**
     * Retrieve verified buyer flag value
     *
     * @return int
     */
    protected function getVerifiedBuyerValue()
    {
        return (int)$this->getRequest()->getParam('verified_buyer_value');
    }
}
