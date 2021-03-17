<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Form;
/**
 * Trait Shipping
 *
 * @package Cart2Quote\Quotation\Model\Config\Backend\Form
 */
trait Shipping
{
    /**
     * Get all options
     *
     * @return array
     */
    private function getAllOptions()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$options = [
            ['value' => '1', 'label' => __('Yes')],
        ];
        if ($this->shipping->isActive()) {
            $options = [
                ['value' => '1', 'label' => __('Yes')],
                ['value' => '0', 'label' => __('No')],
            ];
        }
        return $options;
		}
	}
}
