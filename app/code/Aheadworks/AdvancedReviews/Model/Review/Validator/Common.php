<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Validator;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review;
use Magento\Store\Model\Store;

/**
 * Class Common
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Validator
 */
class Common extends AbstractValidator
{
    /**
     * Validate required review data
     *
     * @param Review $review
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($review)
    {
        $errors = [];

        if (!\Zend_Validate::is($review->getCreatedAt(), 'NotEmpty')) {
            $errors[] = __('Created At can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getRating(), 'NotEmpty')) {
            $errors[] = __('Rating can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getNickname(), 'NotEmpty')) {
            $errors[] = __('Nickname can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getContent(), 'NotEmpty')) {
            $errors[] = __('Content can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getStoreId(), 'NotEmpty')) {
            $errors[] = __('Store ID can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getProductId(), 'NotEmpty')) {
            $errors[] = __('Product ID can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getStatus(), 'NotEmpty')) {
            $errors[] = __('Status can\'t be empty.');
        }
        if (!\Zend_Validate::is($review->getAuthorType(), 'NotEmpty')) {
            $errors[] = __('Author Type can\'t be empty.');
        }
        if ($review->getVotesPositive() && !\Zend_Validate::is($review->getVotesPositive(), 'Digits')) {
            $errors[] = __('Votes Positive must contain only digits.');
        }
        if ($review->getVotesNegative() && !\Zend_Validate::is($review->getVotesNegative(), 'Digits')) {
            $errors[] = __('Votes Negative must contain only digits.');
        }
        if (!\Zend_Validate::is($review->getAuthorType(), 'NotEmpty')) {
            $errors[] = __('Author Type can\'t be empty.');
        }
        if ($review->getStoreId() == Store::DEFAULT_STORE_ID
            && !\Zend_Validate::is($review->getSharedStoreIds(), 'NotEmpty')) {
            $errors[] = __('You need to select at least one store view to publish it on frontend.');
        }
        $this->_addMessages($errors);

        return empty($errors);
    }
}
