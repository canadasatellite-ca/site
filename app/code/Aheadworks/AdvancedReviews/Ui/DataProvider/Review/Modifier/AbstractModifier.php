<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class AbstractModifier
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier
 */
abstract class AbstractModifier implements ModifierInterface
{
    /**
     * Check if is set id
     *
     * @param array $data
     * @return bool
     */
    protected function isSetId($data)
    {
        return isset($data[ReviewInterface::ID]) && !empty($data[ReviewInterface::ID]);
    }
}
