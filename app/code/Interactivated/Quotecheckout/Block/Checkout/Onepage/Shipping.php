<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;

class Shipping extends \Interactivated\Quotecheckout\Block\Checkout\Onepage\AbstractOnepage
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
     * Sales Quote Shipping Address instance
     *
     * @var \Magento\Quote\Model\Quote\Address
     */
    protected $_address = null;

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
        $this->_checkoutSession = $resourceSession;
    }

    /**
     * Initialize shipping address step
     *
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'shipping',
            ['label' => __('Shipping Information'), 'is_show' => $this->isShow()]
        );

        parent::_construct();
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
     * Return Sales Quote Address model (shipping address)
     *
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getAddress()
    {
        if ($this->_address === null) {
            if ($this->isCustomerLoggedIn()) {
                $this->_address = $this->getQuote()->getShippingAddress();
            } else {
                $this->_address = $this->_addressFactory->create();
            }
        }

        return $this->_address;
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }

	public function getAddressesHtmlSelect($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = [];
            $customer = $this->_customerSession->getCustomer();
            foreach ($customer->getAddresses() as $address) {
                $options[] = [
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                ];
            }

            $addressId = $this->getAddress()->getId();
            if (empty($addressId)) {
                if ($type == 'billing') {
                    $address = $customer->getPrimaryBillingAddress();
                } else {
                    $address = $customer->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select')
                ->setValue($addressId)
                ->setOptions($options);
            $select->addOption('', __('New Address'));

            return $select->getHtml();
        }

        return '';
    }

 	public function getCountryHtmlSelect($type)
    {
    	if($this->_sessionManager->getCountryId()) {
    		$countryId = $this->_sessionManager->getCountryId();
    	} else {
    		$countryId = $this->getAddress()->getCountryId();
    	}

        if (is_null($countryId)) {
			if ($this->_dataHelper->getStoreConfig('onestepcheckout/general/enable_geoip')) {
				$countryId = $this->_coreRegistry->registry('Countrycode');
			} elseif ($this->_dataHelper->getStoreConfig('onestepcheckout/general/default_country')) {
				$countryId = $this->_dataHelper->getStoreConfig('onestepcheckout/general/default_country');
			} else {
				$countryId = $this->_dataHelper->getStoreConfig('general/country/default');
			}
        }

        $select = $this->getLayout()->createBlock('Magento\Framework\View\Element\Html\Select')
            ->setName($type.'[country_id]')
            ->setId($type.':country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select shipping_country')
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
