<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\AdvancedReviews\Model\Source\Review
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Review status values
     */
    const APPROVED = 1;
    const PENDING = 2;
    const NOT_APPROVED = 3;
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
                'value' => self::APPROVED,
                'label' => __('Approved')
            ],
            [
                'value' => self::PENDING,
                'label' => __('Pending')
            ],
            [
                'value' => self::NOT_APPROVED,
                'label' => __('Not Approved')
            ]
        ];

        return $this->options;
    }

    /**
     * Retrieve default status
     *
     * @return int
     */
    public static function getDefaultStatus()
    {
        return self::PENDING;
    }

    /**
     * Retrieve display statuses
     *
     * @return array
     */
    public static function getDisplayStatuses()
    {
        return [self::APPROVED];
    }
}
