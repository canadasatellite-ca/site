<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Order\Tracking;

use Magento\Amazon\Model\ResourceModel\AbstractGridCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Psr\Log\LoggerInterface;

/**
 * Class Grid
 */
class Grid extends AbstractGridCollection
{
    /** @var string */
    protected $_idFieldName = 'item_order_id';

    /**
     * Initialize dependencies
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface $mainTable
     * @param AbstractDb $eventPrefix
     * @param $eventObject
     * @param $resourceModel
     * @param string $model
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_init(
            \Magento\Amazon\Model\Amazon\Order\Tracking::class,
            \Magento\Amazon\Model\ResourceModel\Amazon\Order\Tracking::class
        );
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * Add table join to primary grid table
     *
     * @return void
     */
    protected function _initSelect()
    {
        $this->getSelect()->joinLeft(
            ['order' => $this->getTable('channel_amazon_order')],
            'main_table.order_id = order.order_id',
            ['id', 'sales_order_number', 'sales_order_id']
        )->joinLeft(
            ['items' => $this->getTable('channel_amazon_order_item')],
            'main_table.order_item_id = items.order_item_id',
            ['title']
        );

        $this->addFilterToMap('id', 'order.id');
        $this->addFilterToMap('order_item_id', 'main_table.order_item_id');
        $this->addFilterToMap('title', 'items.title');

        parent::_initSelect();
    }
}
