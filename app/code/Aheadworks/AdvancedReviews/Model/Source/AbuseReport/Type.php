<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\AbuseReport;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\AdvancedReviews\Model\Source\AbuseReport
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Abuse report type values
     */
    const REVIEW = 'review';
    const COMMENT = 'comment';
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
                'value' => self::REVIEW,
                'label' => __('Review')
            ],
            [
                'value' => self::COMMENT,
                'label' => __('Comment')
            ]
        ];

        return $this->options;
    }
}
