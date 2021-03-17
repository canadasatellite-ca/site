<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Ui\DataProvider\Listing\Collection;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Shipment
 */
class Shipment extends SearchResult
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * Shipment constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        $mainTable = 'mageside_canadapost_shipment',
        $resourceModel = 'Mageside\CanadaPostShipping\Model\ResourceModel\Shipment'
    ) {
        $this->_request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        if ($id = $this->_request->getParam('id')) {
            $this->getSelect()->where('main_table.id = ?', $id);
        }
        $this->getSelect()
            ->joinLeft(
                ['order' => $this->getResource()->getTable('sales_order')],
                'main_table.sales_order_id = order.entity_id',
                [
                    'order_increment_id'    => 'order.increment_id',
                    'ordered_at'            => 'order.created_at',
                    'paid'                  => 'order.base_shipping_amount',
                    'customer_name'         => new \Zend_Db_Expr(
                        'concat(order.customer_firstname, \' \', order.customer_lastname)'
                    ),
                ]
            )
            ->joinLeft(
                ['shipment' => $this->getResource()->getTable('sales_shipment')],
                'main_table.sales_shipment_id = shipment.entity_id',
                ['shipment_increment_id' => 'shipment.increment_id']
            )
            ->joinLeft(
                ['store' => $this->getResource()->getTable('store')],
                'main_table.store_id = store.store_id',
                ['store_name' => 'store.name']
            );

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch ($field) {
            case 'order_increment_id':
                $field = 'order.increment_id';
                break;
            case 'ordered_at':
                $field = 'order.created_at';
                break;
            case 'customer_name':
                $field = 'order.customer_lastname';
                break;
            case 'paid':
                $field = 'order.base_shipping_amount';
                break;
            case 'shipment_increment_id':
                $field = 'shipment.increment_id';
                break;
            case 'store_name':
                $field = 'store.name';
                break;
            default:
                $field = 'main_table.' . $field;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
