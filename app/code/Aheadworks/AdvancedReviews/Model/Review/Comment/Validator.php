<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Comment;

use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Type;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review\Comment;

/**
 * Class Validator
 * @package Aheadworks\AdvancedReviews\Model\Review\Comment
 */
class Validator extends AbstractValidator
{
    /**
     * Validate required comment data
     *
     * @param Comment $comment
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($comment)
    {
        $errors = [];
        
        if (!\Zend_Validate::is($comment->getType(), 'NotEmpty')) {
            $errors[] = __('Type can\'t be empty.');
        }
        if (!\Zend_Validate::is($comment->getReviewId(), 'NotEmpty')) {
            $errors[] = __('Review ID can\'t be empty.');
        }
        if (!\Zend_Validate::is($comment->getStatus(), 'NotEmpty')) {
            $errors[] = __('Status can\'t be empty.');
        }
        if (!\Zend_Validate::is($comment->getNickname(), 'NotEmpty')
            && $comment->getType() != Type::ADMIN) {
            $errors[] = __('Nickname can\'t be empty.');
        }
        if (!\Zend_Validate::is($comment->getContent(), 'NotEmpty')) {
            $errors[] = __('Content can\'t be empty.');
        }
        
        $this->_addMessages($errors);

        return empty($errors);
    }
}
