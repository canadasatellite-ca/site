<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Status\History;
/**
 * Trait Validator
 *
 * @package Cart2Quote\Quotation\Model\Quote\Status\History
 */
trait Validator
{
    /**
     * Validate
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History $history
     * @return array
     */
    private function validate(\Cart2Quote\Quotation\Model\Quote\Status\History $history)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$warnings = [];
        foreach ($this->requiredFields as $code => $label) {
            if (!$history->hasData($code)) {
                $warnings[] = sprintf('%s is a required field', $label);
            }
        }
        return $warnings;
		}
	}
}
