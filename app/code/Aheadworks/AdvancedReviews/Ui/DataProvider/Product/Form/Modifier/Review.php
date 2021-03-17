<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\UrlInterface;
use Magento\Review\Ui\DataProvider\Product\Form\Modifier\Review as NativeReviewModifier;

/**
 * Class Review
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Product\Form\Modifier
 */
class Review extends AbstractModifier
{
    /**#@+
     * Constants defined for modify js-layout on product page
     */
    const GROUP_ADVANCED_REVIEWS = 'advanced_reviews';
    const GROUP_CONTENT = 'content';
    const SORT_ORDER = 20;
    /**#@-*/

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId()) {
            return $meta;
        }

        $meta = $this->unsetNativeReviewsGrid($meta);
        $meta[static::GROUP_ADVANCED_REVIEWS] = [
            'children' => [
                'aw_advanced_reviews_review_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'aw_advanced_reviews_review_listing',
                                'externalProvider' =>
                                    'aw_advanced_reviews_review_listing.aw_advanced_reviews_review_listing_data_source',
                                'selectionsProvider' =>
                                    'aw_advanced_reviews_review_listing.aw_advanced_reviews_review_columns.ids',
                                'ns' => 'aw_advanced_reviews_review_listing',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'isNeedToIgnoreBookmarks' => true,
                                'productNameColumnVisibility' => false,
                                'productSkuColumnVisibility' => false,
                                'params' => [
                                    'is_need_to_ignore_bookmarks' => '${$.isNeedToIgnoreBookmarks}',
                                ],
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id'
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'isNeedToIgnoreBookmarks' =>
                                        '${ $.externalProvider }:params.is_need_to_ignore_bookmarks',
                                    'productNameColumnVisibility' =>
                                        '${ $.ns }.${ $.ns }.aw_advanced_reviews_review_columns.product_name:visible',
                                    'productSkuColumnVisibility' =>
                                        '${ $.ns }.${ $.ns }.aw_advanced_reviews_review_columns.product_sku:visible'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Reviews'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Fieldset::NAME,
                        'sortOrder' =>
                            $this->getNextGroupSortOrder(
                                $meta,
                                static::GROUP_CONTENT,
                                static::SORT_ORDER
                            ),
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($productId = $this->locator->getProduct()->getId()) {
            $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        }

        return $data;
    }

    /**
     * Unset native reviews grid
     *
     * @param array $meta
     * @return array
     */
    private function unsetNativeReviewsGrid(array $meta)
    {
        if (isset($meta[NativeReviewModifier::GROUP_REVIEW])) {
            unset($meta[NativeReviewModifier::GROUP_REVIEW]);
        }

        return $meta;
    }
}
