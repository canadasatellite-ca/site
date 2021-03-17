<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Ui\DataProvider\Supplier\Form\Modifier;

use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Field;
use Magento\Directory\Model\Config\Source\Country as SourceCountry;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod;
use Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentTerm;
/**
 * Data provider for Configurable panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingPayment extends \Magestore\SupplierSuccess\Ui\DataProvider\Supplier\DataForm\Modifier\AbstractModifier
{
    /**
     * @var array
     */
    protected $countries;

    /**
     * @var SourceCountry
     */
    protected $sourceCountry;

    /**
     * @var array
     */
    protected $regions;
    protected $objectManager;
    protected $shippingMethods;
    protected $paymentTerms;


    public function __construct(
        SourceCountry $sourceCountry,
        DirectoryHelper $directoryHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        $this->sourceCountry = $sourceCountry;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            $this->getSupplierInformation($meta)
        );
        return $meta;
    }

    /**
     * @param $meta
     * @return mixed
     */
    public function getSupplierInformation($meta)
    {
        $meta['casat-shipping-payment']['arguments']['data']['config'] = [
            'label' => __('Shipping and Payment'),
            'collapsible' => true,
            'visible' => true,
            'opened' => false,
            'dataScope' => 'data',
            'componentType' => Form\Fieldset::NAME
        ];
        $meta['casat-shipping-payment']['children'] = $this->getSupplierAddressChildren();
        return $meta;
    }

    /**
     * @return array
     */
    public function getSupplierAddressChildren()
    {
        $this->shippingMethods = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\ShippingMethod')->getOptionArray();
        $this->paymentTerms = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\PaymentTerm')->getOptionArray();
        $orderSource = $this->objectManager
            ->get('Magestore\PurchaseOrderSuccess\Model\PurchaseOrder\Option\OrderSource')->getOptionArray();
        $children = [
            'shipping_address' => $this->addFormFieldTextArea('Shipping Address', 10),
            'shipping_method' => $this->addFormFieldSelect(
                'Shipping Method', $this->shippingMethods, 20, false,
                ShippingMethod::OPTION_NONE_VALUE, ''
            ),
            'shipping_cost' => $this->addFormFieldText('Shipping Cost', 'input', 40),
            'payment_term' => $this->addFormFieldSelect(
                'Payment Term',  $this->paymentTerms, 70, false,
                PaymentTerm::OPTION_NONE_VALUE, ''
            ),
            'placed_via' => $this->addFormFieldSelect('Sales Place Via', $orderSource, 90),
        ];
        return $children;
    }
    public function addFormFieldTextArea(
        $label, $sortOrder, $validation = false, $defaultValue = null, $notice = ''){
        return $this->addFormField($label, 'text', 'textarea', $sortOrder, $validation, $defaultValue, $notice);
    }
    public function addFormField(
        $label, $dataType , $formElement, $sortOrder, $validation = false, $defaultValue = null, $notice = ''){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'label' => __($label),
                        'dataType' => $dataType,
                        'formElement' => $formElement,
                        'sortOrder' => $sortOrder,
                        'default' => $defaultValue,
                        'notice' => __($notice),
                    ],
                ],
            ],
        ];
        if($validation)
            $field['arguments']['data']['config']['validation'] = ['required-entry' => $validation];
        if($defaultValue)
            $field = $this->setDefaultValue($field, $defaultValue);
        return $field;
    }
    public function addFormFieldSelect(
        $label, $options = [], $sortOrder, $validation = false, $defaultValue = null, $notice = '', $switcherConfig = null, $disabled = false
    ){
        $field = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType' => 'text',
                        'formElement' => 'select',
                        'options' => $options,
                        'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                        'label' => __($label),
                        'sortOrder' => $sortOrder,
                        'validation' => ['required-entry' => $validation],
                        'notice' => __($notice),
                    ],
                ],
            ],
        ];
        if($defaultValue)
            $field = $this->setDefaultValue($field, $defaultValue);
        if($switcherConfig)
            $field = $this->addSwitcherConfig($field, $switcherConfig);
        if($disabled)
            $field['arguments']['data']['config']['disabled'] = $disabled;
        return $field;
    }
    public function setDefaultValue($field, $value){
        $field['arguments']['data']['config']['default'] = $value;
        return $field;
    }
    public function addFormFieldText(
        $label, $formElement, $sortOrder, $validation = false, $defaultValue = null, $notice = '', $disabled = false){
        $field = $this->addFormField($label, 'text', $formElement, $sortOrder, $validation, $defaultValue, $notice);
        if($disabled)
            $field['arguments']['data']['config']['disabled'] = $disabled;
        return $field;
    }
}
