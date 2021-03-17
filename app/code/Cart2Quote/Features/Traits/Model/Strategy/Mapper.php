<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Strategy;
/**
 * Trait Mapper
 *
 * @package Cart2Quote\Quotation\Model\Strategy
 */
trait Mapper
{
    /**
     * Get mapper
     *
     * @return \Cart2Quote\Quotation\Model\Strategy\StrategyInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function getMapping()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$strategy = (string)$this->strategyProvider->getStrategy();
        if (!isset($this->mapping[$strategy])) {
            throw new \Magento\Framework\Exception\NotFoundException(
                __(
                    'Mapping not found for strategy %1',
                    $strategy
                )
            );
        }
        return $this->mapping[$strategy];
		}
	}
}
