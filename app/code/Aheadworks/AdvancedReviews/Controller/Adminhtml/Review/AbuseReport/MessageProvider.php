<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Adminhtml\Review\AbuseReport;

use Aheadworks\AdvancedReviews\Api\AbuseReportRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Status;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Type;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MessageProvider
 * @package Aheadworks\AdvancedReviews\Controller\Adminhtml\Review\AbuseReport
 */
class MessageProvider
{
    /**
     * @var AbuseReportRepositoryInterface
     */
    private $abuseReportRepository;

    /**
     * @var ReviewRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param AbuseReportRepositoryInterface $abuseReportRepository
     * @param CommentRepositoryInterface $commentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        AbuseReportRepositoryInterface $abuseReportRepository,
        CommentRepositoryInterface $commentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->abuseReportRepository = $abuseReportRepository;
        $this->commentRepository = $commentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve warning messages
     *
     * @param int $reviewId
     * @return array
     */
    public function getWarningMessages($reviewId)
    {
        $messages = [];
        $commentIdsForReview = $this->commentRepository->getIdsByReviewIds([$reviewId]);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(AbuseReportInterface::ENTITY_TYPE, Type::COMMENT)
            ->addFilter(AbuseReportInterface::STATUS, Status::getDefaultStatus())
            ->addFilter(AbuseReportInterface::ENTITY_ID, $commentIdsForReview, 'in')
            ->create();

        try {
            $reviewAbuse = $this->abuseReportRepository->getByEntity(Type::REVIEW, $reviewId);
            $isReviewReported = $reviewAbuse->getStatus() == Status::getDefaultStatus();
            $isCommentsReported = !empty($this->abuseReportRepository->getList($searchCriteria)->getItems());

            if ($isReviewReported) {
                $messages[] = __('This review was reported as abusive. You can edit the text of review or disable it '
                . 'completely and then click "Save" button. After saving the review will be considered as moderated and'
                . ' this message will not appear unless someone will report it again.');
            }
            if ($isCommentsReported) {
                $messages[] = __('One or more comments in this review was reported as abusive. '
                . 'Sort the comments grid by "Abuse Reported" column to find them. Then, you can disapprove it or '
                . 'disable completely using actions listed in the action column. If you choose "Ignore" action, the '
                . 'comment will be considered as moderated and this message will not appear unless someone will report '
                . 'it again.');
            }
        } catch (LocalizedException $e) {
        }

        return $messages;
    }
}
