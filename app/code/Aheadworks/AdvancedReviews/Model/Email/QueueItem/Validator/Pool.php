<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\ValidatorInterface;

/**
 * Class Pool
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator
 */
class Pool
{
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    /**
     * @param array $validators
     */
    public function __construct(
        $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * Retrieve validator instance by queue item type
     *
     * @param int $type
     * @return ValidatorInterface|null
     */
    public function getValidatorByType($type)
    {
        if (isset($this->validators[$type])
            && ($this->validators[$type] instanceof ValidatorInterface)
        ) {
            return $this->validators[$type];
        }

        return null;
    }
}
