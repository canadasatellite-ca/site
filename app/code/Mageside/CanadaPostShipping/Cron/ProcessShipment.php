<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Cron;

/**
 * Class ProcessShipment
 * @package Mageside\CanadaPostShipping\Cron
 */
class ProcessShipment
{
    /**
     * @var \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment\CollectionFactory
     */
    private $shipmentCollection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\Shipment
     */
    private $shipmentService;

    /**
     * ProcessShipment constructor.
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment\CollectionFactory $shipmentCollectionFactory
     * @param \Mageside\CanadaPostShipping\Model\Service\Shipment $shipmentService
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment\CollectionFactory $shipmentCollectionFactory,
        \Mageside\CanadaPostShipping\Model\Service\Shipment $shipmentService,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->shipmentCollection = $shipmentCollectionFactory;
        $this->shipmentService = $shipmentService;
        $this->logger = $logger;
    }

    /**
     * Get shipment cost from CP API
     */
    public function getCost()
    {
        try {
            $date = date('Y-m-d', strtotime("-7 days"));
            $shipments = $this->shipmentCollection->create()
                ->addFieldToFilter('cost', [['eq' => 0], ['null' => true]])
                ->addFieldToFilter('created_at', ['gt' => $date]);

            foreach ($shipments->getItems() as $shipment) {
                $cost = $this->shipmentService->getShipmentPrice($shipment->getShipmentId());
                if ($cost > 0) {
                    $shipment->setCost($cost);
                    $shipment->save();
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
