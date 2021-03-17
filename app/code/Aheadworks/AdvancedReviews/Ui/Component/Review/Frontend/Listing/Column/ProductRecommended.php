<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Frontend\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;

/**
 * Class ProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Frontend\Listing\Column
 */
class ProductRecommended extends Column
{
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['view_config'] = $this->getProductRecommendedViewConfig();
        $this->setData('config', (array)$config);
        parent::prepare();
    }

    /**
     * Retrieve config for column rendering
     *
     * @return array
     */
    private function getProductRecommendedViewConfig()
    {
        return [
            ProductRecommendedSource::NOT_SPECIFIED => [
                'additionalClasses'     => []
            ],
            ProductRecommendedSource::NO => [
                'additionalClasses'     => [
                    'recommend' => true,
                    'dont' => true
                ]
            ],
            ProductRecommendedSource::YES => [
                'additionalClasses'     => [
                    'recommend' => true,
                    'dont' => false
                ]
            ],
        ];
    }
}
