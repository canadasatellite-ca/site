<?php

namespace MageSuper\Casat\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class ChangeOrderIncrement implements ObserverInterface
{
    private $connection;
    private $meta;

    function __construct(
        \Magento\SalesSequence\Model\Meta $meta,
        AppResource $resource

    )
    {
        $this->meta = $meta;
        $this->connection = $resource->getConnection('sales');
        //	$this->pricehlper = $pricehlper;


    }

    function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getOrder();

        $increment = $order->getIncrementId();
        //if (strlen($increment) >= 9) {
            //$newIncrement = substr($increment, -8);
            $orderstoreid = '0';$order->getStoreId();
            $sequence_order_ = $this->connection->getTableName('sequence_order_' . $orderstoreid); //gives table name with prefix
            $querysequence_order_ = 'SELECT MAX(sequence_value) FROM ' . $sequence_order_ ;
            $lastIncrementId = (int)$this->connection->fetchOne($querysequence_order_);
            $lastIncrementId +=1;

            $newIncrement = $lastIncrementId;
            $order->setIncrementId($newIncrement);
        //}
    }
}
