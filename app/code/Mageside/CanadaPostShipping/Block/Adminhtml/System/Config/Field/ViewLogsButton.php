<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */

namespace Mageside\CanadaPostShipping\Block\Adminhtml\System\Config\Field;

class ViewLogsButton extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'system/config/button/button-view-logs.phtml';

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->addData(
            [
                'id'            => 'view_logs',
                'button_label'  => __('View'),
                'url'           => $this->getUrl('canadapost/log/index'),
            ]
        );
        return $this->_toHtml();
    }
}
