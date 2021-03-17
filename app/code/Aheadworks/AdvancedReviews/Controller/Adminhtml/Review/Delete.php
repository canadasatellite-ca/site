<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 */
class Delete extends Action
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
     * @param Context $context
     * @param ReviewManagementInterface $reviewManagement
     */
    public function __construct(
        Context $context,
        ReviewManagementInterface $reviewManagement
    ) {
        parent::__construct($context);
        $this->reviewManagement = $reviewManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($reviewId = $this->getRequest()->getParam('id', false)) {
            try {
                $this->reviewManagement->deleteReviewById($reviewId);
                $this->messageManager->addSuccessMessage(__('The review was successfully deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (CouldNotDeleteException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while deleting the review.')
                );
            }
            return $resultRedirect->setPath('*/*/edit', ['id' => $reviewId]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
