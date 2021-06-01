<?php

namespace CanadaSatellite\Theme\Model\ResourceModel;

/**
 * Device resource model
 */
class Device extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource device model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('', 'new_deviceid');
    }

    /**
     * It is supposed to return main table name but we do not use real db
     *
     * @return string
     * @api
     */
    function getMainTable()
    {
        return '';
    }

    function save(\Magento\Framework\Model\AbstractModel $object) {
        // save operations here
        // make sure to update your model with the data!
    }

    function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null) {
        // loading operations here
        // make sure to update your model with the data!
    }

    function delete(\Magento\Framework\Model\AbstractModel $object) {
        // delete operations here
    }
}