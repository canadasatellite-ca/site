<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Quote;
/**
 * Backend model for products quotable by default setting
 */
trait ProductsQuotable
{
    /**
     * Retrieve all options array ( rewritten from parent )
     *
     * @return array
     */
    private function getAllOptions()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Yes'), 'value' => self::VALUE_YES],
                ['label' => __('No'), 'value' => self::VALUE_NO],
                ['label' => __('Only for selected customer groups'), 'value' => self::VALUE_CUSTOMERGROUP],
            ];
        }
        return $this->_options;
		}
	}
    /**
     * Get a text for index option value ( rewritten from parent )
     *
     * @param  string|int $value
     * @return string|bool
     */
    private function getIndexOptionText($value)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			switch ($value) {
            case self::VALUE_YES:
                return 'Yes';
            case self::VALUE_NO:
                return 'No';
            case self::VALUE_CUSTOMERGROUP:
                return 'Only for selected customer groups';
        }
        return parent::getIndexOptionText($value);
		}
	}
}
