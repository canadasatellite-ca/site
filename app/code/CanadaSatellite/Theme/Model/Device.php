<?php

namespace CanadaSatellite\Theme\Model;

/**
 * Device model
 *
 */
class Device extends \Magento\Framework\Model\AbstractModel
{
    const SIM_GRID_INDEXER_ID = 'device_grid';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'device_block';

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CanadaSatellite\Theme\Model\ResourceModel\Device');
    }

    // getters for formatted values
    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->getPropertyOrDefault('ordernumber', '');
    }

    /**
     * @return string
     */
    public function getImei()
    {
        return $this->getPropertyOrDefault('new_name', '');
    }

    /**
     * @return string
     */
    public function getProduct()
    {
        return $this->getPropertyOrDefault('productname', '');
    }

    /**
     * @return int|null unix timestamp
     */
    public function getSaleDate()
    {
        return isset($this->_data['new_saledate']) ? strtotime($this->_data['new_saledate']) : null;
    }

    private function getPropertyOrDefault($prop, $default = '')
    {
        if (!isset($this->_data[$prop])) {
            return $default;
        }

        return $this->_data[$prop];
    }
}