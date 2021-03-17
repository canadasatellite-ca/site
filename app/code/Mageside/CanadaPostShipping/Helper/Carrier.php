<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order\Shipment;

class Carrier extends \Magento\Shipping\Helper\Carrier
{
    /**
     * Code of the carrier
     *
     * @var string
     */
    private $_code = \Mageside\CanadaPostShipping\Model\Carrier::CODE;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    private $_configReader;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $_regionFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory
     */
    private $_manifestCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * @var \Mageside\CanadaPostShipping\Model\RequestLogFactory
     */
    private $_requestLogModelFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLogFactory
     */
    private $_requestLogResourceFactory;

    /**
     * Carrier constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\Dir\Reader $configReader
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory $manifestCollection
     * @param \Mageside\CanadaPostShipping\Model\RequestLogFactory $requestLogModelFactory
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLogFactory $requestLogResourceFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Dir\Reader $configReader,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory $manifestCollection,
        \Mageside\CanadaPostShipping\Model\RequestLogFactory $requestLogModelFactory,
        \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLogFactory $requestLogResourceFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_configReader = $configReader;
        $this->_regionFactory = $regionFactory;
        $this->_manifestCollection = $manifestCollection;
        $this->_registry = $registry;
        $this->_requestLogModelFactory = $requestLogModelFactory;
        $this->_requestLogResourceFactory = $requestLogResourceFactory;
        return parent::__construct($context, $localeResolver);
    }

    /**
     * Get module settings
     *
     * @param $key
     * @return mixed
     */
    public function getConfigModule($key)
    {
        return $this->scopeConfig->getValue(
            'mageside_canada_post_shipping/general/' . $key,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        if ($this->getConfigCarrier('active')
            && $this->isModuleOutputEnabled("Mageside_CanadaPostShipping")
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isContractShipment()
    {
        $contractId = $this->getConfigCarrier('contract_id');
        if (!empty($contractId)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve information from carrier configuration
     *
     * @param $field
     * @return bool|mixed
     */
    public function getConfigCarrier($field)
    {
        return $this->scopeConfig->getValue(
            'carriers/' . $this->_code . '/' . $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool|mixed
     */
    public function getMailedBy()
    {
        return $this->getConfigCarrier('customer_number');
    }

    /**
     * @param $notify
     * @return bool
     */
    public function getNotificationConfig($notify)
    {
        if ($this->getConfigCarrier('enable_d2po')) {
            return true;
        }

        if (strpos($this->getConfigCarrier('notification'), $notify) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Get path to module etc directory
     *
     * @param $directory
     * @return string
     */
    public function getModuleDir($directory)
    {
        return $this->_configReader->getModuleDir($directory, 'Mageside_CanadaPostShipping');
    }

    /**
     * @param null $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getStoreConfig($storeId = null)
    {
        $storeConfig = new \Magento\Framework\DataObject();

        $store = new \Magento\Framework\DataObject(
            (array)$this->scopeConfig->getValue(
                'general/store_information',
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );

        $shipperRegionCode = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_REGION_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->_regionFactory->create()->load($shipperRegionCode)->getCode();
        }
        $originStreet1 = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS1,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $originStreet2 = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS2,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $storeConfig->setShipperContactCompanyName($store->getName());
        $storeConfig->setShipperContactPhoneNumber($store->getPhone());
        $storeConfig->setShipperAddressStreet(trim($originStreet1 . ' ' . $originStreet2));
        $storeConfig->setShipperAddressStreet1($originStreet1);
        $storeConfig->setShipperAddressCity(
            $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_CITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
        $storeConfig->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $storeConfig->setShipperAddressPostalCode(
            $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ZIP,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );

        return $storeConfig;
    }

    /**
     * Get store weight unit setting
     *
     * @param $storeId
     * @return string
     */
    public function getStoreWeightUnit($storeId)
    {
        return $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) == 'lbs' ? 'LBS' : 'KILOGRAM';
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isMeasuresMetric($storeId)
    {
        return $this->getStoreWeightUnit($storeId) == 'KILOGRAM';
    }

    /**
     * @inheritdoc
     */
    public function convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure)
    {
        if ($value) {
            $unitWeight = new \Zend_Measure_Weight($value, $sourceWeightMeasure, 'en_US');
            $unitWeight->setType($toWeightMeasure);
            return $unitWeight->getValue();
        }
        return null;
    }

    /**
     * @param $method
     * @return bool|mixed
     */
    public function getMethodNonDeliveryOption($method)
    {
        $labels = $this->getConfigCarrier('shipping_methods_labels');
        if ($labels) {
            $labels = unserialize($labels);
        }

        $key = strtolower(str_replace('.', '_', $method));
        if (isset($labels[$key]) && isset($labels[$key]['non_delivery'])) {
            return !empty(trim($labels[$key]['non_delivery']))
                ? $labels[$key]['non_delivery']
                : $this->getConfigCarrier('non_delivery_handling');
        }

        return $this->getConfigCarrier('non_delivery_handling');
    }

    /**
     * Check is can process manifests for Canada Post shipment
     *
     * @return bool
     */
    public function canChooseManifest()
    {
        if (!$this->isContractShipment()) {
            return false;
        }

        $shipment = $this->_registry->registry('current_shipment');
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

    /**
     * @param $data
     * @throws \Exception
     */
    public function saveRequestLogRecord($data)
    {
        if ($this->getConfigCarrier('debug')) {
            try {
                /** @var \Mageside\CanadaPostShipping\Model\RequestLog $model */
                $model = $this->_requestLogModelFactory->create();
                /** @var \Mageside\CanadaPostShipping\Model\ResourceModel\RequestLog $resourceModel */
                $resourceModel = $this->_requestLogResourceFactory->create();
                $model->setData($data);
                $resourceModel->save($model);
            } catch (\Exception $e) {

            }
        }
    }
}
