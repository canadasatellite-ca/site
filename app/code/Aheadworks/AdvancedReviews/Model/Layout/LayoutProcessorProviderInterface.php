<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Layout;

/**
 * Interface LayoutProcessorProviderInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Layout
 */
interface LayoutProcessorProviderInterface
{
    /**
     * Retrieves array of layout processors
     *
     * @return LayoutProcessorInterface[]
     */
    public function getLayoutProcessors();
}
