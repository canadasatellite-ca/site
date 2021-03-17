<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AdminReviewAbuseReportProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class AdminReviewAbuseReportProcessor extends AbstractProcessor
{
    /**
     * @var ReviewRepositoryInterface
     */
    protected $reviewRepository;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param ReviewRepositoryInterface $reviewRepository
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        ReviewRepositoryInterface $reviewRepository
    ) {
        parent::__construct($config, $storeManager, $emailMetadataFactory, $urlBuilder, $resolver);
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getEmailTemplateForAbuseReport($storeId);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateVariables(QueueItemInterface $queueItem)
    {
        $review = $this->reviewRepository->getById($queueItem->getObjectId());

        return [
            EmailVariables::REPORT_TYPE => 'review',
            EmailVariables::REVIEW => $review,
            EmailVariables::REVIEW_URL => $this->getReviewUrl($review)
        ];
    }

    /**
     * Retrieve review url
     *
     * @param ReviewInterface $review
     * @return string
     */
    protected function getReviewUrl($review)
    {
        return $this->urlBuilder->getBackendUrl(
            'aw_advanced_reviews/review/edit',
            $review->getStoreId(),
            [ReviewInterface::ID => $review->getId()]
        );
    }
}
