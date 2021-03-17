<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\CollectionFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\Collection;
use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractMassAction
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Comment
 */
abstract class AbstractMassAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::reviews';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CommentManagementInterface
     */
    protected $commentManagement;

    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param CommentManagementInterface $commentManagement
     * @param CommentRepositoryInterface $commentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        CommentManagementInterface $commentManagement,
        CommentRepositoryInterface $commentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->commentManagement = $commentManagement;
        $this->commentRepository = $commentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $commentsArray = $this->getCommentsForMassAction();
            $this->massAction($commentsArray);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }

    /**
     * Retrieve array of comments for mass action
     *
     * @return CommentInterface[]
     */
    protected function getCommentsForMassAction()
    {
        $commentsForMassAction = [];
        try {
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(CommentInterface::ID, $collection->getAllIds(), 'in')
                ->create();
            $commentsForMassAction = $this->commentRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $exception) {
        }

        return $commentsForMassAction;
    }

    /**
     * Perform mass action
     *
     * @param CommentInterface[] $comments
     * @throws LocalizedException
     */
    abstract protected function massAction($comments);
}
