<?php

namespace MW\Onestepcheckout\Model\Quote;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;

class Address extends \Magento\Quote\Model\Quote\Address
{
    /**
     * @var \MW\Onestepcheckout\Helper\Data
     */
    protected $_oscDataHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param AddressMetadataInterface $metadataService
     * @param AddressInterfaceFactory $addressDataFactory
     * @param RegionInterfaceFactory $regionDataFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\ItemFactory $addressItemFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateCollectorInterfaceFactory $rateCollector
     * @param \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory
     * @param \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory
     * @param \Magento\Quote\Model\Quote\Address\Total\CollectorFactory $totalCollectorFactory
     * @param \Magento\Quote\Model\Quote\Address\TotalFactory $addressTotalFactory
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory
     * @param \Magento\Quote\Model\Quote\Address\Validator $validator
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Quote\Model\Quote\Address\CustomAttributeListInterface $attributeList
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magento\Quote\Model\Quote\TotalsReader $totalsReader
     * @param \MW\Onestepcheckout\Helper\Data $oscDataHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        AddressMetadataInterface $metadataService,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\ItemFactory $addressItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory,
        \Magento\Quote\Model\Quote\Address\RateCollectorInterfaceFactory $rateCollector,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory,
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Magento\Quote\Model\Quote\Address\Total\CollectorFactory $totalCollectorFactory,
        \Magento\Quote\Model\Quote\Address\TotalFactory $addressTotalFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory,
        \Magento\Quote\Model\Quote\Address\Validator $validator,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Quote\Model\Quote\Address\CustomAttributeListInterface $attributeList,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Quote\Model\Quote\TotalsReader $totalsReader,
        \MW\Onestepcheckout\Helper\Data $oscDataHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $directoryData,
            $eavConfig,
            $addressConfig,
            $regionFactory,
            $countryFactory,
            $metadataService,
            $addressDataFactory,
            $regionDataFactory,
            $dataObjectHelper,
            $scopeConfig,
            $addressItemFactory,
            $itemCollectionFactory,
            $addressRateFactory,
            $rateCollector,
            $rateCollectionFactory,
            $rateRequestFactory,
            $totalCollectorFactory,
            $addressTotalFactory,
            $objectCopyService,
            $carrierFactory,
            $validator,
            $addressMapper,
            $attributeList,
            $totalsCollector,
            $totalsReader,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_oscDataHelper = $oscDataHelper;
    }

    /**
     * Validate address attribute values
     *
     * @return bool|array
     */
    public function validate()
    {
        $errors = [];
        if (!\Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
            $errors[] = __('Please enter the first name.');
        }

        if (!\Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
            $errors[] = __('Please enter the last name.');
        }

        if (!\Zend_Validate::is($this->getStreetLine(1), 'NotEmpty')) {
            $errors[] = __('Please enter the street.');
        }

        // Check require option of city in onestepcheckout configuration
        if ($this->_oscDataHelper->getStoreConfig('onestepcheckout/addfield/city') == 2) {
            if (!\Zend_Validate::is($this->getCity(), 'NotEmpty')) {
                $errors[] = __('Please enter the city.');
            }
        }

        // Check require option of telephone in onestepcheckout configuration
        if ($this->_oscDataHelper->getStoreConfig('onestepcheckout/addfield/telephone') == 2) {
            if (!\Zend_Validate::is($this->getTelephone(), 'NotEmpty')) {
                $errors[] = __('Please enter the phone number.');
            }
        }

        // Check require option of zip/postal code in onestepcheckout configuration
        if ($this->_oscDataHelper->getStoreConfig('onestepcheckout/addfield/zip') == 2) {
            $_havingOptionalZip = $this->_directoryData->getCountriesWithOptionalZip();
            if (!in_array(
                    $this->getCountryId(),
                    $_havingOptionalZip
                ) && !\Zend_Validate::is(
                    $this->getPostcode(),
                    'NotEmpty'
                )
            ) {
                $errors[] = __('Please enter the zip/postal code.');
            }
        }

        // Check require option of country in onestepcheckout configuration
        if ($this->_oscDataHelper->getStoreConfig('onestepcheckout/addfield/country') == 2) {
            if (!\Zend_Validate::is($this->getCountryId(), 'NotEmpty')) {
                $errors[] = __('Please enter the country.');
            }
        }

        // Check require option of state/province in onestepcheckout configuration
        if ($this->_oscDataHelper->getStoreConfig('onestepcheckout/addfield/state') == 2) {
            if ($this->getCountryModel()->getRegionCollection()->getSize() && !\Zend_Validate::is(
                    $this->getRegionId(),
                    'NotEmpty'
                ) && $this->_directoryData->isRegionRequired(
                    $this->getCountryId()
                )
            ) {
                $errors[] = __('Please enter the state/province.');
            }
        }

        if (empty($errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }

        return $errors;
    }
}
