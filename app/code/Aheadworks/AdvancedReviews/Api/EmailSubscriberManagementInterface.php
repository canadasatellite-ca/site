<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface EmailSubscriberManagementInterface
 *
 * @package Aheadworks\AdvancedReviews\Api
 */
interface EmailSubscriberManagementInterface
{
    /**
     * Create new subscriber
     *
     * @param string $email
     * @param int $websiteId
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createSubscriber($email, $websiteId);

    /**
     * Retrieve subscriber
     *
     * @param string $email
     * @param int $websiteId
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubscriber($email, $websiteId);

    /**
     * Retrieve subscriber by related customer id
     *
     * @param int $customerId
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubscriberByCustomerId($customerId);

    /**
     * Update existing subscriber
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateSubscriber(\Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber);

    /**
     * Delete existing subscriber
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteSubscriber(\Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface $subscriber);

    /**
     * Delete existing subscriber by id
     *
     * @param int $subscriberId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteSubscriberById($subscriberId);
}
