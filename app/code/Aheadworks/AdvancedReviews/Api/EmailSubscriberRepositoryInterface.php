<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface EmailSubscriberRepositoryInterface
 *
 * @package Aheadworks\AdvancedReviews\Api
 */
interface EmailSubscriberRepositoryInterface
{
    /**
     * Save subscriber
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber);

    /**
     * Delete subscriber
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber);

    /**
     * Retrieve subscriber by id
     *
     * @param int $subscriberId
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($subscriberId);

    /**
     * Retrieve subscribers matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
