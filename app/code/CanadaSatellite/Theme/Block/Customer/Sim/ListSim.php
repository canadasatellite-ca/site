<?php
namespace CanadaSatellite\Theme\Block\Customer\Sim;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use CanadaSatellite\DynamicsIntegration\Enums\Sims\SimTableField;
use CanadaSatellite\DynamicsIntegration\Enums\Sims\SortingDirection;
use CanadaSatellite\DynamicsIntegration\Enums\Sims\FilterNetworkStatus;

/**
 * Customer Sims list block
 */
class ListSim extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * sim collection
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Sim\Collection
     */
    protected $_collection;

    /**
     * Sim resource model
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Sim\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Customer Tickets list block constructor
     *
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \CanadaSatellite\Theme\Model\ResourceModel\Sim\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \CanadaSatellite\Theme\Model\ResourceModel\Sim\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_sessionManager = $sessionManager;
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
    }

    /**
     * Load sim
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $sims = $this->getSims();
        if ($sims) {
            $sims->load();
        }
        return parent::_beforeToHtml();
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        if ($this->getSims()) {
            $toolbar = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer_sim_list.toolbar'
            )->setCollection(
                $this->getSims()
            );

            $this->setChild('toolbar', $toolbar);
        }
        return parent::_prepareLayout();
    }



    /**
     * Get html code for toolbar
     *
     * @return string
     */
    function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    function wasRecentlyActivated($sim)
    {
        $activatedSimsIds = $this->getRequest()->getParam('activated');
        $activated = false;
        if ($activatedSimsIds && is_array($activatedSimsIds)) {
            $activated = in_array($sim->getId(), $activatedSimsIds);
        }

        return $activated || $sim->wasRecentlyActivated();
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }



    /**
     * Get sims
     *
     * @return bool|\CanadaSatellite\Theme\Model\ResourceModel\Sim\Collection
     */
    function getSims()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return false;
        }
        if (!$this->_collection) {
            $this->restoreTableState($this->getRequest());
            $this->_collection = $this->_collectionFactory->create();
            $this->_collection->addCustomerFilter($customerId);
            $this->_collection->addSimSorting($this->getField(), $this->getSorting());
            $this->_collection->addSimFilter($this->getFilter());
        }
        return $this->_collection;
    }

    /**
     * Restore SIM's table state from request
     *
     * @param $request
     */
    function restoreTableState($request)
    {
        $field = $request->getParam(SimTableField::RequestParam);
        if($field === null) {
            $field = $this->_sessionManager->getField();
            if ($field === null) {
                $this->_sessionManager->setField(SimTableField::NetworkStatus);
            }
        }
        else {
            $this->_sessionManager->setField($field);
        }

        $sorting = $request->getParam(SortingDirection::RequestParam);
        if($sorting === null) {
            $sorting = $this->_sessionManager->getSorting();
            if ($sorting === null) {
                $this->_sessionManager->setSorting(SortingDirection::Ascending);
            }
        }
        else {
            $this->_sessionManager->setSorting($sorting);
        }

        $filter = $request->getParam(FilterNetworkStatus::RequestParam);
        if($filter === null) {
            $filter = $this->_sessionManager->getFilter();
            if ($filter === null) {
                $this->_sessionManager->setFilter(FilterNetworkStatus::None);
            }
        }
        else {
            $this->_sessionManager->setFilter($filter);
        }
    }

    /**
     * Reset SIMs filter
     */
    function resetSimsFilter()
    {
        $this->_sessionManager->setFilter(FilterNetworkStatus::None);
    }



    /**
     * Return current sorting field from session store
     * @return mixed
     */
    function getField()
    {
        return $this->_sessionManager->getField();
    }

    /**
     * Return current sorting direction from session store
     * @return mixed
     */
    function getSorting()
    {
        return $this->_sessionManager->getSorting();
    }

    /**
     * Return current filter from session store
     * @return mixed
     */
    function getFilter()
    {
        return $this->_sessionManager->getFilter();
    }



    /**
     * @param $field
     * @return mixed
     */
    function formatFieldName($field)
    {
        $fieldName = $this->getFieldName($field);
        $fieldName = $this->escapeHtml(__($fieldName));
        return $fieldName . $this->getSortingSuffix($field);
    }

    /**
     * Return field name for showing in html page
     *
     * @param $field
     * @return string
     */
    private function getFieldName($field)
    {
        switch ($field) {
            case SimTableField::NetworkStatus:
                return 'Network Status';
            case SimTableField::SimSharp:
                return 'SIM #';
            case SimTableField::SatSharp:
                return 'Sat #';
            case SimTableField::Network:
                return 'Network';
            case SimTableField::Plan:
                return 'Plan';
            case SimTableField::CurrentMinutes:
                return 'Current Minutes';
            case SimTableField::ExpiryDate:
                return 'Expiry Date';
            case SimTableField::Nickname:
                return 'Nickname';
            case SimTableField::Select:
                return 'Select';
            default:
                return '';
        }
    }

    /**
     * Return mark of sorting
     *
     * @param $field
     * @return string
     */
    private function getSortingSuffix($field)
    {
        if($field === $this->getField())
        {
            switch ($this->getSorting())
            {
                case SortingDirection::Ascending: return ' ↑';
                case SortingDirection::Descending: return ' ↓';
            }
        }
        return '';
    }

    /**
     * set 'selected' sttribute to 'option' element
     *
     * @param $prevFilter
     * @param $curFilter
     * @return string
     */
    function getSelected($curFilter)
    {
        return ($curFilter === $this->getFilter()) ? 'selected' : '';
    }



    /**
     * Get sim link
     *
     * @return string
     */
    function getSimLink()
    {
        return $this->getUrl('casat/customer/simdetails/');
    }

    function getCardsUrl()
    {
        return $this->getUrl('casat/customer/card_listing');
    }

    function getActivateUrl()
    {
        return $this->getUrl('casat/customer/simactivate');
    }

    function sortingSimsUrl()
    {
        return $this->getUrl('casat/customer/viewsim');
    }

}
