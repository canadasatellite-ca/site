<?php

namespace CanadaSatellite\Theme\Model;

/**
 * Sim model
 *
 */
class Sim extends \Magento\Framework\Model\AbstractModel
{
    const SIM_GRID_INDEXER_ID = 'sim_grid';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'sim_block';

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CanadaSatellite\Theme\Model\ResourceModel\Sim');
    }

    // getters for formatted values
    /**
     * @return string
     */
    function getNetworkStatus()
    {
        return $this->getPropertyOrEmptyString('cs_simstatus@OData.Community.Display.V1.FormattedValue');
    }

    /**
     * @return string
     */
    function getSubStatus()
    {
        return $this->getPropertyOrEmptyString('new_substatus@OData.Community.Display.V1.FormattedValue');
    }

    /**
     * @return string
     */
    function getNickname()
    {
        return $this->getPropertyOrEmptyString('new_nickname');
    }

    /**
     * @return string
     */
    function getSimNumber()
    {
        return $this->getPropertyOrEmptyString('cs_number');
    }

    /**
     * @return string
     */
    function getNetwork()
    {
        return $this->getPropertyOrEmptyString('cs_network@OData.Community.Display.V1.FormattedValue');
    }

    /**
     * @return string
     */
    function getType()
    {
        return $this->getPropertyOrEmptyString('cs_type@OData.Community.Display.V1.FormattedValue');
    }

    /**
     * @return string
     */
    function getService()
    {
        return $this->getPropertyOrEmptyString('cs_service@OData.Community.Display.V1.FormattedValue');
    }

    /**
     * @return string
     */
    function getPlan()
    {
        return $this->getPropertyOrEmptyString('cs_plan@OData.Community.Display.V1.FormattedValue');
    }

    /**
     * @return string
     */
    function getCurrentMinutes()
    {
        return $this->getPropertyOrEmptyString('cs_currentminutes');
    }

    /**
     * @return string
     */
    function getSatelliteNumber()
    {
        return $this->getPropertyOrEmptyString('cs_satellitenumber');
    }

    /**
     * @return string
     */
    function getDataNumber()
    {
        return $this->getPropertyOrEmptyString('cs_data');
    }

    /**
     * @return int|null unix timestamp
     */
    function getActivationDate()
    {
        return isset($this->_data['cs_activationdate']) ? strtotime($this->_data['cs_activationdate']) : null;
    }

    /**
     * @return int|null unix timestamp
     */
    function getExpiryDate()
    {
        return isset($this->_data['cs_expirydate']) ? strtotime($this->_data['cs_expirydate']) : null;
    }

    /**
     * @return string
     */
    function getQuickNote()
    {
        return $this->getPropertyOrEmptyString('new_quicknote');
    }

    function isHijacked()
    {
        if (!isset($this->_data['new_substatus'])) {
            return false;
        }

        return
            $this->_data['new_substatus'] == '100000008' || // HIJACK
            $this->_data['new_substatus'] == '100000009'; // HIJACKED
    }

    function isAutoRecharged()
    {
        if (!isset($this->_data['new_substatus'])) {
            return false;
        }
        
        return
            $this->_data['new_substatus'] == '100000000' || // AUTO-RECHARGE
            $this->_data['new_substatus'] == '100000014'; // AUTO-RECHARGE - PAID
    }

    function isIssued()
    {
        if (!isset($this->_data['cs_simstatus'])) {
            return false;
        }

        return $this->_data['cs_simstatus'] == '100000000'; // ISSUED
    }

    function wasRecentlyActivated()
    {
        if (!isset($this->_data['activationrequests_count'])) {
            return false;
        }

        return $this->_data['activationrequests_count'] > 0;
    }

    /**
     * @return int 0 if not set
     */
    function getMagentoCustomerId()
    {
        return intval($this->_data['magento_customer_id'] ?: 0);
    }

    private function getPropertyOrEmptyString($name) {
        if (!isset($this->_data[$name])) {
            return '';
        }

        return $this->_data[$name] ?: '';
    }
}