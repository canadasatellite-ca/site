<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status as ReviewStatusSource;

/**
 * Class Approve
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 */
class Approve extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::reviews';

    /**
     * @var ReviewManagementInterface
     */
    private $reviewManagement;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @param Context $context
     * @param ReviewManagementInterface $reviewManagement
     * @param ReviewRepositoryInterface $reviewRepository
     */
    public function __construct(
        Context $context,
        ReviewManagementInterface $reviewManagement,
        ReviewRepositoryInterface $reviewRepository
    ) {
        parent::__construct($context);
        $this->reviewManagement = $reviewManagement;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($reviewId = $this->getRequest()->getParam(ReviewInterface::ID, false)) {
            try {
                $review = $this->reviewRepository->getById($reviewId);
                $review->setStatus(ReviewStatusSource::APPROVED);
                $this->reviewManagement->updateReview($review);
                $this->messageManager->addSuccessMessage(__('The review was successfully approved.'));
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        return $resultRedirect->setRefererUrl();
    }
}
