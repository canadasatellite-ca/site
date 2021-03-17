<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\DateTime;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Class Formatter
 *
 * @package Aheadworks\AdvancedReviews\Model\DateTime
 */
class Formatter
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatter;

    /**
     * @param DateTime $dateTime
     * @param TimezoneInterface $localeDate
     * @param DateTimeFormatterInterface $dateTimeFormatter
     */
    public function __construct(
        DateTime $dateTime,
        TimezoneInterface $localeDate,
        DateTimeFormatterInterface $dateTimeFormatter
    ) {
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Retrieve formatted date, localized according to the specific store
     *
     * @param string|null $date
     * @param int|null $storeId
     * @param int $dateFormat
     * @return string
     */
    public function getLocalizedDate($date = null, $storeId = null, $dateFormat = \IntlDateFormatter::MEDIUM)
    {
        $timeFormat = \IntlDateFormatter::NONE;
        return $this->getLocalizedDateTime($date, $storeId, [$dateFormat, $timeFormat]);
    }

    /**
     * Retrieve formatted date and time, localized according to the specific store
     *
     * @param string|null $date
     * @param int|null $storeId
     * @param string|int|array|null $format
     * @return string
     */
    public function getLocalizedDateTime($date = null, $storeId = null, $format = null)
    {
        $scopeDate = $this->localeDate->scopeDate($storeId, $date, true);
        return $this->dateTimeFormatter->formatObject($scopeDate, $format);
    }

    /**
     * Get date and time in db format
     *
     * @param string|null $date
     * @return string
     */
    public function getDateTimeInDbFormat($date = null)
    {
        if (empty($date)) {
            $formattedDate = $this->dateTime->gmtDate(
                StdlibDateTime::DATETIME_PHP_FORMAT
            );
        } else {
            $createdAtTimestamp = strtotime($date);
            $formattedDate = $this->dateTime->gmtDate(
                StdlibDateTime::DATETIME_PHP_FORMAT,
                $createdAtTimestamp
            );
        }

        return $formattedDate;
    }
}
