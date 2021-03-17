<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review\Agreements;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class DisplayMode
 *
 * @package Aheadworks\AdvancedReviews\Model\Source\Review\Agreements
 */
class DisplayMode implements OptionSourceInterface
{
    /**#@+
     * Agreements display mode values
     */
    const GUESTS_ONLY = 1;
    const EVERYONE = 2;
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
                'value' => self::GUESTS_ONLY,
                'label' => __('Guests only')
            ],
            [
                'value' => self::EVERYONE,
                'label' => __('Everyone')
            ],
        ];

        return $this->options;
    }
}
