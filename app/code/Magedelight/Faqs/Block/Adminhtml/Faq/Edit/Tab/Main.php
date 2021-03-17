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

namespace Magedelight\Faqs\Block\Adminhtml\Faq\Edit\Tab;

use Magento\Customer\Model\CustomerFactory as CustomerFactory;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $systemStore;
    public $status;
    public $questiontype;
    public $create;
    public $customerFactory;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    public $category;
    
    public $fieldFactory;
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    public $wysiwygConfig;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magedelight\Faqs\Model\Source\Faq\Status $status,
        \Magedelight\Faqs\Model\Source\Faq\Questiontype $questiontype,
        \Magedelight\Faqs\Model\Source\Faq\Created $create,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Magedelight\Faqs\Model\Source\Category\Category $category,
        CustomerFactory $customerfactory,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->status = $status;
        $this->questiontype = $questiontype;
        $this->create = $create;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->customerFactory = $customerfactory;
        $this->category = $category;
        $this->fieldFactory = $fieldFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    // @codingStandardsIgnoreStart
    protected function _prepareForm()
    {
        
        $model = $this->_coreRegistry->registry('magedelight_faqs');
        /*
         * Checking if user have permissions to save information
        */
        if ($this->_isAllowedAction('Magedelight_Faqs::faq')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('faq_');
 
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('FAQ Question Information'), 'class' => 'fieldset-wide']
        );
 
        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
 
        $fieldset->addField(
            'question',
            'textarea',
            [
                'name' => 'question',
                'style' => 'height:10em;',
                'label' => __('Question'),
                'title' => __('Question'),
                'required' => true
            ]
        );
        if (!$model->getId()) {
            $model->setCreatedBy('1'); // created_by admin  when adding a Faq
        }
        
        $createstatus = $this->create->toOptionArray();
        
        if ($model->getCreatedBy() == \Magedelight\Faqs\Model\Faq::LOGIN_CUSTOMER) {
            $customerObj = $this->customerFactory->create()->load($model->getCustomerId());
            $custmer_url = $this->getUrl('customer/index/edit', ['id' => $customerObj->getId()]);
            $customer_info = '<div style="margin:-4px 0 0; padding:0 0 0 6px;background:#ffffff;">Customer Name: '
                    . '<span class="my-tooltip">'
                    . '<a style="cursor: pointer;" '
                    . 'href='.$custmer_url.' class="tooltip-toggle">' . $customerObj->getName() . '</a>'
                    . '<span class="tooltip-content">'
                    . '<p>Name: ' . $customerObj->getName() . '</p>'
                    . '<p>Email: ' . $customerObj->getEmail() . '</p>'
                    . '</span>'
                    . '</span>'
                    . '</div>';
        } elseif ($model->getCreatedBy() == \Magedelight\Faqs\Model\Faq::GUEST_CUSTOMER) {
            $customer_info = '<div style="margin:-4px 0 0; padding:0 0 0 6px;background:#ffffff;">Guest Name: '
                    . '<span class="my-tooltip">'
                    . '<a style="cursor: pointer;" href="#" class="tooltip-toggle">' . $model->getGuestName() . '</a>'
                    . '<span class="tooltip-content">'
                    . '<p>Name: ' . $model->getGuestName() . '</p>'
                    . '<p>Email: ' . $model->getGuestEmail() . '</p>'
                    . '</span>'
                    . '</span>'
                    . '</div>';
        } else {
            $customer_info = '';
        }
        $fieldset->addField(
            'created_by',
            'select',
            [
                'name' => 'created_by',
                'label' => __('Created By'),
                'title' => __('Created By'),
                'values' => $createstatus ,
                'required' => false ,
                'disabled'=>true,
                'style'=> "vertical-align:top;",
                'after_element_html' => $customer_info]
        );
        
        if ($model->getCreatedBy() == \Magedelight\Faqs\Model\Faq::LOGIN_CUSTOMER
            || $model->getCreatedBy() == \Magedelight\Faqs\Model\Faq::GUEST_CUSTOMER) {
            $fieldset->addField(
                'email_send',
                'checkbox',
                [
                    'name' => 'email_send' ,
                    'header_css_class'  => 'a-center',
                    'label' => __('Notify by Email'),
                    'title' => __('Notify Customer by Email'),
                    'onclick' => 'this.value = this.checked ? 1 : 0;' ,
                    'tabindex' => 1 ,
                    'after_element_html' => '<small>Yon can notify customer by email. </small>'
                ]
            );
        }
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
        $fieldset->addField(
            'answer',
            'editor',
            [
            'name' => 'answer',
            'label' => __('Answer'),
            'title' => __('Answer'),
            'config' => $this->wysiwygConfig->getConfig()
            ]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true
            ]
        );
        // Status - Dropdown
        if (!$model->getId()) {
            $model->setIsActive('1'); // Enable status when adding a Faq
        }
        $statuses = $this->status->toOptionArray();
        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $statuses
            ]
        );
        $questiontypes = $this->questiontype->toOptionArray();
        $question_type=  $fieldset->addField(
            'question_type',
            'select',
            [
                'name' => 'question_type',
                'label' => __('Question Type'),
                'title' => __('Question Type'),
                'required' => true,
                'values' => $questiontypes
            ]
        );
         // Category - Dropdown
        $categories = $this->category->toOptionArray();
        $another_field=  $fieldset->addField(
            'category_id',
            'select',
            [
                'name' => 'category_id',
                'label' => __('Category'),
                'required' => true,
                'title' => __('Category'),
                'values' => $categories
            ]
        );
        
        $refField = $this->fieldFactory->create(
            ['fieldData' => ['value' => '2,3', 'separator' => ','], 'fieldPrefix' => '']
        );
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($question_type->getHtmlId(), $question_type->getName())
            ->addFieldMap($another_field->getHtmlId(), $another_field->getName())
            ->addFieldDependence($another_field->getName(), $question_type->getName(), $refField)
        );
        
        $form->setValues($model->getData());
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    // @codingStandardsIgnoreEnd
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Question Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Question Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    // @codingStandardsIgnoreStart
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    // @codingStandardsIgnoreEnd
}
