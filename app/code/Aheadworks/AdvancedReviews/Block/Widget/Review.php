<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Widget;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Widget\Block\BlockInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as RatingResolver;
use Magento\Catalog\Block\Product\Image as ProductImage;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Class Review
 * @package Aheadworks\AdvancedReviews\Block\Widget
 * @method null|int getReviewId()
 */
class Review extends Template implements BlockInterface, IdentityInterface
{
    /**
     * Default review content height, px
     */
    const DEFAULT_CONTENT_HEIGHT = 120;

    /**
     * Component name base code
     */
    const COMPONENT_NAME_BASE = 'aw-ar-widget-rating';

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RatingResolver
     */
    private $ratingResolver;

    /**
     * @var ImageBuilder
     */
    private $imageBuilder;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @param Context $context
     * @param ReviewRepositoryInterface $reviewRepository
     * @param ProductRepositoryInterface $productRepository
     * @param RatingResolver $ratingResolver
     * @param ImageBuilder $imageBuilder
     * @param ProductResolver $productResolver
     * @param array $data
     */
    public function __construct(
        Context $context,
        ReviewRepositoryInterface $reviewRepository,
        ProductRepositoryInterface $productRepository,
        RatingResolver $ratingResolver,
        ImageBuilder $imageBuilder,
        ProductResolver $productResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->reviewRepository = $reviewRepository;
        $this->productRepository = $productRepository;
        $this->ratingResolver = $ratingResolver;
        $this->imageBuilder = $imageBuilder;
        $this->productResolver = $productResolver;
    }

    /**
     * Retrieve content height
     *
     * @return int
     */
    public function getContentHeight()
    {
        $height = $this->getData('content_height');

        return $height ? $height : self::DEFAULT_CONTENT_HEIGHT;
    }

    /**
     * Retrieve review instance
     *
     * @return ReviewInterface|null
     */
    public function getReview()
    {
        try {
            $review = $this->reviewRepository->getById($this->getReviewId());
        } catch (NoSuchEntityException $e) {
            $review = null;
        }

        return $review;
    }

    /**
     * Retrieve product
     *
     * @param int $productId
     * @return ProductInterface|null
     */
    public function getProduct($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        return $product;
    }

    /**
     * Retrieve product image
     *
     * @param ProductInterface $product
     * @param string $imageId
     * @param array $attributes
     * @return ProductImage
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $review = $this->getReview();
        $identities = [];

        if ($review) {
            $identities = [
                ReviewInterface::CACHE_TAG . '_' . $review->getId(),
                ReviewInterface::CACHE_PRODUCT_TAG . '_' . $review->getProductId(),
            ];
        }

        return $identities;
    }

    /**
     * Retrieve component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return self::COMPONENT_NAME_BASE . rand();
    }
    
    /**
     * Retrieve component config data
     *
     * @param int $rating
     * @return string
     */
    public function getComponentConfigData($rating)
    {
        $config = [
            'component' => 'uiComponent',
            'template' => 'Aheadworks_AdvancedReviews/review/rating/view',
            'value' => $rating,
            'title' => $this->ratingResolver->getRatingTitle($rating)
        ];
        return json_encode($config);
    }

    /**
     * Retrieve product review url by product object
     *
     * @param Product $product
     * @return string
     */
    public function getProductReviewUrlByObject($product)
    {
        $productReviewUrl=$this->productResolver->getProductReviewUrlByObject(
            $product
        );
        return $productReviewUrl;
    }

    /**
     * Retrieve short text
     *
     * @param string $txt
     * @param int $count
     * @return string
     */
    public function getCutString($txt, $count)
    {
        if ($count < mb_strlen($txt)) {
            return mb_substr($txt, 0, $count, 'UTF-8') . '...';
        } else {
            return $txt;
        }
    }
}
