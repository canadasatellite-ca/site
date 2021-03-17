<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\SalesSequence;
/**
 * Trait Config
 * - configuration container for sequence
 *
 * @package Cart2Quote\Quotation\Model\SalesSequence
 */
trait Config
{
    /**
     * Default toOptionArray function
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return [];
		}
	}
}
