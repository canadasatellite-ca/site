<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class MassProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 */
class MassProductRecommended extends AbstractMassAction
{
    /**
     * Change product recommended parameter
     *
     * @param ReviewInterface[] $reviews
     */
    protected function massAction($reviews)
    {
        $productRecommendedValue = $this->getProductRecommendedValue();
        $count = 0;
        /** @var ReviewInterface $item */
        foreach ($reviews as $item) {
            $item->setProductRecommended($productRecommendedValue);
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
     * Retrieve product recommended parameter value
     *
     * @return int
     */
    protected function getProductRecommendedValue()
    {
        return (int)$this->getRequest()->getParam('product_recommended_value');
    }
}
