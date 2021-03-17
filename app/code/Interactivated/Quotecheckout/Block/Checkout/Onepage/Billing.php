<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;

class Billing extends \Interactivated\Quotecheckout\Block\Checkout\Onepage\AbstractOnepage
{
	/**
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry;

	/**
	 * @var \Interactivated\Quotecheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Sales Quote Billing Address instance
     *
     * @var \Magento\Quote\Model\Quote\Address
     */
    protected $_address;

    /**
     * Customer Taxvat Widget block
     *
     * @var \Magento\Customer\Block\Widget\Taxvat
     */
    protected $_taxvat;

    /**
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $resourceSession
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressConfig $addressConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Quote\Model\Quote\AddressFactory $addressFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Interactivated\Quotecheckout\Helper\Data $dataHelper
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param array $data
     */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $resourceSession,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        CustomerRepositoryInterface $customerRepository,
        AddressConfig $addressConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
		\Magento\Framework\Registry $coreRegistry,
		\Interactivated\Quotecheckout\Helper\Data $dataHelper,
		array $data = []
	) {
		$this->_coreRegistry = $coreRegistry;
		$this->_dataHelper = $dataHelper;
		$this->_sessionManager = $context->getSession();
        $this->_customerSession = $customerSession;
        $this->_addressFactory = $addressFactory;

		parent::__construct(
            $context,
            $directoryHelper,
            $configCacheType,
            $customerSession,
            $resourceSession,
            $countryCollectionFactory,
            $regionCollectionFactory,
            $customerRepository,
            $addressConfig,
            $httpContext,
            $addressMapper,
            $data
        );

        $this->_isScopePrivate = true;
	}

    /**
     * Initialize billing address step
     *
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'billing',
            ['label' => __('Billing Information'), 'is_show' => $this->isShow()]
        );

        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
        parent::_construct();
    }

    /**
     * @return bool
     */
    public function isUseBillingAddressForShipping()
    {
        if ($this->getQuote()->getIsVirtual() || !$this->getQuote()->getShippingAddress()->getSameAsBilling()) {
            return false;
        }
        return true;
    }

    /**
     * Return country collection
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    public function getCountries()
    {
        return $this->_countryCollectionFactory->create()->loadByStore();
    }

    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Return Sales Quote Address model
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getAddress()
    {
        if ($this->_address === null) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getBillingAddress();
                if (!$this->_address->getFirstname()) {
                    $this->_address->setFirstname($this->getQuote()->getCustomer()->getFirstname());
                }
                if (!$this->_address->getLastname()) {
                    $this->_address->setLastname($this->getQuote()->getCustomer()->getLastname());
                }
            } else {
                $this->_address = $this->_addressFactory->create();
            }
        }

        return $this->_address;
    }

    /**
     * Return Customer Address First Name
     * If Sales Quote Address First Name is not defined - return Customer First Name
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->getAddress()->getFirstname();
    }

    /**
     * Return Customer Address Last Name
     * If Sales Quote Address Last Name is not defined - return Customer Last Name
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->getAddress()->getLastname();
    }

    /**
     * Check is Quote items can ship to
     *
     * @return bool
     */
    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }

    /**
     * @return void
     */
    public function getSaveUrl()
    {
    }

    /**
     * Get Customer Taxvat Widget block
     *
     * @return \Magento\Customer\Block\Widget\Taxvat
     */
    protected function _getTaxvat()
    {
        if (!$this->_taxvat) {
            $this->_taxvat = $this->getLayout()->createBlock('Magento\Customer\Block\Widget\Taxvat');
        }

        return $this->_taxvat;
    }

    /**
     * Check whether taxvat is enabled
     *
     * @return bool
     */
    public function isTaxvatEnabled()
    {
        return $this->_getTaxvat()->isEnabled();
    }

    /**
     * @return string
     */
    public function getTaxvatHtml()
    {
        return $this->_getTaxvat()->setTaxvat(
            $this->getQuote()->getCustomerTaxvat()
        )->setFieldIdFormat(
            'billing:%s'
        )->setFieldNameFormat(
            'billing[%s]'
        )->toHtml();
    }

    public function getCountryHtmlSelect($type)
    {
        if ($this->_sessionManager->getCountryId()) {
            $countryId = $this->_sessionManager->getCountryId();
        } else {
            $countryId = $this->getAddress()->getCountryId();
        }

        if (is_null($countryId)) {
            if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/enable_geoip')) {
                $countryId = $this->_coreRegistry->registry('Countrycode');
            } else if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/default_country')) {
                $countryId = $this->_dataHelper->getStoreConfig('onestepcheckout/general/default_country');
            } else {
                $countryId = $this->_dataHelper->getStoreConfig('general/country/default');
            }
        }

        $select = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select billing_country')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        return $select->getHtml();
    }

    /**
     * Retrive session manager object
     * @return object
     */
    public function getSessionManager()
    {
        return $this->_sessionManager;
    }
}
