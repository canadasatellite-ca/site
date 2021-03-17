<?php

namespace CanadaSatellite\Theme\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Request\Http;
use MageWorx\OptionFeatures\Ui\DataProvider\Product\Form\Modifier\Features as ParentFeatures;

class Features extends ParentFeatures
{
    public function __construct(
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager,
        LocatorInterface $locator,
        Helper $helper,
        Http $request,
        UrlInterface $urlBuilder)
    {
        parent::__construct(
            $arrayManager,
            $storeManager,
            $locator,
            $helper,
            $request,
            $urlBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        if ($this->request->getRouteName() == 'mageworx_optiontemplates') {
            $this->form = 'mageworx_optiontemplates_group_form';
        }

        $this->addFeaturesFields();

        $this->addImageModal();
        $this->addImagesButton();

        return $this->meta;
    }

    /**
     * Adds features fields to the meta-data
     */
    protected function addFeaturesFields()
    {
        $groupCustomOptionsName    = CustomOptions::GROUP_CUSTOM_OPTIONS_NAME;
        $optionContainerName       = CustomOptions::CONTAINER_OPTION;
        $commonOptionContainerName = CustomOptions::CONTAINER_COMMON_NAME;

        // Add fields to the values
        $valueFeaturesFields = $this->getValueFeaturesFieldsConfig();
        $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
        [$optionContainerName]['children']['values']['children']['record']['children'] = array_replace_recursive(
            $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
            [$optionContainerName]['children']['values']['children']['record']['children'],
            $valueFeaturesFields
        );

        // Add fields to the option
        $optionFeaturesFields = $this->getOptionFeaturesFieldsConfig();
        $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
        [$optionContainerName]['children'][$commonOptionContainerName]['children'] = array_replace_recursive(
            $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
            [$optionContainerName]['children'][$commonOptionContainerName]['children'],
            $optionFeaturesFields
        );

        // Add fields to the options container
        $productFeaturesFields  = $this->getProductFeaturesFieldsConfig();
        $this->meta[$groupCustomOptionsName]['children'] = array_replace_recursive(
            $this->meta[$groupCustomOptionsName]['children'],
            $productFeaturesFields
        );
    }

    /**
     * The custom option fields config
     *
     * @return array
     */
    protected function getOptionFeaturesFieldsConfig()
    {
        $fields = [];

        $fields['currency_code'] = $this->getCurrencyCodeConfig(58);

        if ($this->helper->isOneTimeEnabled()) {
            $fields[Helper::KEY_ONE_TIME] = $this->getOneTimeConfig(60);
        }

        if ($this->helper->isQtyInputEnabled()) {
            $fields[Helper::KEY_QTY_INPUT] = $this->getQtyInputConfig(63);
        }

        $fields[Helper::KEY_IS_HIDDEN] = $this->getIsHiddenConfig(66);

        return $fields;
    }

    protected function getCurrencyCodeConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Currency Code'),
                        'componentType' => 'field',
                        'formElement' => 'select',
                        'component' => 'Magento_Catalog/js/custom-options-type',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        //'dataScope' => 'type',
                        'selectType' => 'optgroup',
                        'dataType' => 'text',
                        'sortOrder' => $sortOrder,
                        'options' => [
                            ['value' => 'CAD', 'label' => 'C$'],
                            ['value' => 'USD', 'label' => '$'],
                            ['value' => 'AUD', 'label' => 'A$'],
                            ['value' => 'EUR', 'label' => '€']
                        ],
                        'disableLabel' => true,
                        'multiple' => false,
                        'validation' => [
                            'required-entry' => true
                        ],
                    ],
                ],
            ],
        ];
    }
}
