<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\SalesSequence;

/**
 * Class Sequence represents sequence in logic
 */
class Sequence extends \Magento\SalesSequence\Model\Sequence
{
    use \Cart2Quote\Features\Traits\Model\SalesSequence\Sequence {
        calculateCurrentValue as private traitCalculateCurrentValue;
        setPrefix as private traitSetPrefix;
    }

    /**
     * @var string
     */
    private $lastIncrementId;

    /**
     * @var \Magento\SalesSequence\Model\Meta
     */
    private $meta;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    private $entityType;

    /**
     * Sequence constructor.
     *
     * @param \Magento\SalesSequence\Model\Meta $meta
     * @param \Magento\Eav\Model\Entity\Type $entityType
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param string $pattern
     */
    public function __construct(
        \Magento\SalesSequence\Model\Meta $meta,
        \Magento\Eav\Model\Entity\Type $entityType,
        \Magento\Framework\App\ResourceConnection $resource,
        $pattern = parent::DEFAULT_PATTERN
    ) {
        $pattern = str_replace("9", $entityType->getIncrementPadLength(), $pattern);
        parent::__construct($meta, $resource, $pattern);
        $this->meta = $meta;
        $this->connection = $resource->getConnection('quotation');
        $this->pattern = $pattern;
        $this->entityType = $entityType;
    }

    /**
     * Calculate current value depends on start value
     *
     * @return string
     */
    private function calculateCurrentValue()
    {
        return $this->traitCalculateCurrentValue();
    }

    /**
     * Sequence Prefix setter
     *
     * @param string $prefix
     * @return string
     */
    public function setPrefix($prefix)
    {
        return $this->traitSetPrefix($prefix);
    }
}
