<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email;

use Aheadworks\AdvancedReviews\Model\Email\Processor\AbstractProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\AdminCommentAbuseReportProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\AdminCriticalReviewAlertProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\AdminReviewAbuseReportProcessor;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;
use Magento\Framework\ObjectManagerInterface;
use Aheadworks\AdvancedReviews\Model\Email\Processor\MetadataProcessorInterface;
use Aheadworks\AdvancedReviews\Model\Email\Processor\NewReviewProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\ReviewApprovedProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\ReviewCommentProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\ReviewReminderProcessor;
use Aheadworks\AdvancedReviews\Model\Email\Processor\AdminNewCommentProcessor;

/**
 * Class ProcessorFactory
 * @package Aheadworks\AdvancedReviews\Model\Email
 */
class ProcessorFactory
{
    /**
     * @var MetadataProcessorInterface[]
     */
    private $processors = [
        NotificationType::ADMIN_NEW_REVIEW => NewReviewProcessor::class,
        NotificationType::SUBSCRIBER_REVIEW_APPROVED => ReviewApprovedProcessor::class,
        NotificationType::SUBSCRIBER_NEW_COMMENT => ReviewCommentProcessor::class,
        NotificationType::SUBSCRIBER_REVIEW_REMINDER => ReviewReminderProcessor::class,
        NotificationType::ADMIN_REVIEW_ABUSE_REPORT => AdminReviewAbuseReportProcessor::class,
        NotificationType::ADMIN_COMMENT_ABUSE_REPORT => AdminCommentAbuseReportProcessor::class,
        NotificationType::ADMIN_CRITICAL_REVIEW_ALERT => AdminCriticalReviewAlertProcessor::class,
        NotificationType::ADMIN_NEW_COMMENT_FROM_VISITOR => AdminNewCommentProcessor::class,
    ];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Get email processor by queue item type
     *
     * @param string $type
     * @return AbstractProcessor
     * @throws \Exception
     */
    public function create($type)
    {
        if (!array_key_exists($type, $this->processors)) {
            throw new \Exception('Notification type not supported!');
        }

        return $this->objectManager->create($this->processors[$type]);
    }
}
