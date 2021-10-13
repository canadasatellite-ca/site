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

namespace Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Test;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class Test
 * @package Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Test
 */
class Test extends Generic implements TabInterface
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'rule/tab/test.phtml';

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    function getTabLabel()
    {
        return __('A/B Testing');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    function getTabTitle()
    {
        return __('A/B Testing');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    function isHidden()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    function initForm()
    {
        $form = $this->_formFactory->create();
        $form->addFieldset('test_base_fieldset', ['legend' => __('A/B Testing')]);
        $this->setForm($form);

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Mageplaza\AutoRelated\Block\Adminhtml\Rule\Edit\Tab\Test\BlockList',
                'autorelated.test.blocklist.grid'
            )
        );
        parent::_prepareLayout();

        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->initForm();

        return parent::_toHtml();
    }
}
