<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Attribute\Value;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Overwrite
 */
class Overwrite implements OptionSourceInterface
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
            ['value' => '0', 'label' => __('Do Not Overwrite Existing Magento Values')],
            ['value' => '1', 'label' => __('Overwrite Existing Magento Values')]
        ];
    }
}
