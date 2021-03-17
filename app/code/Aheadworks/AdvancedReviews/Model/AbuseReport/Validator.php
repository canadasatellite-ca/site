<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\AbuseReport;

use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Status;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Type;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\AbuseReport;

/**
 * Class Validator
 * @package Aheadworks\AdvancedReviews\Model\AbuseReport
 */
class Validator extends AbstractValidator
{
    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @param ReviewRepositoryInterface $reviewRepository
     * @param CommentRepositoryInterface $commentRepository
     */
    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        CommentRepositoryInterface $commentRepository
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Validate required report data
     *
     * @param AbuseReport $report
     * @return bool
     * @throws \Zend_Validate_Exception
     */
    public function isValid($report)
    {
        $errors = [];
        $statuses = [Status::NEW_REPORT, Status::PROCESSED];
        $entityTypes = [Type::COMMENT, Type::REVIEW];

        if (!\Zend_Validate::is($report->getEntityType(), 'NotEmpty')) {
            $errors[] = __('Entity Type can\'t be empty.');
        }
        if (!\Zend_Validate::is($report->getEntityId(), 'NotEmpty')) {
            $errors[] = __('Entity ID can\'t be empty.');
        }
        if (!\Zend_Validate::is($report->getStatus(), 'NotEmpty')) {
            $errors[] = __('Status can\'t be empty.');
        }
        if (!in_array($report->getStatus(), $statuses)) {
            $errors[] = __('Unknown Status value.');
        }
        if (!in_array($report->getEntityType(), $entityTypes)) {
            $errors[] = __('Unknown Entity Type value.');
        }
        if (!$this->isReviewExist($report)) {
            $errors[] = __('Specified review with does not exist.');
        }
        if (!$this->isCommentExist($report)) {
            $errors[] = __('Specified comment with does not exist.');
        }
        $this->_addMessages($errors);

        return empty($errors);
    }

    /**
     * Check is review exist
     *
     * @param AbuseReport $report
     * @return bool
     */
    private function isReviewExist($report)
    {
        $result = true;

        if ($report->getEntityType() == Type::REVIEW) {
            try {
                $this->reviewRepository->getById($report->getEntityId());
            } catch (NoSuchEntityException $e) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Check is comment exist
     *
     * @param AbuseReport $report
     * @return bool
     */
    private function isCommentExist($report)
    {
        $result = true;

        if ($report->getEntityType() == Type::COMMENT) {
            try {
                $this->commentRepository->getById($report->getEntityId());
            } catch (NoSuchEntityException $e) {
                $result = false;
            }
        }

        return $result;
    }
}
