<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Strategy;

/**
 * Class Mapper
 *
 * @package Cart2Quote\Quotation\Model\Strategy
 */
class Mapper
{
    use \Cart2Quote\Features\Traits\Model\Strategy\Mapper {
        getMapping as private traitGetMapping;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Strategy\ProviderInterface
     */
    private $strategyProvider;

    /**
     * @var array
     */
    private $mapping;

    /**
     * Mapper constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Strategy\ProviderInterface $strategyProvider
     * @param array $mapping
     */
    public function __construct(ProviderInterface $strategyProvider, $mapping = [])
    {
        $this->strategyProvider = $strategyProvider;
        $this->mapping = $mapping;
    }

    /**
     * Get mapper
     *
     * @return \Cart2Quote\Quotation\Model\Strategy\StrategyInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getMapping()
    {
        return $this->traitGetMapping();
    }
}
