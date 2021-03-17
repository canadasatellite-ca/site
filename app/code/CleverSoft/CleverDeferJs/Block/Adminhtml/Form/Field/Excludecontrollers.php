<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CleverSoft\CleverDeferJs\Block\Adminhtml\Form\Field;

class Excludecontrollers extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('exclude_controllers', ['label' => __('Matched Expression')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
