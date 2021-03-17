<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;

/**
 * Class MassSend
 *
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue
 */
class MassSend extends AbstractMassAction
{
    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param Filter $filter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QueueRepositoryInterface $queueRepository
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        QueueCollectionFactory $queueCollectionFactory,
        Filter $filter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QueueRepositoryInterface $queueRepository,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct(
            $context,
            $queueCollectionFactory,
            $filter,
            $searchCriteriaBuilder,
            $queueRepository
        );
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     */
    protected function performAction($queueItemsArray)
    {
        $updatedItemsCount = 0;
        /** @var QueueItemInterface $item */
        foreach ($queueItemsArray as $item) {
            $this->queueManagement->send($item);
            $updatedItemsCount++;
        }
        return ($updatedItemsCount);
    }
}
