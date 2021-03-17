<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::comments';

    /**
     * @var CommentManagementInterface
     */
    private $commentManagement;

    /**
     * @param Context $context
     * @param CommentManagementInterface $commentManagement
     */
    public function __construct(
        Context $context,
        CommentManagementInterface $commentManagement
    ) {
        parent::__construct($context);
        $this->commentManagement = $commentManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($commentId = $this->getRequest()->getParam(CommentInterface::ID, false)) {
            try {
                $this->commentManagement->deleteCommentById($commentId);
                $this->messageManager->addSuccessMessage(__('The comment was deleted successfully.'));
                return $resultRedirect->setRefererUrl();
            } catch (CouldNotDeleteException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while deleting the comment.')
                );
            }
        }
        return $resultRedirect->setRefererUrl();
    }
}
