<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\ResourceModel;

/**
 * Class Shipment
 */
class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Shipment constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Define main table and id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageside_canadapost_shipment', 'id');
    }

    /**
     * @param $successIds
     */
    public function removeShipments($successIds)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getMainTable(),
            ['id IN (?)' => $successIds]
        );
    }

    /**
     * @param $orderIds
     */
    public function removeTracking($orderIds)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('sales_shipment_track'),
            [
                'carrier_code = ?'  => \Mageside\CanadaPostShipping\Model\Carrier::CODE,
                'order_id IN (?)'   => $orderIds
            ]
        );
    }
}
