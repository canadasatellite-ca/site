<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\ResolverInterface;

/**
 * Class Admin
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type
 */
class Admin implements ResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBackendLabel($customerId)
    {
        return __('Administrator');
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
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail($review)
    {
        return null;
    }
}
