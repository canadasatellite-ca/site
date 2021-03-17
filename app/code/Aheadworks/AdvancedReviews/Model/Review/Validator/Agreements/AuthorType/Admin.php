<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review;

/**
 * Class Admin
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType
 */
class Admin extends AbstractValidator
{
    /**
     * Validate review agreements data for admin review author
     *
     * @param Review $review
     * @return bool
     */
    public function isValid($review)
    {
        return true;
    }
}
