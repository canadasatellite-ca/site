<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Api\EmailSubscriberRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class Finder
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber
 */
class Finder
{
    /**
     * @var EmailSubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param EmailSubscriberRepositoryInterface $subscriberRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EmailSubscriberRepositoryInterface $subscriberRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Find subscriber by email for specific website
     *
     * @param string $email
     * @param int $websiteId
     * @return SubscriberInterface|null
     */
    public function find($email, $websiteId)
    {
        $result = null;

        $this->searchCriteriaBuilder
            ->addFilter(SubscriberInterface::EMAIL, $email)
            ->addFilter(SubscriberInterface::WEBSITE_ID, $websiteId);

        /** @var SubscriberInterface[] $subscribers */
        $subscribers = $this->subscriberRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        /** @var SubscriberInterface $result */
        if (count($subscribers) > 0) {
            $result = reset($subscribers);
        }

        return $result;
    }
}
