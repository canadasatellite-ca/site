<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\SalesSequence;

/**
 * Factory class for @see \Cart2Quote\Quotation\Model\SalesSequence\Sequence
 */
class SequenceFactory extends \Magento\SalesSequence\Model\SequenceFactory
{
    use \Cart2Quote\Features\Traits\Model\SalesSequence\SequenceFactory {
    }

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Cart2Quote\Quotation\Model\SalesSequence\Sequence::class
    ) {
        parent::__construct(
            $objectManager,
            $instanceName
        );
    }
}
