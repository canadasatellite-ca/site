<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value;

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
            \Magento\Amazon\Model\Amazon\Attribute\Value::class,
            \Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value::class
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
        $this->getSelect()->joinInner(
            ['account' => $this->getTable('channel_amazon_account')],
            'main_table.country_code = account.country_code',
            ['merchant_id']
        )->joinInner(
            ['listing' => $this->getTable('channel_amazon_listing')],
            'main_table.asin = listing.asin and account.merchant_id = listing.merchant_id',
            ['id', 'catalog_sku', 'catalog_product_id']
        );

        $this->addFilterToMap('id', 'listing.id');
        $this->addFilterToMap('country_code', 'account.country_code');

        parent::_initSelect();
    }
}
