<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\SalesSequence;
/**
 * Trait Sequence
 */
trait Sequence
{
    /**
     * Calculate current value depends on start value
     *
     * @return string
     */
    private function calculateCurrentValue()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return ($this->lastIncrementId - $this->meta->getActiveProfile()->getStartValue())
            * $this->meta->getActiveProfile()->getStep()
            + $this->meta->getActiveProfile()->getStartValue();
		}
	}
    /**
     * Sequence Prefix setter
     *
     * @param string $prefix
     * @return string
     */
    private function setPrefix($prefix)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->meta->getActiveProfile()->setPrefix($prefix);
		}
	}
}
