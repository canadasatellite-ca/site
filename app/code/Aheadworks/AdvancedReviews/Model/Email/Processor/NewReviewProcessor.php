<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Class NewReviewProcessor
 * @package Aheadworks\AdvancedReviews\Model\Email\Processor
 */
class NewReviewProcessor extends AbstractProcessor
{
    /**
     * @var ReviewRepositoryInterface
     */
    protected $reviewRepository;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param EmailMetadataInterfaceFactory $emailMetadataFactory
     * @param UrlBuilder $urlBuilder
     * @param Resolver $resolver
     * @param ReviewRepositoryInterface $reviewRepository
     * @param ProductResolver $productResolver
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        EmailMetadataInterfaceFactory $emailMetadataFactory,
        UrlBuilder $urlBuilder,
        Resolver $resolver,
        ReviewRepositoryInterface $reviewRepository,
        ProductResolver $productResolver
    ) {
        parent::__construct($config, $storeManager, $emailMetadataFactory, $urlBuilder, $resolver);
        $this->reviewRepository = $reviewRepository;
        $this->productResolver = $productResolver;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateId($storeId)
    {
        return $this->config->getAdminNotificationTemplate($storeId);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplateVariables(QueueItemInterface $queueItem)
    {
        $review = $this->reviewRepository->getById($queueItem->getObjectId());
        $this->prepareReviewRating($review);

        return [
            EmailVariables::PRODUCT_NAME => $this->getProductName($review->getProductId()),
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

    /**
     * Retrieve product name
     *
     * @param int $productId
     * @return string
     */
    protected function getProductName($productId)
    {
        $preparedProductName=$this->productResolver->getPreparedProductName(
            $productId
        );
        return $preparedProductName;
    }
}
