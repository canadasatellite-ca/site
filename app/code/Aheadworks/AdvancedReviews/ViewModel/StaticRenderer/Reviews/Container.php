<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\AdvancedReviews\ViewModel\IdentityInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class Container
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews
 */
class Container implements ArgumentInterface, IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBlockIdentities()
    {
        $blockIdentities = [
            ReviewInterface::CACHE_ALL_REVIEWS_PAGE_TAG
        ];
        return $blockIdentities;
    }
}
