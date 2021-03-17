<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Layout;

/**
 * Interface LayoutProcessorInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Layout
 */
interface LayoutProcessorInterface
{
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @param int|null $productId
     * @param int|null $storeId
     * @return array
     */
    public function process($jsLayout, $productId = null, $storeId = null);
}
