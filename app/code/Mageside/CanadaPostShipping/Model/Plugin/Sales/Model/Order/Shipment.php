<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Sales\Model\Order;

/**
 * Class Shipment
 */
class Shipment
{
    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    private $_carrierHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * Shipment constructor.
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->_carrierHelper = $carrierHelper;
        $this->_registry = $registry;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $subject
     * @param \Magento\Sales\Model\Order\Shipment $orderShipment
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function afterSave(
        \Magento\Sales\Model\Order\Shipment $subject,
        \Magento\Sales\Model\Order\Shipment $orderShipment
    ) {
        $manifestId = null;

        if ($this->_carrierHelper->isContractShipment()) {
            /** @var \Mageside\CanadaPostShipping\Model\Manifest $manifest */
            if ($manifest = $this->_registry->registry('canadapost_manifest')) {
                $manifest->setStatus('pending');
                if ($manifest->getId()) {
                    $manifest->setUpdatedAt(date('Y-m-d H:i:s'));
                }
                $manifest->save();
                $manifestId = $manifest->getId();
            }
        }

        /** @var \Mageside\CanadaPostShipping\Model\Shipment $cpShipment */
        if ($cpShipment = $this->_registry->registry('canadapost_shipment')) {
            $cpShipment->setStoreId((int)$orderShipment->getStoreId());
            $cpShipment->setSalesOrderId($orderShipment->getOrderId());
            $cpShipment->setSalesShipmentId($orderShipment->getEntityId());
            $cpShipment->setManifestId($manifestId);
            $cpShipment->save();
        }

        return $orderShipment;
    }
}
