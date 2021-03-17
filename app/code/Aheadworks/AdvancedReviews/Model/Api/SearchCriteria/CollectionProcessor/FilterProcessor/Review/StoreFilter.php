<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Review;

use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class StoreFilter
 * @package Aheadworks\AdvancedReviews\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Review
 */
class StoreFilter implements CustomFilterInterface
{
    /**
     * Apply store filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        if ($this->canApplyFilter($filter)) {
            /** @var Collection $collection */
            $collection->addStoreFilter($filter->getValue());
            return true;
        }
        return false;
    }

    /**
     * Check can apply filter
     *
     * @param Filter $filter
     * @return bool
     */
    private function canApplyFilter(Filter $filter)
    {
        return is_numeric($filter->getValue()) && $filter->getConditionType() == 'eq';
    }
}
