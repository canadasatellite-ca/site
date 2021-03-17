<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Math\Random;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Generator
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode
 */
class Generator
{
    /**
     * Security code length
     */
    const CODE_LENGTH = 32;

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Random
     */
    private $random;

    /**
     * @param QueueRepositoryInterface $queueRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Random $random
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Random $random
    ) {
        $this->queueRepository = $queueRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->random = $random;
    }

    /**
     * Get unsubscription code
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCode()
    {
        do {
            $securityCode = $this->random->getRandomString(self::CODE_LENGTH);

            $this->searchCriteriaBuilder->addFilter(QueueItemInterface::SECURITY_CODE, $securityCode, 'eq');
            /** @var QueueItemSearchResultsInterface $result */
            $result = $this->queueRepository->getList(
                $this->searchCriteriaBuilder->create()
            );
        } while ($result->getTotalCount() > 0);

        return $securityCode;
    }
}
