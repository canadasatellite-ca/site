<?php

namespace MW\Onestepcheckout\Block\Adminhtml\System\Config\Form\Field;

class Addtimerange extends \Magento\Config\Block\System\Config\Form\Field\Regexceptions
{
	public function _construct()
    {
        $this->addColumn(
            'starttime',
            [
                'label' => __('Start Time'),
                'style' => 'width:120px',
            ]
        );
        $this->addColumn(
            'endtime',
            [
                'label' => __('End Time'),
                'style' => 'width:120px',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Time Range');
    }
}
