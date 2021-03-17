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

class Design extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public $systemStore;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    
    // @codingStandardsIgnoreStart
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('md_faqs_question');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Magedelight_Faqs::faq')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('faqs_faq_form');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General')]);
        $field = $fieldset->addField(
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
        $renderer = $this->getLayout()->createBlock('Magedelight\Faqs\Block\Adminhtml\Faq\Edit\Renderer\Color');
        $field->setRenderer($renderer);
        
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
