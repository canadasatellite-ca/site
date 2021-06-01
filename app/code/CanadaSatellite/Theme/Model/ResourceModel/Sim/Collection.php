<?php

namespace CanadaSatellite\Theme\Model\ResourceModel\Sim;

/**
 * Sim collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * REST API for MS Dynamics
     *
     * @var \CanadaSatellite\DynamicsIntegration\Rest\RestApi
     */
    protected $_restApi;

    protected $_magentoCustomerId;

    protected $_simField;

    protected $_simSorting;

    protected $_simFilter;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_restApi = $restApi;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CanadaSatellite\Theme\Model\Sim', 'CanadaSatellite\Theme\Model\ResourceModel\Sim');
    }

    /**
     * Add customer filter
     *
     * @param int $magentoCustomerId
     * @return \CanadaSatellite\Theme\Model\ResourceModel\Sim\Collection
     */
    function addCustomerFilter($magentoCustomerId)
    {
        $this->_magentoCustomerId = $magentoCustomerId;
        return $this;
    }

    /**
     * Add sorting by SIM's field
     *
     * @param int $field
     * @param int $sorting
     * @return Collection
     */
    function addSimSorting($field, $sorting)
    {
        $this->_simField = $field;
        $this->_simSorting = $sorting;
        return $this;
    }

    /**
     * add filtration to selecting SIMs
     *
     * @param $filter
     * @return Collection
     */
    function addSimFilter($filter)
    {
        $this->_simFilter = $filter;
        return $this;
    }

    /**
     * Load data with filter in place
     * it is not designed to load without customerId filter!
     *
     * @param   bool $printQuery
     * @param   bool $logQuery
     * @return  $this
     */
    function loadWithFilter($printQuery = false, $logQuery = false)
    {
        $this->_beforeLoad();
        //$this->printLogQuery($printQuery, $logQuery);
        // FIXME retrieve only one page? but _totalRecords
        $data = $this->_restApi->getSimsByCustomerId($this->_magentoCustomerId, $this->_simField, $this->_simSorting, $this->_simFilter);
        $requests = $this->_restApi->getSimsActivationRequestsCountByCustomerId($this->_magentoCustomerId);
        $this->setActivationRequestsCount($data, $requests);

        $this->resetData();
        $this->_totalRecords = count($data);
        if (is_array($data)) {
            if ($this->_pageSize) {
                $start = $this->_pageSize*($this->getCurPage() - 1);
                $end = min($start + $this->_pageSize, $this->_totalRecords);
            } else {
                $start = 0;
                $end = $this->_totalRecords;
            }
            for($i = $start; $i < $end; $i++) {
                $item = $this->getNewEmptyItem();
                if ($this->getIdFieldName()) {
                    $item->setIdFieldName($this->getIdFieldName());
                }
                $item->addData($data[$i]);
                $this->beforeAddLoadedItem($item);
                $this->addItem($item);
            }
        }
        $this->_setIsLoaded();
        $this->_afterLoad();
        return $this;
    }

    private function setActivationRequestsCount(&$sims, $requests)
    {
        if (!is_array($sims) || !is_array($requests)) {
            return;
        }

        $requestsBySimId = array();
        foreach ($requests as $request) {
            $simId = $request['cs_simid'];

            $requestsBySimId[$simId] = $request;
        }
        unset($request);

        foreach ($sims as &$sim) {
            $simId = $sim['cs_simid'];

            if (array_key_exists($simId, $requestsBySimId)) {
                $request = $requestsBySimId[$simId];
                $sim['activationrequests_count'] = $request['activationrequests_count'];
            } else {
                $sim['activationrequests_count'] = 0;
            }
        }
        unset($sim);
    }
}
