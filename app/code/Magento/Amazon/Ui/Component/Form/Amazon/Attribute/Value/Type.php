<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Attribute\Value;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type
 */
class Type implements OptionSourceInterface
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
            ['value' => '1', 'label' => __('Text')],
            ['value' => '2', 'label' => __('Select')]
        ];
    }
}
