<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review;
use Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\Pool as AgreementsValidatorPool;

/**
 * Class Agreements
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator
 */
class Agreements extends AbstractValidator
{
    /**
     * @var AgreementsValidatorPool
     */
    private $agreementsValidatorPool;

    /**
     * @param AgreementsValidatorPool $agreementsValidatorPool
     */
    public function __construct(
        AgreementsValidatorPool $agreementsValidatorPool
    ) {
        $this->agreementsValidatorPool = $agreementsValidatorPool;
    }

    /**
     * Validate review agreements data
     *
     * @param Review $review
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($review)
    {
        $validator = $this->agreementsValidatorPool->getValidatorByAuthorType($review->getAuthorType());
        if ($validator) {
            if (!$validator->isValid($review)) {
                $this->_addMessages($validator->getMessages());
            }
        }

        return empty($this->getMessages());
    }
}
