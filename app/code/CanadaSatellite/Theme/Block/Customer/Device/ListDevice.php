<?php
namespace CanadaSatellite\Theme\Block\Customer\Device;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer Devices list block
 */
class ListDevice extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * device collection
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Device\Collection
     */
    protected $_collection;

    /**
     * Device resource model
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Device\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * REST API for MS Dynamics
     *
     * @var \CanadaSatellite\DynamicsIntegration\Rest\RestApi
     */
    protected $_restApi;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * ListDevice constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \CanadaSatellite\Theme\Model\ResourceModel\Device\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi
     * @param \Magento\Catalog\Model\ProductFactory $_productFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \CanadaSatellite\Theme\Model\ResourceModel\Device\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
        \Magento\Catalog\Model\ProductFactory $_productFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->currentCustomer = $currentCustomer;
        $this->_restApi = $restApi;
        $this->_productFactory = $_productFactory;
        $this->coreRegistry = $registry;
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getDevices()) {
            $toolbar = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer_device_list.toolbar'
            )->setCollection(
                $this->getDevices()
            );

            $this->setChild('toolbar', $toolbar);
        }
        return parent::_prepareLayout();
    }

    /**
     * Get devices
     *
     * @return bool|\CanadaSatellite\Theme\Model\ResourceModel\Device\Collection
     */
    public function getDevices()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return false;
        }
        if (!$this->_collection) {
            $this->_collection = $this->_collectionFactory->create();
            $this->_collection->addCustomerFilter($customerId);
        }
        return $this->_collection;
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getProductByName($name)
    {
        try{
            return $this->_productFactory->create()->loadByAttribute('name', $name);
        }
        catch(\Exception $e){

        }
    }

    /**
     * @return \Magento\Framework\Registry
     */
    public function getRegistry()
    {
        return $this->coreRegistry;
    }


}
