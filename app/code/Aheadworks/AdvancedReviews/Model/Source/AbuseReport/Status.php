<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\AbuseReport;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\AdvancedReviews\Model\Source\AbuseReport
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Abuse report status values
     */
    const NEW_REPORT = 'new';
    const PROCESSED = 'processed';
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
                'value' => self::NEW_REPORT,
                'label' => __('New')
            ],
            [
                'value' => self::PROCESSED,
                'label' => __('Processed')
            ]
        ];

        return $this->options;
    }

    /**
     * Retrieve default status
     *
     * @return string
     */
    public static function getDefaultStatus()
    {
        return self::NEW_REPORT;
    }
}
