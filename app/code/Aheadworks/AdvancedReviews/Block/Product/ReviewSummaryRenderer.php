<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Product;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\AdvancedReviews\Api\StatisticsRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as RatingResolver;
use Magento\Framework\Math\Random;
use Magento\Framework\DataObject;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Class ReviewSummaryRenderer
 * @package Aheadworks\AdvancedReviews\Block\Product
 */
class ReviewSummaryRenderer extends Template implements ReviewRendererInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'product/review_summary.phtml';

    /**
     * @var array
     */
    private $componentConfigData = [];

    /**
     * @var array
     */
    private $snippetSchemaData = [];

    /**
     * @var StatisticsRepositoryInterface
     */
    private $statisticsRepository;

    /**
     * @var RatingResolver
     */
    private $ratingResolver;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductResolver
     */
    protected $productResolver;

    /**
     * @param Context $context
     * @param StatisticsRepositoryInterface $statisticsRepository
     * @param RatingResolver $ratingResolver
     * @param Registry $registry
     * @param ProductResolver $productResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        StatisticsRepositoryInterface $statisticsRepository,
        RatingResolver $ratingResolver,
        Registry $registry,
        ProductResolver $productResolver,
        array $data = []
    ) {
        $this->statisticsRepository = $statisticsRepository;
        $this->ratingResolver = $ratingResolver;
        $this->registry = $registry;
        $this->productResolver = $productResolver;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewsSummaryHtml(
        Product $product,
        $templateType = self::DEFAULT_VIEW,
        $displayIfNoReviews = false
    ) {
        if ($product) {
            $this->generateSnippetSchemaData($product, $templateType);
            $this->generateComponentConfigData($product, $templateType, $displayIfNoReviews);
        }

        return $this->toHtml();
    }

    /**
     * Retrieve component config data
     *
     * @return string
     */
    public function getComponentConfigData()
    {
        return json_encode($this->componentConfigData);
    }

    /**
     * Retrieve snippet schema data
     *
     * @return DataObject
     */
    public function getSnippetSchema()
    {
        return new DataObject($this->snippetSchemaData);
    }

    /**
     * Get component name
     *
     * @return string
     * @throws LocalizedException
     */
    public function getComponentName()
    {
        return 'awArReviewSummary' . Random::getRandomNumber();
    }

    /**
     * Generate component config data
     *
     * @param Product $product
     * @param string|bool $templateType
     * @param bool $displayIfNoReviews
     */
    private function generateComponentConfigData(
        $product,
        $templateType,
        $displayIfNoReviews
    ) {
        $this->componentConfigData = [
            'productData' => $this->getProductData($product),
            'statisticsData' => $this->getStatisticsDataForProduct($product->getId()),
            'displayIfNoReviews' => $displayIfNoReviews,
            'summaryType' => $templateType,
            'isProductPage' => $this->registry->registry('current_product') ? true : false
        ];
    }

    /**
     * Generate snippet schema data
     *
     * @param Product $product
     * @param string $templateType
     */
    private function generateSnippetSchemaData($product, $templateType)
    {
        $statisticsData = $this->getStatisticsDataForProduct($product->getId());
        $isNeedToRender = $templateType != self::SHORT_VIEW && $statisticsData['reviewsCount'] > 0;

        $this->snippetSchemaData = [
            'is_need_to_render' => $isNeedToRender,
            'product_name' => $this->productResolver->getPreparedProductNameByObject(
                $product
            ),
            'rating_value' => $this->ratingResolver->getRatingAbsoluteValue($statisticsData['ratingValue']),
            'rating_maximum_value' => $this->ratingResolver->getRatingMaximumAbsoluteValue(),
            'rating_minimum_value' => $this->ratingResolver->getRatingMinimumAbsoluteValue(),
            'reviews_count' => $statisticsData['reviewsCount']
        ];
    }

    /**
     * Get product data
     *
     * @param Product $product
     * @return array
     */
    private function getProductData($product)
    {
        return [
            'name' => $this->productResolver->getPreparedProductNameByObject(
                $product
            ),
            'url' => $product->getUrlModel()->getUrl($product, ['_ignore_category' => true])
        ];
    }

    /**
     * Retrieve statistics data for product
     *
     * @param int $productId
     * @return array
     */
    private function getStatisticsDataForProduct($productId)
    {
        try {
            $storeId = $this->_storeManager->getStore()->getId();
            $statisticForProduct = $this->statisticsRepository->getByProductId($productId, $storeId);
            $statisticsData = [
                'ratingValue' => $statisticForProduct->getAggregatedRating(),
                'reviewsCount' => $statisticForProduct->getReviewsCount(),
                'ratingTitle' => $this->ratingResolver->getRatingTitle($statisticForProduct->getAggregatedRating())
            ];
        } catch (NoSuchEntityException $e) {
            $statisticsData = [];
        }

        return $statisticsData;
    }
}
