<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\DateTime;

use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\DateTime
 */
class Resolver
{
    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param DateTimeFormatter $dateTimeFormatter
     * @param Config $config
     */
    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
        Config $config
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->config = $config;
    }

    /**
     * Get scheduled date
     *
     * @param int $emailType
     * @param int $storeId
     * @return string
     */
    public function getScheduledDateTimeInDbFormat($emailType, $storeId)
    {
        $currentDate = $this->getCurrentDate();
        $sendAfterDays = $this->config->getSendReminderAfterDays($storeId);

        if ($emailType == Type::SUBSCRIBER_REVIEW_REMINDER && is_numeric($sendAfterDays)) {
            $dateToFormat = $sendAfterDays . ' days';
            $scheduledDate = $this->dateTimeFormatter->getDateTimeInDbFormat($dateToFormat);
        } else {
            $scheduledDate = $currentDate;
        }

        return $scheduledDate;
    }

    /**
     * Get current date
     *
     * @return string
     */
    public function getCurrentDate()
    {
        return $this->dateTimeFormatter->getDateTimeInDbFormat(null);
    }

    /**
     * Retrieve deadline date for processed emails storage
     *
     * @return string
     */
    public function getDeadlineForProcessedEmails()
    {
        $dateToFormat = '-30 days';
        $deadlineDate = $this->dateTimeFormatter->getDateTimeInDbFormat($dateToFormat);
        return $deadlineDate;
    }
}
