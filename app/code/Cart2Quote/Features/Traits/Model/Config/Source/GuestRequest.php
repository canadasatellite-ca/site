<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Source;
/**
 * Trait GuestRequest
 * @package Cart2Quote\Quotation\Model\Config\Source
 */
trait GuestRequest
{
    /**
     * Options getter
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return [
            ['value' => 1, 'label' => __('Yes')],
            ['value' => 2, 'label' => __('Yes (request with addresses)')],
            ['value' => 0, 'label' => __('No')]
        ];
		}
	}
    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    private function toArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return [0 => __('No'), 1 => __('Yes'), 2 => __('Yes (request with addresses)')];
		}
	}
}