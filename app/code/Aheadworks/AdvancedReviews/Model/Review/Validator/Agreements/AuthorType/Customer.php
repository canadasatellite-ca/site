<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review;
use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;

/**
 * Class Customer
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType
 */
class Customer extends AbstractValidator
{
    /**
     * @var AgreementsChecker
     */
    private $agreementsChecker;

    /**
     * @param AgreementsChecker $agreementsChecker
     */
    public function __construct(
        AgreementsChecker $agreementsChecker
    ) {
        $this->agreementsChecker = $agreementsChecker;
    }

    /**
     * Validate review agreements data for customer review author
     *
     * @param Review $review
     * @return bool
     */
    public function isValid($review)
    {
        if ($this->agreementsChecker->areAgreementsEnabled($review->getStoreId()) &&
            $this->agreementsChecker->isNeedToShowForCustomers($review->getStoreId())
        ) {
            if (empty($review->getAreAgreementsConfirmed())) {
                $this->_addMessages([__('Please accept Terms and Conditions.')]);
            }
        }

        return empty($this->getMessages());
    }
}
