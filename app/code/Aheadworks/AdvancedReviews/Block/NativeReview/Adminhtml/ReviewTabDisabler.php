<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\NativeReview\Adminhtml;

use Magento\Ui\Component\Layout\Tabs\TabWrapper;

/**
 * Class ReviewTabDisabler
 * @package Aheadworks\AdvancedReviews\Block\NativeReview\Adminhtml
 */
class ReviewTabDisabler extends TabWrapper
{
    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return false;
    }
}
