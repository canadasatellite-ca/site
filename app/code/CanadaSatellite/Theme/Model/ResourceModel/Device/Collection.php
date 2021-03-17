<?php

namespace CanadaSatellite\Theme\Model\ResourceModel\Device;

/**
 * Device collection
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

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
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
        $this->_init('CanadaSatellite\Theme\Model\Device', 'CanadaSatellite\Theme\Model\ResourceModel\Device');
    }

    /**
     * Add customer filter
     *
     * @param int $magentoCustomerId
     * @return \CanadaSatellite\Theme\Model\ResourceModel\Sim\Collection
     */
    public function addCustomerFilter($magentoCustomerId)
    {
        $this->_magentoCustomerId = $magentoCustomerId;
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
    public function loadWithFilter($printQuery = false, $logQuery = false)
    {
        $this->_beforeLoad();
        //$this->printLogQuery($printQuery, $logQuery);
        // FIXME retrieve only one page? but _totalRecords
        $data = $this->_restApi->getDevicesByCustomerId($this->_magentoCustomerId);
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
}
