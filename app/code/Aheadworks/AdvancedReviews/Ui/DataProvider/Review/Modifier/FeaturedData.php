<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status as ReviewStatusSource;
use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product\Featured\ListingDataProvider
    as FeaturedReviewListingDataProvider;

/**
 * Class FeaturedData
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier
 */
class FeaturedData extends AbstractModifier
{
    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ReviewRepositoryInterface $reviewRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $featuredReviewsCount = 0;
        if (isset($data[ReviewInterface::PRODUCT_ID])) {
            $featuredReviewsCount = $this->getFeaturedReviewsCount($data[ReviewInterface::PRODUCT_ID]);
        }
        $data['featuredReviewsCount'] = $featuredReviewsCount;
        $data['featuredReviewsLimit'] = FeaturedReviewListingDataProvider::REVIEWS_COUNT;
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Retrieve count of featured reviews for specific product
     *
     * @param int $productId
     * @return int
     */
    private function getFeaturedReviewsCount($productId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(ReviewInterface::PRODUCT_ID, $productId)
            ->addFilter(ReviewInterface::IS_FEATURED, true)
            ->addFilter(ReviewInterface::STATUS, ReviewStatusSource::getDisplayStatuses())
        ;
        $searchResults = $this->reviewRepository->getList($this->searchCriteriaBuilder->create());
        return $searchResults->getTotalCount();
    }
}
