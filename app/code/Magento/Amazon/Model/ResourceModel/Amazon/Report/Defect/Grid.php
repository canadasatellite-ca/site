<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Report\Defect;

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
    protected $_idFieldName = 'id';

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
            \Magento\Amazon\Model\Amazon\Defect::class,
            \Magento\Amazon\Model\ResourceModel\Amazon\Defect::class
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
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['listing' => $this->getTable('channel_amazon_listing')],
            'main_table.seller_sku = listing.seller_sku AND main_table.merchant_id = listing.merchant_id',
            ['listing_id' => 'id', 'catalog_product_id' => 'catalog_product_id']
        );

        $this->getSelect()->joinLeft(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.merchant_id = account.merchant_id',
            ['account_name' => 'account.name']
        );

        $this->addFilterToMap('id', 'main_table.id');
        $this->addFilterToMap('merchant_id', 'main_table.merchant_id');
        $this->addFilterToMap('asin', 'main_table.asin');
        $this->addFilterToMap('seller_sku', 'main_table.seller_sku');
    }
}
