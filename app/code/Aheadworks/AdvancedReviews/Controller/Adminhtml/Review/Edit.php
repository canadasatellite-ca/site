<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Controller\Adminhtml\Review\AbuseReport\MessageProvider;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::reviews';

    /**
     * {@inheritdoc}
     */
    protected $_publicActions = ['edit'];

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var MessageProvider
     */
    private $messagesProvider;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ReviewRepositoryInterface $reviewRepository
     * @param MessageProvider $messageProvider
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ReviewRepositoryInterface $reviewRepository,
        MessageProvider $messageProvider
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->reviewRepository = $reviewRepository;
        $this->messagesProvider = $messageProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $reviewId = (int)$this->getRequest()->getParam('id');
        if ($reviewId) {
            try {
                /** @var ReviewInterface $review */
                $review = $this->reviewRepository->getById($reviewId);
                foreach ($this->messagesProvider->getWarningMessages($reviewId) as $message) {
                    $this->messageManager->addWarningMessage($message);
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the review.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_AdvancedReviews::reviews')
            ->getConfig()->getTitle()->prepend(
                $reviewId ? __('Edit Review') : __('New Review')
            );
        return $resultPage;
    }
}
