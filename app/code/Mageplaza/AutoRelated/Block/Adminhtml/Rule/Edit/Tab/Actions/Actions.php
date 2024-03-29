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
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Actions as RuleActions;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Mageplaza\AutoRelated\Model\RuleFactory;

/**
 * Class Actions
 * @package Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Actions
 */
class Actions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Actions
     */
    protected $ruleActions;

    /**
     * @var \Mageplaza\AutoRelated\Model\RuleFactory
     */
    protected $autoRelatedRuleFactory;

    /**
     * Actions constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Actions $ruleActions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Mageplaza\AutoRelated\Model\RuleFactory $autoRelatedRuleFactory
     * @param array $data
     */
    function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleActions $ruleActions,
        Fieldset $rendererFieldset,
        RuleFactory $autoRelatedRuleFactory,
        array $data = []
    )
    {
        $this->rendererFieldset       = $rendererFieldset;
        $this->ruleActions            = $ruleActions;
        $this->autoRelatedRuleFactory = $autoRelatedRuleFactory;
        $this->formKey                = $context->getFormKey();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('autorelated_rule');
        $form  = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of actions tab to supplied form.
     *
     * @param \Mageplaza\AutoRelated\Model\Rule $model
     * @param string $fieldsetId
     * @param string $formName
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'actions_fieldset', $formName = 'autorelated_rule_form')
    {
        $id = $this->getRequest()->getParam('id');
        if (!$model) {
            $model = $this->autoRelatedRuleFactory->create();
            $model->load($id);
        }

        $actionsFieldSetId = $model->getActionsFieldSetId($formName);

        $newChildUrl = $this->getUrl(
            'autorelated/condition/newActionHtml/form/' . $actionsFieldSetId,
            ['form_namespace' => $formName]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $renderer = $this->rendererFieldset->setTemplate(
            'Mageplaza_AutoRelated::rule/fieldset.phtml'
        )->setNewChildUrl(
            $newChildUrl
        )->setFieldSetId(
            $actionsFieldSetId
        )->setAjaxUrl($this->getUrl('autorelated/grid/productlist', ['id' => $id, 'type' => 'act', 'form_key' => $this->formKey->getFormKey()]));

        $fieldset = $form->addFieldset($fieldsetId, [
                'legend' => __('Apply the rule only to products matching the following conditions (leave blank for all products).')
            ]
        )->setRenderer($renderer);

        $fieldset->addField('actions', 'text', [
                'name'           => 'apply_to',
                'label'          => __('Apply To'),
                'title'          => __('Apply To'),
                'required'       => true,
                'data-form-part' => $formName
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->ruleActions
        );

        $form->setValues($model->getData());
        $model->getActions()->setJsFormObject($actionsFieldSetId);
        $this->setActionFormName($model->getActions(), $formName);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function getTabClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function getTabUrl()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function isAjaxLoaded()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    function isHidden()
    {
        return false;
    }
}
