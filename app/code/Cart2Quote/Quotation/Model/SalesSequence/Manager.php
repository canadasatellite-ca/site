<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\SalesSequence;

/**
 * Class Manager
 */
class Manager extends \Magento\SalesSequence\Model\Manager
{
    use \Cart2Quote\Features\Traits\Model\SalesSequence\Manager {
        getSequence as private traitGetSequence;
    }

    protected $scopeConfig;
    /**
     * @var \Magento\SalesSequence\Model\ResourceModel\Meta
     */
    protected $resourceSequenceMeta;

    /**
     * @var \Magento\SalesSequence\Model\SequenceFactory
     */
    protected $sequenceFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $entityConfig;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helperData;

    /**
     * Manager constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\SalesSequence\Model\ResourceModel\Meta $resourceSequenceMeta
     * @param \Magento\SalesSequence\Model\SequenceFactory $sequenceFactory
     * @param \Magento\Eav\Model\Entity\Type $entityConfig
     * @param \Cart2Quote\Quotation\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\SalesSequence\Model\ResourceModel\Meta $resourceSequenceMeta,
        \Magento\SalesSequence\Model\SequenceFactory $sequenceFactory,
        \Magento\Eav\Model\Entity\Type $entityConfig,
        \Cart2Quote\Quotation\Helper\Data $helperData
    ) {
        parent::__construct(
            $resourceSequenceMeta,
            $sequenceFactory
        );
        $this->scopeConfig = $scopeConfig;
        $this->resourceSequenceMeta = $resourceSequenceMeta;
        $this->sequenceFactory = $sequenceFactory;
        $this->entityConfig = $entityConfig;
        $this->helperData = $helperData;
    }

    /**
     * Returns sequence for given entityType and store
     *
     * @param string $entityType
     * @param int $storeId
     * @return \Magento\Framework\DB\Sequence\SequenceInterface|\Magento\SalesSequence\Model\Sequence
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSequence($entityType, $storeId)
    {
        return $this->traitGetSequence($entityType, $storeId);
    }
}
