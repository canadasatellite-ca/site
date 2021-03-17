<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Email\Review;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended;
use Magento\Framework\Url;
use Aheadworks\AdvancedReviews\Model\Config;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Class RequestForm
 * @package Aheadworks\AdvancedReviews\Block\Email\Review
 */
class RequestForm extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'email/review/request_form.phtml';

    /**
     * @var RatingValue
     */
    private $ratingValue;

    /**
     * @var ProductRecommended
     */
    private $productRecommended;

    /**
     * @var Url
     */
    private $frontendUrlBuilder;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param RatingValue $ratingValue
     * @param ProductRecommended $productRecommended
     * @param Url $frontendUrlBuilder
     * @param ProductResolver $productResolver
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        RatingValue $ratingValue,
        ProductRecommended $productRecommended,
        Url $frontendUrlBuilder,
        ProductResolver $productResolver,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ratingValue = $ratingValue;
        $this->productRecommended = $productRecommended;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
        $this->productResolver = $productResolver;
        $this->config = $config;
    }

    /**
     * Retrieve rating options
     *
     * @return array
     */
    public function getRatingValues()
    {
        return array_reverse($this->ratingValue->toOptionArray());
    }

    /**
     * Retrieve recommend values
     *
     * @return array
     */
    public function getProductRecommendValues()
    {
        return $this->productRecommended->toOptionArray();
    }

    /**
     * Retrieve product name by ID
     *
     * @param $productId
     * @return null|string
     */
    public function getProductName($productId)
    {
        $preparedProductName=$this->productResolver->getPreparedProductName(
            $productId
        );
        return $preparedProductName;
    }

    /**
     * Retrieve review post frontendUrlBuilder
     *
     * @return string
     */
    public function getReviewPostUrl()
    {
        return $this->frontendUrlBuilder->getUrl(
            'aw_advanced_reviews/review/emailpost',
            [
                '_secure' => true,
                '_nosid' => true
            ]
        );
    }

    /**
     * Check if need to add pros and cons fields to the review submit form
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isNeedToAddProsAndConsFields($websiteId)
    {
        return $this->config->areProsAndConsEnabled($websiteId);
    }

    /**
     * Retrieve current website id
     *
     * @return int|null
     */
    public function getCurrentWebsiteId()
    {
        $currentWebsiteId = null;
        $store = $this->getData('store');
        if ($store instanceof StoreInterface) {
            $currentWebsiteId = $store->getWebsiteId();
        }
        return $currentWebsiteId;
    }

    /**
     * Retrieve product review url
     *
     * @param $productId
     * @return string
     */
    public function getProductReviewUrl($productId)
    {
        $productReviewUrl=$this->productResolver->getProductReviewUrl(
            $productId
        );
        return $productReviewUrl;
    }
}
