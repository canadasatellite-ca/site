<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Email;
/**
 * Backend model for showing quote adjustment setting
 * 'sales_email/general/async_sending'.
 */
trait ShowAdjustment
{
    /**
     * To option array
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$options = [
            ['value' => '0', 'label' => __('No')],
            ['value' => '1', 'label' => __('Yes')],
            ['value' => '2', 'label' => __('Dynamic')],
        ];
        return $options;
		}
	}
}
