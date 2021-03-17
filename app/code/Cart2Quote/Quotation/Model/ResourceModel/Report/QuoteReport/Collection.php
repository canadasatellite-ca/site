<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport;

/**
 * Class Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Report\QuoteReport\Collection {
        getQuotedField as private traitGetQuotedField;
        _getSelectedColumns as private _traitGetSelectedColumns;
        getSelectCountSql as private traitGetSelectCountSql;
        _beforeLoad as private _traitBeforeLoad;
        _applyOrderStatusFilter as private _traitApplyOrderStatusFilter;
    }

    /**
     * Period format
     *
     * @var string
     */
    protected $periodFormat;

    /**
     * Selected columns
     *
     * @var array
     */
    protected $selectedColumns = [];

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init(\Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport::QUOTATION_AGGREGATION);
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    /**
     * Return ordered filed
     *
     * @return string
     */
    protected function getQuotedField()
    {
        return $this->traitGetQuotedField();
    }

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        return $this->_traitGetSelectedColumns();
    }

    /**
     * Get SQL for get record count
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        return $this->traitGetSelectCountSql();
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeLoad()
    {
        return $this->_traitBeforeLoad();
    }

    /**
     * Apply quote status filter
     *
     * @return $this
     */
    protected function _applyOrderStatusFilter()
    {
        return $this->_traitApplyOrderStatusFilter();
    }
}