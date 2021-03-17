<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Customer\Grid;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;

/**
 * Class Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Customer\Grid
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid\Collection
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Customer\Grid\Collection {
        _initSelect as private _traitInitSelect;
        addCustomerIdFilter as private traitAddCustomerIdFilter;
    }

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable = 'quotation_quote',
        $resourceModel = \Cart2Quote\Quotation\Model\ResourceModel\Quote::class
    ) {
        $this->registry = $registry;
        parent::__construct($coreResourceHelper, $entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }


    /**
     * Initialize db select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return $this->_traitInitSelect();
    }

    /**
     * Add filtration by customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerIdFilter($customerId)
    {
        return $this->traitAddCustomerIdFilter($customerId);
    }
}
