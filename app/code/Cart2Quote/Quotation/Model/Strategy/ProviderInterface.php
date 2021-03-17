<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Strategy;

/**
 * Interface ProviderInterface
 *
 * @package Cart2Quote\Quotation\Model\Strategy
 */
interface ProviderInterface
{
    /**
     * Get quote request strategy
     *
     * @return \Cart2Quote\Quotation\Model\Strategy\StrategyInterface
     */
    public function getStrategy();
}
