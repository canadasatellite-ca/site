<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Visibility
 */
class Visibility implements OptionSourceInterface
{
    /**
     * Creates the core attribute ids
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        return [
            ['value' => '1', 'label' => __('Not Visible Individually')],
            ['value' => '2', 'label' => __('Catalog')],
            ['value' => '3', 'label' => __('Search')],
            ['value' => '4', 'label' => __('Catalog / Search')]
        ];
    }
}
