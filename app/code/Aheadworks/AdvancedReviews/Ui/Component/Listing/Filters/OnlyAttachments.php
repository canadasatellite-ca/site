<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters;

use Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters\Type\Checkbox;

/**
 * Class OnlyAttachments
 * @package Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters
 */
class OnlyAttachments extends Checkbox
{
    /**
     * {@inheritdoc}
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if ($this->booleanUtils->toBoolean($value)) {
                $filter = $this->filterBuilder->setConditionType('gt')
                    ->setField($this->getName())
                    ->setValue(0)
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
