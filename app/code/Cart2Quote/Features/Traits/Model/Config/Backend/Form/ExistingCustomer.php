<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Form;
/**
 * Trait ExistingCustomer
 *
 * @package Cart2Quote\Quotation\Model\Config\Backend\Form
 */
trait ExistingCustomer
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
            ['value' => '0', 'label' => __('Require login to the account first')],
            ['value' => '1', 'label' => __('Request as guest')],
            ['value' => '2', 'label' => __('Request address details')]
        ];
        return $options;
		}
	}
}
