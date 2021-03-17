<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

use Magento\Amazon\GraphQl\FilterToCollectionFilterConverter;

class CollectionFilterApplier
{
    /**
     * @var FilterToCollectionFilterConverter
     */
    private $filterConverter;

    public function __construct(FilterToCollectionFilterConverter $filterConverter)
    {
        $this->filterConverter = $filterConverter;
    }

    public function apply(\Magento\Amazon\Model\ResourceModel\Amazon\Order\Collection $collection, array $filters, array $fieldsMap): void
    {
        if (!empty($filters)) {
            $filters = $this->filterConverter->convert($filters, $fieldsMap);
            foreach ($filters as $field => $conditions) {
                if (is_int($field)) {
                    foreach ($conditions as $field => $condition) {
                        $collection->addFieldToFilter($field, $condition);
                    }
                } else {
                    $collection->addFieldToFilter($field, $conditions);
                }
            }
        }
    }
}
