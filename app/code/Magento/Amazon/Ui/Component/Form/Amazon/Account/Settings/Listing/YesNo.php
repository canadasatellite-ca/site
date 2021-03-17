<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Settings\Listing;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class YesNo
 */
class YesNo implements OptionSourceInterface
{
    /**
     * Returns option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data[] = ['value' => 0, 'label' => __('Disabled')];
        $data[] = ['value' => 1, 'label' => __('Enabled')];

        return $data;
    }
}
