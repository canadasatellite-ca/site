<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Source;
/**
 * Trait Options
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Quote
 */
trait Options
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$optionArray = [];
        foreach ($this->options as $key => $option) {
            $optionArray[$key] = $option->getLabel();
        }
        return $optionArray;
		}
	}
    /**
     * Get options
     *
     * @return \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface[]
     */
    private function getOptions()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->options;
		}
	}
}
