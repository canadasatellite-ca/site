<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Pool
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements
 */
class Pool
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * Retrieve validator instance by queue item type
     *
     * @param int $authorType
     * @return AbstractValidator|null
     */
    public function getValidatorByAuthorType($authorType)
    {
        if (isset($this->validators[$authorType])
            && ($this->validators[$authorType] instanceof AbstractValidator)
        ) {
            return $this->validators[$authorType];
        }

        return null;
    }
}
