<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Attribute\Value;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class InNavigation
 */
class InNavigation implements OptionSourceInterface
{
    /**
     * Creates the attribute type options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        return [
            ['value' => '0', 'label' => __('No')],
            ['value' => '1', 'label' => __('Filterable (with results)')],
            ['value' => '2', 'label' => __('Filterable (no results)')]
        ];
    }
}
