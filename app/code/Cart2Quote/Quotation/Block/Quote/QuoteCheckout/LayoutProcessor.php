<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\QuoteCheckout;

/**
 * Class LayoutProcessor
 *
 * @package Cart2Quote\Quotation\Block\Quote\QuoteCheckout
 */
class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * Attribute Mapper
     *
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    protected $attributeMapper;
    /**
     * Attribute Merger
     *
     * @var \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\AttributeMerger
     */
    protected $merger;
    /**
     * Attribute Metadata Data Provider
     *
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;
    /**
     * Options
     *
     * @var \Magento\Customer\Model\Options
     */
    private $options;

    /**
     * Address Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    private $helper;

    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * LayoutProcessor constructor.
     *
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\AttributeMerger $merger
     * @param \Magento\Customer\Model\Options $options
     * @param \Cart2Quote\Quotation\Helper\Address $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\AttributeMerger $merger,
        \Magento\Customer\Model\Options $options,
        \Cart2Quote\Quotation\Helper\Address $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->options = $options;
        $this->merger = $merger;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->logger = $logger;
        $this->customerMetadata = $customerMetadata;
        $this->localeDate = $localeDate;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($jsLayout)
    {
        //create a pointer to keep this readable
        $jsLayoutP = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children'];

        if (!$this->getAllowFullForm() && isset($jsLayoutP['billing-address'], $jsLayoutP['shippingAddress'])) {
            unset($jsLayoutP['billing-address']);
            unset($jsLayoutP['shippingAddress']);

            if (isset($jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'])) {
                $jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'] = $this
                    ->removeNonRequiredGlobalFields(
                        $jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'],
                        ['remarks']
                    );
            }

            return $jsLayout;
        }

        //used in call_user_func in convertElementsToSelect
        $attributesToConvert = [
            'prefix' => [$this->options, 'getNamePrefixOptions'],
            'suffix' => [$this->options, 'getNameSuffixOptions'],
            'gender' => [$this, 'getGenderOptions'],
        ];

        $elements = $this->getAddressAttributes();
        $elements = $this->convertElementsToSelect($elements, $attributesToConvert);

        // The following code is a workaround for custom address attributes
        if (isset($jsLayoutP['billing-address']['children']['billing-address-fieldset']['children'])) {
            $fields = $jsLayoutP['billing-address']['children']['billing-address-fieldset']['children'];
            $fields = $this->convertFieldsToSelect($fields, $attributesToConvert);
            $jsLayoutP['billing-address']['children']['billing-address-fieldset']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'billingAddress',
                $fields
            );

            /** Process Config data */
            $jsLayoutP['billing-address']['children']['billing-address-fieldset']['children'] = $this->processFields(
                $jsLayoutP['billing-address']['children']['billing-address-fieldset']['children'],
                $this->getBillingAddressConfig()
            );
        }

        if (isset($jsLayoutP['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
            $fields = $jsLayoutP['shippingAddress']['children']['shipping-address-fieldset']['children'];
            $fields = $this->convertFieldsToSelect($fields, $attributesToConvert);
            $jsLayoutP['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'shippingAddress',
                $fields
            );

            /** Process Config data */
            $jsLayoutP['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->processFields(
                $jsLayoutP['shippingAddress']['children']['shipping-address-fieldset']['children'],
                $this->getShippingAddressConfig()
            );
        }

        if (isset($jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'])) {
            $fields = $jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'];
            $fields = $this->convertFieldsToSelect($fields, $attributesToConvert);
            $fields = $this->configureDob($fields);
            $fields = $this->configureGender($fields);
            $jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'] = $this->merger
                ->merge(
                    $elements,
                    'checkoutProvider',
                    'quotationFieldData',
                    $fields
                );

            $jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children'] = $this
                ->removeNonRequiredGlobalFields(
                    $jsLayoutP['quotation-fields']['children']['account-information-fieldsets']['children']
                );
        }

        return $jsLayout;
    }

    /**
     * Display full form
     *
     * @return bool
     */
    private function getAllowFullForm()
    {
        return $this->helper->getEnableForm();
    }

    /**
     * Get address attributes
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAddressAttributes()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($attribute->getIsUserDefined()) {
                continue;
            }

            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }

        return $elements;
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $elements address attributes
     * @param array $attributesToConvert fields and their callbacks
     * @return array
     */
    private function convertElementsToSelect($elements, $attributesToConvert)
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }

            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }

            $elements[$code]['dataType'] = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $elements;
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $fields address fields
     * @param array $attributesToConvert fields and their callbacks
     * @return array
     */
    private function convertFieldsToSelect($fields, $attributesToConvert)
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($fields) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }

            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }

            if (!isset($fields[$code]['config'])) {
                $fields[$code]['config'] = [];
            }

            $fields[$code]['component'] = 'Magento_Ui/js/form/element/select';
            $fields[$code]['config']['elementTmpl'] = 'ui/form/element/select';
            foreach ($options as $key => $value) {
                $fields[$code]['config']['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $fields;
    }

    /**
     * Process the settings
     *
     * @param array $fields
     * @param \stdClass $config
     * @return array
     */
    private function processFields($fields, $config)
    {
        foreach ($config as $fieldData) {
            if (!$fieldData->enabled) {
                unset($fields[$fieldData->name]);
            } else {
                if (isset($fields[$fieldData->name])) {
                    $fields[$fieldData->name] = $this->convertSettingToFieldMapping(
                        $fields[$fieldData->name],
                        $fieldData
                    );
                } else {
                    $this->logger->info('C2Q: Field isn\'t set in config: ' . $fieldData->name);
                }
            }
        }

        return $fields;
    }

    /**
     * Convert the settings JSON to a field mapping
     *
     * @param array $elementData
     * @param \stdClass $fieldData
     * @return array
     */
    private function convertSettingToFieldMapping($elementData, $fieldData)
    {
        $elementData['visible'] = $fieldData->enabled;
        $elementData['sortOrder'] = $fieldData->sortOrder;
        $elementData['required'] = $fieldData->required;

        if (isset($elementData['children'], $elementData['children'][0])) {
            $elementData['children'][0]['validation']['required-entry'] = $fieldData->required;

            if (isset($elementData['children'][0]['additionalClasses'])) {
                $elementData['children'][0]['additionalClasses'] = $elementData['children'][0]['additionalClasses']
                    . ' ' . $fieldData->additionalCss;
            } else {
                $elementData['children'][0]['additionalClasses'] = $fieldData->additionalCss;
            }

            if ($fieldData->required) {
                $elementData['children'][0]['additionalClasses'] = $elementData['children'][0]['additionalClasses']
                    . ' _required';
            }
        } else {
            $elementData['validation']['required-entry'] = $fieldData->required;

            if (isset($elementData['additionalClasses'])) {
                $elementData['additionalClasses'] = $elementData['additionalClasses'] . ' ' . $fieldData->additionalCss;
            } else {
                $elementData['additionalClasses'] = $fieldData->additionalCss;
            }

            if ($fieldData->required) {
                $elementData['additionalClasses'] = $elementData['additionalClasses'] . ' _required';
            }
        }

        return $elementData;
    }

    /**
     * Get the billing configuration
     *
     * @return array
     */
    private function getBillingAddressConfig()
    {
        if ($billingAddress = $this->helper->getBillingAddressConfig()) {
            return $billingAddress;
        }

        return $this->helper->getDefaultAddressConfig();
    }

    /**
     * Get the shipping configuration
     *
     * @return array
     */
    private function getShippingAddressConfig()
    {
        if ($shippingAddress = $this->helper->getShippingAddressConfig()) {
            return $shippingAddress;
        }

        return $this->helper->getDefaultAddressConfig();
    }

    /**
     * Function to get the gender options
     *
     * @return \Magento\Customer\Api\Data\OptionInterface[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getGenderOptions()
    {
        $options = null;

        try {
            $attributeMetaData = $this->customerMetadata->getAttributeMetadata('gender');
            if ($attributeMetaData) {
                $options = $attributeMetaData->getOptions();

                //make a simple array
                $simpleOptions = [];
                foreach ($options as $option) {
                    $value = $option->getValue();
                    $label = $option->getLabel();
                    $simpleOptions[$value] = $label;
                }

                $options = $simpleOptions;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }

        return $options;
    }

    /**
     * Configure the Gender field
     *
     * @param array $fields
     * @return void|array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function configureGender($fields)
    {
        foreach (array_keys($fields) as $code) {
            if ($code != 'gender') {
                continue;
            }

            if (!isset($fields[$code]['config'])) {
                $fields[$code]['config'] = [];
            }

            $visible = false;
            $required = false;
            try {
                $attributeMetaData = $this->customerMetadata->getAttributeMetadata('gender');
                if ($attributeMetaData) {
                    $visible = (bool)$attributeMetaData->isVisible();
                    $required = (bool)$attributeMetaData->isRequired();
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                continue;
            }

            if (!$visible && !$required) {
                unset($fields[$code]);
                continue;
            }

            if (!$visible) {
                $fields[$code]['visible'] = 'false';
            }

            if (!$required && isset($fields[$code]['validation']) && is_array($fields[$code]['validation'])) {
                unset($fields[$code]['validation']['required-entry']);
            }
        }

        return $fields;
    }

    /**
     * Configure DOB field
     *
     * @param array $fields
     * @return void|array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function configureDob($fields)
    {
        foreach (array_keys($fields) as $code) {
            if ($code != 'dob') {
                continue;
            }

            if (!isset($fields[$code]['config'])) {
                $fields[$code]['config'] = [];
            }

            $visible = $this->getDobVisible();
            $required = $this->getDobRequired();
            if (!$visible && !$required) {
                unset($fields[$code]);
                continue;
            }

            if (!$visible) {
                $fields[$code]['visible'] = 'false';
            }

            if (!$required && isset($fields[$code]['validation']) && is_array($fields[$code]['validation'])) {
                unset($fields[$code]['validation']['required-entry']);
                unset($fields[$code]['validation']['input_validation']);
            } else {
                if (!isset($fields[$code]['validation'])) {
                    $fields[$code]['validation'] = [];
                }

                if (is_array($fields[$code]['validation'])) {
                    $fields[$code]['validation']['input_validation'] = 'date';
                }
            }

            if (isset($fields[$code]['options']) && is_array($fields[$code]['options'])) {
                $dateFormat = $this->localeDate->getDateFormatWithLongYear();
                $fields[$code]['options']['dateFormat'] = $dateFormat;
                $fields[$code]['options']['showTime'] = false;
                $fields[$code]['options']['buttonImage'] = false;
            }
        }

        return $fields;
    }

    /**
     * Get dob field visibility
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDobVisible()
    {
        try {
            $attributeMetaData = $this->customerMetadata->getAttributeMetadata('dob');
            if ($attributeMetaData) {
                return (bool)$attributeMetaData->isVisible();
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }

        return false;
    }

    /**
     * Get dob field requirement
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getDobRequired()
    {
        try {
            $attributeMetaData = $this->customerMetadata->getAttributeMetadata('dob');
            if ($attributeMetaData) {
                return (bool)$attributeMetaData->isRequired();
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }

        return false;
    }

    /**
     * Function that removes all non required global fields
     *
     * @param array $jsLayout
     * @param null|array $requiredFields
     * @return array
     */
    private function removeNonRequiredGlobalFields($jsLayout, $requiredFields = null)
    {
        if ($requiredFields === null) {
            $requiredFields = [
                'dob',
                'gender',
                'remarks'
            ];
        }

        foreach ($jsLayout as $name => $element) {
            if (!in_array($name, $requiredFields)) {
                unset($jsLayout[$name]);
            }
        }

        return $jsLayout;
    }
}
