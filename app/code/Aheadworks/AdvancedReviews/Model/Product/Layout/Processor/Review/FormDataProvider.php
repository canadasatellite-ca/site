<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review
 */
class FormDataProvider implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var ProductResolver
     */
    private $productResolver;

    /**
     * @param ArrayManager $arrayManager
     * @param ProductResolver $productResolver
     */
    public function __construct(
        ArrayManager $arrayManager,
        ProductResolver $productResolver
    ) {
        $this->arrayManager = $arrayManager;
        $this->productResolver = $productResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        $reviewFormProviderPath = 'components/awArReviewFormProvider';
        $jsLayout = $this->arrayManager->merge(
            $reviewFormProviderPath,
            $jsLayout,
            [
                'data' => $this->getCurrentProductData($productId)
            ]
        );

        return $jsLayout;
    }

    /**
     * Retrieve current product data for review form
     *
     * @param int|null $productId
     * @return array
     */
    private function getCurrentProductData($productId = null)
    {
        if (isset($productId)) {
            $currentProductData = [
                'product_name' => $this->productResolver->getPreparedProductName(
                    $productId
                ),
                'product_id' => $productId,
            ];
        } else {
            $currentProductData = [];
        }
        return $currentProductData;
    }
}
