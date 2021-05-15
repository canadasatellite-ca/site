<?php

namespace Interactivated\Quotecheckout\Block\Checkout\Onepage\Shipping;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\Config as AddressConfig;

class Method extends \Interactivated\Quotecheckout\Block\Checkout\Onepage\AbstractOnepage
{
    protected $tmpCheckuotSession;
    function __construct(
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
        array $data)
    {
        parent::__construct($context,
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
            $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'shipping_method',
            ['label' => __('Shipping Method'), 'is_show' => $this->isShow()]
        );
        parent::_construct();
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }
}
