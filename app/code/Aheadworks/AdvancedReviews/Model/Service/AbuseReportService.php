<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Service;

use Aheadworks\AdvancedReviews\Api\AbuseReportManagementInterface;
use Aheadworks\AdvancedReviews\Api\AbuseReportRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Model\AbuseReport\NewReportPreparer;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Type;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;

/**
 * Class AbuseReportService
 * @package Aheadworks\AdvancedReviews\Model\Service
 */
class AbuseReportService implements AbuseReportManagementInterface
{
    /**
     * @var AbuseReportRepositoryInterface
     */
    private $abuseReportRepository;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var NewReportPreparer
     */
    private $newReportPreparer;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param AbuseReportRepositoryInterface $abuseReportRepository
     * @param CommentRepositoryInterface $commentRepository
     * @param NewReportPreparer $newReportPreparer
     * @param QueueManagementInterface $queueManagement
     * @param Config $config
     */
    public function __construct(
        AbuseReportRepositoryInterface $abuseReportRepository,
        CommentRepositoryInterface $commentRepository,
        NewReportPreparer $newReportPreparer,
        QueueManagementInterface $queueManagement,
        Config $config
    ) {
        $this->abuseReportRepository = $abuseReportRepository;
        $this->commentRepository = $commentRepository;
        $this->newReportPreparer = $newReportPreparer;
        $this->queueManagement = $queueManagement;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function reportReview($reviewId, $storeId)
    {
        $report = $this->newReportPreparer->prepare(Type::REVIEW, $reviewId);
        $this->abuseReportRepository->save($report);
        $this->processAdminNotification($report, NotificationType::ADMIN_REVIEW_ABUSE_REPORT, $storeId);

        return $report;
    }

    /**
     * {@inheritdoc}
     */
    public function reportComment($commentId, $storeId)
    {
        $report = $this->newReportPreparer->prepare(Type::COMMENT, $commentId);
        $this->abuseReportRepository->save($report);
        $this->processAdminNotification($report, NotificationType::ADMIN_COMMENT_ABUSE_REPORT, $storeId);

        return $report;
    }

    /**
     * {@inheritdoc}
     */
    public function ignoreAbuseForReviews($reviewIds, $alsoForComments = false)
    {
        $this->abuseReportRepository->ignoreForEntity(Type::REVIEW, $reviewIds);
        if ($alsoForComments) {
            $commentIds = $this->commentRepository->getIdsByReviewIds($reviewIds);
            $this->ignoreAbuseForComments($commentIds);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ignoreAbuseForComments($commentIds)
    {
        $this->abuseReportRepository->ignoreForEntity(Type::COMMENT, $commentIds);
    }

    /**
     * Process necessary admin notification by review object
     *
     * @param AbuseReportInterface $report
     * @param int $type
     * @param int $storeId
     */
    private function processAdminNotification($report, $type, $storeId)
    {
        if ($this->config->getEmailAddressForAbuseReports($storeId)) {
            $this->queueManagement->add(
                $type,
                $report->getEntityId(),
                $storeId,
                $this->config->getDefaultAdminRecipientName(),
                $this->config->getEmailAddressForAbuseReports()
            );
        }
    }
}
