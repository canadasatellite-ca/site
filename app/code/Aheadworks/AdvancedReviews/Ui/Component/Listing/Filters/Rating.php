<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters;

use Aheadworks\AdvancedReviews\Model\Source\Review\AdvancedRatingValue;
use Magento\Ui\Component\Filters\Type\Select;

/**
 * Class Rating
 * @package Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters
 */
class Rating extends Select
{
    /**
     * {@inheritdoc}
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if (!empty($value) || is_numeric($value)) {
                $multipleFilter = [AdvancedRatingValue::POSITIVE_REVIEWS, AdvancedRatingValue::CRITICAL_REVIEWS];
                if (is_array($value) || in_array($value, $multipleFilter)) {
                    $conditionType = 'in';
                    if ($value == AdvancedRatingValue::POSITIVE_REVIEWS) {
                        $value = AdvancedRatingValue::getPositiveReviews();
                    } elseif ($value == AdvancedRatingValue::CRITICAL_REVIEWS) {
                        $value = AdvancedRatingValue::getCriticalReviews();
                    }
                } else {
                    $dataType = $this->getData('config/dataType');
                    $conditionType = $dataType == 'multiselect' ? 'finset' : 'eq';
                }
                $filter = $this->filterBuilder->setConditionType($conditionType)
                    ->setField($this->getName())
                    ->setValue($value)
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
