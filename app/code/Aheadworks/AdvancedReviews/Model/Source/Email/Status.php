<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Email;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\AdvancedReviews\Model\Source\Email
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Email status values
     */
    const PENDING = 1;
    const SENT = 2;
    const CANCELED = 3;
    const FAILED = 4;
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
                'value' => self::PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => self::SENT,
                'label' => __('Sent')
            ],
            [
                'value' => self::CANCELED,
                'label' => __('Canceled')
            ],
            [
                'value' => self::FAILED,
                'label' => __('Failed')
            ]
        ];

        return $this->options;
    }

    /**
     * Retrieve processed statuses
     *
     * @return array
     */
    public static function getProcessedStatuses()
    {
        return [Status::SENT, Status::CANCELED, self::FAILED];
    }
}
