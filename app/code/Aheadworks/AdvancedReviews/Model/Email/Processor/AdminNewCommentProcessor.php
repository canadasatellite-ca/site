<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Class AdminNewCommentProcessor
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class AdminNewCommentProcessor extends NewReviewProcessor
{
    /**
     * Template
     */
    const TEMPLATE = 'aw_advanced_reviews_email_admin_new_comment_template';

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param ReviewRepositoryInterface $reviewRepository
     * @param ProductResolver $productResolver
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        ReviewRepositoryInterface $reviewRepository,
        ProductResolver $productResolver,
        CommentRepositoryInterface $commentRepository
    ) {
        parent::__construct(
            $config,
            $storeManager,
            $emailMetadataFactory,
            $urlBuilder,
            $resolver,
            $reviewRepository,
            $productResolver
        );
        $this->commentRepository = $commentRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateId($storeId)
    {
        return self::TEMPLATE;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateVariables(QueueItemInterface $queueItem)
    {
        $comment = $this->commentRepository->getById($queueItem->getObjectId());
        $review = $this->reviewRepository->getById($comment->getReviewId());
        $this->prepareReviewRating($review);

        return [
            EmailVariables::PRODUCT_NAME => $this->getProductName($review->getProductId()),
            EmailVariables::REVIEW => $review,
            EmailVariables::REVIEW_URL => $this->getReviewUrl($review),
            EmailVariables::COMMENT => $comment,
        ];
    }
}
