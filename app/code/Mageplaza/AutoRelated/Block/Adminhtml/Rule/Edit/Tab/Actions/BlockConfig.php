<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AutoRelated
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Actions;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\AutoRelated\Helper\Data;
use Mageplaza\AutoRelated\Model\Config\Source\Additional;
use Mageplaza\AutoRelated\Model\Config\Source\AddProductTypes;
use Mageplaza\AutoRelated\Model\Config\Source\Direction;

/**
 * Class BlockConfig
 * @package Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Actions
 */
class BlockConfig extends Generic
{
    /**
     * @var \Mageplaza\AutoRelated\Model\Config\Source\Direction
     */
    protected $direction;

    /**
     * @var \Mageplaza\AutoRelated\Model\Config\Source\Additional
     */
    protected $additional;

    /**
     * @var \Mageplaza\AutoRelated\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Mageplaza\AutoRelated\Model\Config\Source\AddProductTypes
     */
    protected $addProductTypes;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * BlockConfig constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Mageplaza\AutoRelated\Model\Config\Source\Direction $direction
     * @param \Mageplaza\AutoRelated\Model\Config\Source\Additional $additional
     * @param \Mageplaza\AutoRelated\Model\Config\Source\AddProductTypes $addProductTypes
     * @param \Mageplaza\AutoRelated\Helper\Data $helperData
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Direction $direction,
        Additional $additional,
        AddProductTypes $addProductTypes,
        Data $helperData,
        Http $request,
        array $data = []
    )
    {
        $this->additional      = $additional;
        $this->direction       = $direction;
        $this->addProductTypes = $addProductTypes;
        $this->helperData      = $helperData;
        $this->request         = $request;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $model     = $this->_coreRegistry->registry('autorelated_rule');
        $ruleModel = $this->_coreRegistry->registry('autoRelated_type_product');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('block_config_rule_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Block Configuration')]);

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField('block_name', 'text', [
                'name'  => 'block_name',
                'label' => __('Block name'),
                'title' => __('Block name'),
                'note'  => __('Enter the block\'s name. It\'s only visible in the frontend.')
            ]
        );

        $fieldset->addField('product_layout', 'select', [
                'name'    => 'product_layout',
                'label'   => __('Product layout'),
                'title'   => __('Product layout'),
                'options' => [
                    '1' => __('Grid'),
                    '0' => __('Slider')
                ],
                'note'    => __('Select how products are displayed.')
            ]
        );

        $fieldset->addField('limit_number', 'text', [
                'name'  => 'limit_number',
                'label' => __('Limit number of products'),
                'title' => __('Limit number of products')
            ]
        );

        $fieldset->addField('display_out_of_stock', 'select', [
                'name'    => 'display_out_of_stock',
                'label'   => __('Display "Out-of-stock" products'),
                'title'   => __('Display "Out-of-stock" products'),
                'options' => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ]
            ]
        );

        $fieldset->addField('sort_order_direction', 'select', [
                'name'   => 'sort_order_direction',
                'label'  => __('Product order'),
                'title'  => __('Product order'),
                'values' => $this->direction->toOptionArray()
            ]
        );

        $fieldset->addField('display_additional', 'multiselect', [
                'name'   => 'display_additional',
                'label'  => __('Display additional information'),
                'title'  => __('Display additional information'),
                'values' => $this->additional->toOptionArray(),
                'note'   => __('Select information or button(s) to display with products.')
            ]
        );
        if ($displayData = $model->getDisplayAdditional()) {
            $model->setData('display_additional', $this->helperData->unserialize($displayData));
        }

        if ($this->request->getParam('type') == 'product') {
            $fieldset->addField('add_ruc_product', 'multiselect', [
                    'name'   => 'add_ruc_product',
                    'label'  => __('Add Products'),
                    'title'  => __('Add Products'),
                    'values' => $this->addProductTypes->toOptionArray(),
                    'note'   => __('Select to add Related, Up-Sell, Cross-Sell Products to the related product list')
                ]
            );
            if ($rucData = $ruleModel->getAddRucProduct()) {
                $model->setData('add_ruc_product', $this->helperData->unserialize($rucData));
            }
        }

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
