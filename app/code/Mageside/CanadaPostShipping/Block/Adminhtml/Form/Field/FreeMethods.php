<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block\Adminhtml\Form\Field;

class FreeMethods extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $_groupRenderer;

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    protected function _getMethodRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                \Mageside\CanadaPostShipping\Block\Adminhtml\Form\Field\Methods::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_groupRenderer->setClass('method_select');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'method',
            ['label' => __('Method'), 'renderer' => $this->_getMethodRenderer()]
        );
        $this->addColumn('threshold', ['label' => __('Threshold')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Free Method');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getMethodRenderer()->calcOptionHash($row->getData('method'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
