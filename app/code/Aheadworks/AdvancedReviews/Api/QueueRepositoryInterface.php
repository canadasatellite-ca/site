<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface QueueRepositoryInterface
 * @package Aheadworks\AdvancedReviews\Api
 */
interface QueueRepositoryInterface
{
    /**
     * Save queue item
     *
     * @param QueueItemInterface $queueItem
     * @return QueueItemInterface
     * @throws CouldNotSaveException
     */
    public function save(QueueItemInterface $queueItem);

    /**
     * Delete queue item
     *
     * @param QueueItemInterface $queueItem
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(QueueItemInterface $queueItem);

    /**
     * Delete queue item by id
     *
     * @param int $queueItemId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($queueItemId);

    /**
     * Retrieve queue item
     *
     * @param int $queueItemId
     * @return QueueItemInterface
     * @throws NoSuchEntityException
     */
    public function getById($queueItemId);

    /**
     * Retrieve queue items matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueueItemSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
