<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Settings\Log;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Options
 */
class Options implements ArrayInterface
{
    /**
     * Return array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Once Daily')],
            ['value' => '7', 'label' => __('Once Weekly')],
            ['value' => '30', 'label' => __('Once Monthly')]
        ];
    }
}
