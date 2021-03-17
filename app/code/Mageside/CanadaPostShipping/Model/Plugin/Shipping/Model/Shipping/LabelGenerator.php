<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Plugin\Shipping\Model\Shipping;

/**
 * Class LabelGenerator
 */
class LabelGenerator
{
    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    private $_carrierHelper;

    /**
     * @var \Mageside\CanadaPostShipping\Model\ShipmentFactory
     */
    private $_shipmentFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Model\ManifestFactory
     */
    private $_manifestFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * Shipment constructor.
     *
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param \Mageside\CanadaPostShipping\Model\ShipmentFactory $shipmentFactory
     * @param \Mageside\CanadaPostShipping\Model\ManifestFactory $manifestFactory
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        \Mageside\CanadaPostShipping\Model\ShipmentFactory $shipmentFactory,
        \Mageside\CanadaPostShipping\Model\ManifestFactory $manifestFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_carrierHelper = $carrierHelper;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_manifestFactory = $manifestFactory;
        $this->_registry = $registry;
    }

    /**
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $subject
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function beforeCreate(
        \Magento\Shipping\Model\Shipping\LabelGenerator $subject,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $cpShipment = $this->_shipmentFactory->create();
        if ($shipmentId = $shipment->getEntityId()) {
            $cpShipment->load($shipmentId, 'sales_shipment_id');
        }
        $this->_registry->register('canadapost_shipment', $cpShipment);

        if ($this->_carrierHelper->isContractShipment()) {
            $manifestId = null;

            $shipmentData = $request->getParam('shipment');
            if (isset($shipmentData['canadapost_manifest_id'])) {
                $manifestId = $shipmentData['canadapost_manifest_id'];
            }

            /** @var \Mageside\CanadaPostShipping\Model\Manifest $manifest */
            $manifest = $this->_manifestFactory->create();

            if ((int)$manifestId) {
                $manifest->load($manifestId);
            } elseif ($manifestId == 'new' || !$manifestId) {
                $manifest->setGroupId($manifest->createManifestGroupId());
            }

            $this->_registry->register('canadapost_manifest', $manifest);
        }
    }
}
