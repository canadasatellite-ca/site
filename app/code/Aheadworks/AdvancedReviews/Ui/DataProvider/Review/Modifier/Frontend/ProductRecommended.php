<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;

/**
 * Class ProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class ProductRecommended extends AbstractModifier
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareProductRecommendedField($data);
        }
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
     * Prepare product recommended field
     *
     * @param array $data
     * @return array
     */
    protected function prepareProductRecommendedField(&$data)
    {
        if (isset($data[ReviewInterface::PRODUCT_RECOMMENDED])) {
            $productRecommendedLabel = $this->getProductRecommendedLabel($data[ReviewInterface::PRODUCT_RECOMMENDED]);
            $data[ReviewInterface::PRODUCT_RECOMMENDED . '_label'] = $productRecommendedLabel;
        }
        return $data;
    }

    /**
     * Retrieve label for product recommended column
     *
     * @param int $productRecommendedValue
     * @return \Magento\Framework\Phrase|string
     */
    protected function getProductRecommendedLabel($productRecommendedValue)
    {
        $label = "";
        $labelsConfig = $this->getLabelsConfig();
        if (isset($labelsConfig[$productRecommendedValue])) {
            $label = $labelsConfig[$productRecommendedValue];
        }
        return $label;
    }

    /**
     * Retrieve translated labels config
     *
     * @return array
     */
    protected function getLabelsConfig()
    {
        return [
            ProductRecommendedSource::NO => __("I don't recommend<br /> this product"),
            ProductRecommendedSource::YES => __("I recommend<br /> this product"),
        ];
    }
}
