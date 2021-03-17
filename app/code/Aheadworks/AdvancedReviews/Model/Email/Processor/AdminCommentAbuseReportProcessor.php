<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AdminCommentAbuseReportProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class AdminCommentAbuseReportProcessor extends AdminReviewAbuseReportProcessor
{
    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param ReviewRepositoryInterface $reviewRepository
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        ReviewRepositoryInterface $reviewRepository,
        CommentRepositoryInterface $commentRepository
    ) {
        parent::__construct($config, $storeManager, $emailMetadataFactory, $urlBuilder, $resolver, $reviewRepository);
        $this->commentRepository = $commentRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateVariables(QueueItemInterface $queueItem)
    {
        $comment = $this->commentRepository->getById($queueItem->getObjectId());
        $review = $this->reviewRepository->getById($comment->getReviewId());

        return [
            EmailVariables::REPORT_TYPE => 'comment',
            EmailVariables::COMMENT => $comment,
            EmailVariables::REVIEW_URL => $this->getReviewUrl($review)
        ];
    }
}
