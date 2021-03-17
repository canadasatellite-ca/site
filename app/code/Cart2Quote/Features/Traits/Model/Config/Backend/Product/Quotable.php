<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Product;
/**
 * Backend model for products quotable field
 */
trait Quotable
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            ['value' => '2', 'label' => __('Use default')],
            ['value' => '1', 'label' => __('Yes')],
            ['value' => '0', 'label' => __('No')],
        ];
        return $options;
    }
}
