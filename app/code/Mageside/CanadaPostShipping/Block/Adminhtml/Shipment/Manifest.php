<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block\Adminhtml\Shipment;

class Manifest extends \Magento\Backend\Block\Template
{
    /**
     * @var \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory
     */
    private $_manifestCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    private $_carrierHelper;

    /**
     * Manifest constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory $manifestCollection
     * @param \Magento\Framework\Registry $registry
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory $manifestCollection,
        \Magento\Framework\Registry $registry,
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        array $data = []
    ) {
        $this->_manifestCollection = $manifestCollection;
        $this->_registry = $registry;
        $this->_carrierHelper = $carrierHelper;

        parent::__construct($context, $data);
    }

    /**
     * Get shipment object
     *
     * @return null|object
     */
    private function getShipment()
    {
        return $this->_registry->registry('current_shipment');
    }

    /**
     * Check is can process manifests for Canada Post shipment
     *
     * @return bool
     */
    public function canChooseManifest()
    {
        if (!$this->_carrierHelper->isContractShipment()) {
            return false;
        }

        $shipment = $this->getShipment();
        if ($shipment) {
            $shippingMethod = explode('_', $shipment->getOrder()->getShippingMethod());
            if (isset($shippingMethod[0]) && $shippingMethod[0] == \Mageside\CanadaPostShipping\Model\Carrier::CODE) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Get available manifests ids (with status 'pending')
     *
     * @return array
     */
    public function getAvailableManifests()
    {
        $collection = $this->_manifestCollection->create();
        $collection->addFieldToFilter('status', ['eq' => 'pending']);

        return $collection->getAllIds();
    }
}
