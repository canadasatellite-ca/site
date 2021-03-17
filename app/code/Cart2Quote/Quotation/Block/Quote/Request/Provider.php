<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request;

/**
 * Class Provider
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request
 */
class Provider
{
    /**
     * @var \Cart2Quote\Quotation\Model\Strategy\Mapper
     */
    private $mapper;

    /**
     * Provider constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Strategy\Mapper $mapper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Strategy\Mapper $mapper
    ) {
        $this->mapper = $mapper;
    }

    /**
     * Get mapping
     *
     * @return \Cart2Quote\Quotation\Model\Strategy\StrategyInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getBlockClass()
    {
        return $this->mapper->getMapping();
    }
}
