<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Email;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\AdvancedReviews\Model\Source\Email
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Email type values
     */
    const ADMIN_NEW_REVIEW = 1;
    const SUBSCRIBER_REVIEW_APPROVED = 2;
    const SUBSCRIBER_NEW_COMMENT = 3;
    const SUBSCRIBER_REVIEW_REMINDER = 4;
    const ADMIN_REVIEW_ABUSE_REPORT = 5;
    const ADMIN_COMMENT_ABUSE_REPORT = 6;
    const ADMIN_CRITICAL_REVIEW_ALERT = 7;
    const ADMIN_NEW_COMMENT_FROM_VISITOR = 8;
    /**#@-*/

    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            [
                'value' => self::ADMIN_NEW_REVIEW,
                'label' => __('New Review')
            ],
            [
                'value' => self::SUBSCRIBER_REVIEW_APPROVED,
                'label' => __('Review Approved')
            ],
            [
                'value' => self::SUBSCRIBER_NEW_COMMENT,
                'label' => __('Review Comment')
            ],
            [
                'value' => self::SUBSCRIBER_REVIEW_REMINDER,
                'label' => __('Review Reminder')
            ],
            [
                'value' => self::ADMIN_REVIEW_ABUSE_REPORT,
                'label' => __('Review Abuse Report for Admin')
            ],
            [
                'value' => self::ADMIN_COMMENT_ABUSE_REPORT,
                'label' => __('Comment Abuse Report for Admin')
            ],
            [
                'value' => self::ADMIN_CRITICAL_REVIEW_ALERT,
                'label' => __('Critical Review Alert')
            ],
            [
                'value' => self::ADMIN_NEW_COMMENT_FROM_VISITOR,
                'label' => __('New Review Comment for Admin')
            ],
        ];

        return $this->options;
    }
}
