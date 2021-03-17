<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber
 */
class Resolver
{
    /**
     * @var EmailSubscriberManagementInterface
     */
    private $emailSubscriberManagement;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param EmailSubscriberManagementInterface $emailSubscriberManagement
     * @param StoreResolver $storeResolver
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EmailSubscriberManagementInterface $emailSubscriberManagement,
        StoreResolver $storeResolver,
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->emailSubscriberManagement = $emailSubscriberManagement;
        $this->storeResolver = $storeResolver;
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve email subscriber by email security code
     *
     * @param string $securityCode
     * @return SubscriberInterface|null
     */
    public function getBySecurityCode($securityCode)
    {
        $subscriber = null;
        if (!empty($securityCode)) {
            $this->searchCriteriaBuilder->addFilter(QueueItemInterface::SECURITY_CODE, $securityCode, 'eq');
            /** @var QueueItemInterface[] $queueItems */
            $queueItems = $this->queueRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            /** @var QueueItemInterface $queueItem */
            if (count($queueItems) > 0) {
                $queueItem = reset($queueItems);
                $subscriber = $this->getByEmailQueueItem($queueItem);
            }
        }

        return $subscriber;
    }

    /**
     * Retrieve email subscriber by email queue item
     *
     * @param QueueItemInterface $queueItem
     * @return SubscriberInterface|null
     */
    public function getByEmailQueueItem($queueItem)
    {
        $storeId = $queueItem->getStoreId();
        $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);
        try {
            $subscriber = $this->emailSubscriberManagement->getSubscriber(
                $queueItem->getRecipientEmail(),
                $websiteId
            );
        } catch (LocalizedException $exception) {
            $subscriber = null;
        }
        return $subscriber;
    }
}
