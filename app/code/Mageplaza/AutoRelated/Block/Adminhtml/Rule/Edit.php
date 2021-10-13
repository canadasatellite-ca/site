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

namespace Mageplaza\AutoRelated\Block\Adminhtml\Rule;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Mageplaza\AutoRelated\Block\Adminhtml\Rule
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->urlBuilder    = $context->getUrlBuilder();

        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Apply" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Mageplaza_AutoRelated';
        $this->_controller = 'adminhtml_rule';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class'          => 'save',
                'label'          => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            20
        );

        $this->buttonList->update('back', 'onclick', "setLocation('" . $this->getBackUrl() . "')");

        if ($this->getRuleId() && !$this->isTesting()) {
            $this->buttonList->add(
                'add_testing',
                [
                    'class'    => 'save',
                    'label'    => __('Add A/B Testing'),
                    'on_click' => sprintf("location.href = '%s';", $this->getAddTestUrl()),
                ],
                10
            );
        }

        if ($this->getRuleId() && $this->getParent()) {
            $this->buttonList->add(
                'back_parent',
                [
                    'label'    => __('Parent'),
                    'on_click' => sprintf("location.href = '%s';", $this->getBackParentUrl()),
                ],
                10
            );
        }
    }

    /**
     * Get URL for back parent button
     *
     * @return string
     */
    function getBackParentUrl()
    {
        $parentId = $this->getParent();
        $type     = $this->getBlockType();

        return $this->urlBuilder->getUrl('*/*/edit', ['id' => $parentId, 'type' => $type]);
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    function getHeaderText()
    {
        $rule = $this->getRule();
        if ($rule->getRuleId()) {
            return __("Edit Rule '%1'", $this->escapeHtml($rule->getName()));
        } else {
            return __('New Rule');
        }
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    function getBackUrl()
    {
        if ($this->_coreRegistry->registry('autorelated_test_add')) {
            $ruleId = $this->getRuleId();
            $type   = $this->getBlockType();

            return $this->urlBuilder->getUrl('*/*/edit', ['id' => $ruleId, 'type' => $type]);
        }

        return $this->urlBuilder->getUrl('*/*/');
    }

    /**
     * Check Rule
     *
     * @return boolean
     */
    private function isTesting()
    {
        $testAdd         = $this->_coreRegistry->registry('autorelated_test_add');
        $autoRelatedRule = $this->getRule();
        if (!$autoRelatedRule) {
            return false;
        }
        if ($testAdd || $autoRelatedRule->getParentId() || $autoRelatedRule->hasChild()) {
            return true;
        }

        return false;
    }

    /**
     * Get Url Add A/B Testing
     *
     * @return string
     */
    private function getAddTestUrl()
    {
        $rule = $this->getRule();
        if ($rule) {
            return $this->urlBuilder->getUrl(
                'autorelated/rule/test',
                [
                    'id'   => $rule->getRuleId(),
                    'type' => $this->getBlockType()
                ]
            );
        }
    }

    /**
     * Return the current Rule Id.
     *
     * @return int|null
     */
    private function getRuleId()
    {
        $autoRelatedRule = $this->getRule();

        return $autoRelatedRule ? $autoRelatedRule->getId() : null;
    }

    /**
     * Return the block type Rule.
     *
     * @return string|null
     */
    private function getBlockType()
    {
        $autoRelatedRule = $this->getRule();

        return $autoRelatedRule ? $autoRelatedRule->getBlockType() : null;
    }

    /**
     *
     * @return object
     */
    private function getRule()
    {
        return $this->_coreRegistry->registry('autorelated_rule');
    }

    /**
     *
     * @return int
     */
    private function getParent()
    {
        if ($this->getRule()) {
            return (int)$this->getRule()->getParentId();
        }
    }
}
