<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Cart2Quote
 * Used in creating options for Form element types config value selection
 *
 */
namespace Cart2Quote\Features\Traits\Model\Config\Source\Form;
/**
 * Trait ElementTypes
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Form
 */
trait ElementTypes
{
    /**
     * Options getter
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->_options) {
            $this->_options = [];
            foreach ($this->_standardTypes as $standardType) {
                $this->_options[] = ['value' => $standardType, 'label' => __(ucfirst($standardType))];
            }
        }
        return $this->_options;
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
			return $this->_standardTypes;
		}
	}
}
