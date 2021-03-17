<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator;

use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Composite
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator
 */
class Composite extends AbstractValidator
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
     * {@inheritdoc}
     */
    public function isValid($review)
    {
        foreach ($this->validators as $validator) {
            if (!$validator instanceof AbstractValidator) {
                throw new \Exception('Review validator must implement ' . AbstractValidator::class);
            }
            if (!$validator->isValid($review)) {
                $this->_addMessages($validator->getMessages());
            }
        }
        return empty($this->getMessages());
    }
}
