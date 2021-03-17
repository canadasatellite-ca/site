<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Service\Review;

use Aheadworks\AdvancedReviews\Api\HelpfulnessManagementInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\VoteResultInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\VoteResultInterface;
use Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\ProcessorPool;

/**
 * Class HelpfulnessService
 * @package Aheadworks\AdvancedReviews\Model\Service\Review
 */
class HelpfulnessService implements HelpfulnessManagementInterface
{
    /**
     * @var VoteResultInterfaceFactory
     */
    private $voteResultFactory;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var ProcessorPool
     */
    private $processorPool;

    /**
     * @param VoteResultInterfaceFactory $voteResultFactory
     * @param ReviewRepositoryInterface $reviewRepository
     * @param ProcessorPool $processorPool
     */
    public function __construct(
        VoteResultInterfaceFactory $voteResultFactory,
        ReviewRepositoryInterface $reviewRepository,
        ProcessorPool $processorPool
    ) {
        $this->voteResultFactory = $voteResultFactory;
        $this->reviewRepository = $reviewRepository;
        $this->processorPool = $processorPool;
    }

    /**
     * {@inheritdoc}
     */
    public function vote($reviewId, $action = '')
    {
        $review = $this->reviewRepository->getById($reviewId);
        $processor = $this->processorPool->getByAction($action);
        $review = $processor->process($review);
        $savedReview = $this->reviewRepository->save($review);

        return $this->getVoteResult($savedReview);
    }

    /**
     * Get vote result
     *
     * @param ReviewInterface $review
     * @return VoteResultInterface
     */
    private function getVoteResult($review)
    {
        /** @var VoteResultInterface $voteResult */
        $voteResult = $this->voteResultFactory->create();

        $voteResult
            ->setLikeCount($review->getVotesPositive())
            ->setDislikeCount($review->getVotesNegative());

        return $voteResult;
    }
}
