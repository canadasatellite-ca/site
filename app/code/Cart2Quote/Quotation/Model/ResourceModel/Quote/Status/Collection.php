<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Status;

/**
 * Flat quotaion quote status history collection
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Collection\AbstractCollection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Status\Collection {
        toOptionArray as private traitToOptionArray;
        toOptionHash as private traitToOptionHash;
        addStateFilter as private traitAddStateFilter;
        joinStates as private traitJoinStates;
        orderByLabel as private traitOrderByLabel;
        _construct as private _traitConstruct;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote
     */
    protected $quotationResourceModel;

    /**
     * Collection constructor.
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $quotationResourceModel
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @throws \Zend_Exception
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $quotationResourceModel,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->quotationResourceModel = $quotationResourceModel;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * Get collection data as options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }

    /**
     * Get collection data as options hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->traitToOptionHash();
    }

    /**
     * Add state code filter to collection
     *
     * @param string $state
     * @return $this
     */
    public function addStateFilter($state)
    {
        return $this->traitAddStateFilter($state);
    }

    /**
     * Join quote states table
     *
     * @return $this
     */
    public function joinStates()
    {
        return $this->traitJoinStates();
    }

    /**
     * Define label order
     *
     * @param string $dir
     * @return $this
     */
    public function orderByLabel($dir = 'ASC')
    {
        return $this->traitOrderByLabel($dir);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_traitConstruct();
    }
}
