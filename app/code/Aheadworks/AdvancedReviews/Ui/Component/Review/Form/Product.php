<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Form;

use Magento\Ui\Component\Form\Field;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class Product
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Form
 */
class Product extends Field
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        if (!empty($dataSource['data'])) {
            $data = $dataSource['data'];
            if (isset($data[ReviewInterface::PRODUCT_ID]) && $data[ReviewInterface::PRODUCT_ID]) {
                $productId = $data[ReviewInterface::PRODUCT_ID];
                $data[$fieldName . '_url'] = $this->context->getUrl(
                    'catalog/product/edit',
                    ['id' => $productId]
                );
            }
            $dataSource['data'] = $data;
        }
        return $dataSource;
    }
}
