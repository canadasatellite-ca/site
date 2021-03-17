<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class SharedStoreIds
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor\Review
 */
class SharedStoreIds implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (empty($data[ReviewInterface::SHARED_STORE_IDS])) {
            $data[ReviewInterface::SHARED_STORE_IDS] = [];
        }
        return $data;
    }
}
