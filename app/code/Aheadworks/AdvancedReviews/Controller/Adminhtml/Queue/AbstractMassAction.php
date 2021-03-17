<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\Collection as QueueCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractMassAction
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue
 */
abstract class AbstractMassAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReviews::mail_log';

    /**
     * @var QueueCollectionFactory
     */
    protected $queueCollectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @param Context $context
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param Filter $filter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QueueRepositoryInterface $queueRepository
     */
    public function __construct(
        Context $context,
        QueueCollectionFactory $queueCollectionFactory,
        Filter $filter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QueueRepositoryInterface $queueRepository
    ) {
        parent::__construct($context);
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->filter = $filter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->queueRepository = $queueRepository;
    }

    /**
     * Execute action
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            /** @var QueueItemInterface[] $queueItemsArray */
            $queueItemsArray = $this->getQueueItemsArray();
            $updatedItemsCount = $this->performAction($queueItemsArray);
            $this->messageManager->addSuccessMessage(
                __('A total of %1 email(s) have been updated', $updatedItemsCount)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->getPreparedRedirect();
    }

    /**
     * Execute action inner logic
     *
     * @param QueueItemInterface[] $queueItemsArray
     * @return int
     * @throws \Exception
     */
    abstract protected function performAction($queueItemsArray);

    /**
     * Retrieve redirect to the tickets grid in the current state
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }

    /**
     * Retrieve queue items array for mass action
     *
     * @return QueueItemInterface[]
     * @throws LocalizedException
     */
    private function getQueueItemsArray()
    {
        /** @var QueueCollection $collection */
        $collection = $this->filter->getCollection($this->queueCollectionFactory->create());
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(QueueItemInterface::ID, $collection->getAllIds(), 'in')
            ->create();

        return $this->queueRepository->getList($searchCriteria)->getItems();
    }
}
