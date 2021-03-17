<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\ResolverInterface;

/**
 * Class Guest
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type
 */
class Guest implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBackendLabel($customerId)
    {
        return __('Guest');
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendUrl($customerId)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName($review)
    {
        return $review->getNickname();
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail($review)
    {
        return $review->getEmail();
    }
}
