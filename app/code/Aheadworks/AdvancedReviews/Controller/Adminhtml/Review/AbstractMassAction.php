<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review;

use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\CollectionFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractMassAction
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review
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
     * @var ReviewManagementInterface
     */
    protected $reviewManagement;

    /**
     * @var ReviewRepositoryInterface
     */
    protected $reviewRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param ReviewManagementInterface $reviewManagement
     * @param ReviewRepositoryInterface $reviewRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        ReviewManagementInterface $reviewManagement,
        ReviewRepositoryInterface $reviewRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->reviewManagement = $reviewManagement;
        $this->reviewRepository = $reviewRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $reviewsArray = $this->getReviewsForMassAction();
            $this->massAction($reviewsArray);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }

    /**
     * Retrieve array of reviews for mass action
     *
     * @return ReviewInterface[]
     */
    protected function getReviewsForMassAction()
    {
        $reviewsForMassAction = [];
        try {
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(ReviewInterface::ID, $collection->getAllIds(), 'in')
                ->create();
            $reviewsForMassAction = $this->reviewRepository->getList($searchCriteria)->getItems();
        } catch (LocalizedException $exception) {
        }

        return $reviewsForMassAction;
    }

    /**
     * Perform mass action
     *
     * @param ReviewInterface[] $reviews
     */
    abstract protected function massAction($reviews);
}
