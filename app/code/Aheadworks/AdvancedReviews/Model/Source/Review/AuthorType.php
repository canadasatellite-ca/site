<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AuthorType
 * @package Aheadworks\AdvancedReviews\Model\Source\Review
 */
class AuthorType implements OptionSourceInterface
{
    /**#@+
     * Review author type values
     */
    const GUEST = 1;
    const CUSTOMER = 2;
    const ADMIN = 3;
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
                'value' => self::GUEST,
                'label' => __('Guest')
            ],
            [
                'value' => self::CUSTOMER,
                'label' => __('Customer')
            ],
            [
                'value' => self::ADMIN,
                'label' => __('Administrator')
            ]
        ];

        return $this->options;
    }
}
