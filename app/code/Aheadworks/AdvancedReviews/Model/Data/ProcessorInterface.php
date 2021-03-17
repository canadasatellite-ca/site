<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Data
 */
interface ProcessorInterface
{
    /**
     * Process data
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($data);
}
