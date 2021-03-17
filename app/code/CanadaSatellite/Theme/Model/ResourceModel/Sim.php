<?php

namespace CanadaSatellite\Theme\Model\ResourceModel;

/**
 * Sim resource model
 */
class Sim extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * REST API for MS Dynamics
     *
     * @var \CanadaSatellite\DynamicsIntegration\Rest\RestApi
     */
    protected $_restApi;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        $connectionName = null
    ) {
        $this->_restApi = $restApi;
        parent::__construct($context, $connectionName);
    }

    /**
     * Resource sim model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('', 'cs_simid');
    }

    /**
     * It is supposed to return main table name but we do not use real db
     *
     * @return string
     * @api
     */
    public function getMainTable()
    {
        return '';
    }

    public function save(\Magento\Framework\Model\AbstractModel $object) {
        // save operations here
        // make sure to update your model with the data!
        throw new \Exception("Not implemented");
    }

    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null) {
        $object->beforeLoad($value, $field);
        if ($field !== null) {
            throw new \Exception("Not implemented");
        }

        if ($value !== null) {
            $data = $this->_restApi->getSim($value);
            if ($data) {
                $data['activationrequests_count'] = $this->_restApi->getSimActivationRequestsCount($value);
                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);
        $object->afterLoad();
        $object->setOrigData();
        $object->setHasDataChanges(false);

        return $this;
    }

    public function delete(\Magento\Framework\Model\AbstractModel $object) {
        // delete operations here
        throw new \Exception("Not implemented");
    }
}