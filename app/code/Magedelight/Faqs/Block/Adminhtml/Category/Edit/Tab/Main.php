<?php
/* Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Faqs
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 * 
 */
namespace Magedelight\Faqs\Block\Adminhtml\Category\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public $systemStore;
    public $status;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magedelight\Faqs\Model\Source\Category\Status $status,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->status = $status;
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    // @codingStandardsIgnoreStart
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('md_faq_category');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Magedelight_Faqs::category')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('md_faq_category_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('FAQ Category Information')]);
        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }
        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'class' => 'validate-identifier',
                'note' => __('Relative to Web Site Base URL'),
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
                'class' => 'validate-zero-or-greater',
                'disabled' => $isElementDisabled
            ]
        );
         /* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        if (!$model->getId()) {
            $model->setStatus('1'); // Enable status when adding a Faq
        }
        $statuses = $this->status->toOptionArray();
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'label' => __('Meta Keywords'),
                'name' => 'meta_keywords',
                'title' => __('Meta Keywords'),
                'style' => 'height:10em;',
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'label' => __('Meta Description'),
                'name' => 'meta_description',
                'title' => __('Meta Description'),
                'style' => 'height:10em;',
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    // @codingStandardsIgnoreEnd
    public function getTabLabel()
    {
        return __('FAQ Category Information');
    }

    public function getTabTitle()
    {
        return __('FAQ Category Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
    // @codingStandardsIgnoreStart
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed('Magedelight_Faqs::category');
    }
    // @codingStandardsIgnoreEnd
}
