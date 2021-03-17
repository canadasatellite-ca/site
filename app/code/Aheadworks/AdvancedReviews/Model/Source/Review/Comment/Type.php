<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review\Comment;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 * @package Aheadworks\AdvancedReviews\Model\Source\Review\Comment
 */
class Type implements OptionSourceInterface
{
    /**#@+
     * Comment type values
     */
    const VISITOR = 'visitor';
    const ADMIN = 'admin';
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
                'value' => self::VISITOR,
                'label' => __('Visitor')
            ],
            [
                'value' => self::ADMIN,
                'label' => __('Administrator')
            ]
        ];

        return $this->options;
    }
}
