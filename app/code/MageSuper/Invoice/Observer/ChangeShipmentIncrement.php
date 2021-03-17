<?php

namespace MageSuper\Invoice\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection as AppResource;

class ChangeShipmentIncrement implements ObserverInterface
{
    protected $connection;
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $shipment = $observer->getShipment();

        $increment = $shipment->getIncrementId();
        if (!$increment) {
            $increment = $shipment->getOrder()->getIncrementId();
            $this->connection = $shipment->getResource()->getConnection();
            $increment = $this->getIncrementId($increment);
            $shipment->setIncrementId($increment);
        }
    }
    public function getIncrementId($increment){
        $exist = $this->connection->fetchOne("SELECT 1 from sales_shipment where increment_id='{$increment}'");
        if($exist){
            $increment = explode('-',$increment);
            if(!isset($increment[1])){
                $increment[1] = 1;
            } else {
                $increment[1] +=1;
            }
            $increment = implode('-',$increment);
            $increment = $this->getIncrementId($increment);
        }
        return $increment;
    }
}