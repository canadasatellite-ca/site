<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractChangeStatusAction
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
abstract class AbstractChangeStatusAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::reviews';

    /**
     * @var CommentManagementInterface
     */
    protected $commentService;

    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;

    /**
     * @param Context $context
     * @param CommentManagementInterface $commentService
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        Context $context,
        CommentManagementInterface $commentService,
        CommentRepositoryInterface $commentRepository
    ) {
        parent::__construct($context);
        $this->commentService = $commentService;
        $this->commentRepository = $commentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($commentId = $this->getRequest()->getParam(CommentInterface::ID, false)) {
            try {
                $comment = $this->commentRepository->getById($commentId);
                $comment->setStatus($this->getStatusToChange());
                $this->commentService->updateComment($comment);
                $this->messageManager->addSuccessMessage($this->getSuccessMessage());
                return $resultRedirect->setRefererUrl();
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    $this->getErrorMessage()
                );
            }
        }
        return $resultRedirect->setRefererUrl();
    }

    /**
     * Retrieve error message
     *
     * @return string
     */
    private function getErrorMessage()
    {
        return __('Something went wrong while changing status.');
    }

    /**
     * Retrieve status to change
     */
    abstract protected function getStatusToChange();

    /**
     * Retrieve success message
     *
     * @return string
     */
    abstract protected function getSuccessMessage();
}
