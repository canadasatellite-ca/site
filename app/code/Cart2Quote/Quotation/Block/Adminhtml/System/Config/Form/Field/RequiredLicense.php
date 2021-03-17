<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class RequiredLicense
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class RequiredLicense extends \Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Template
{
    /**
     * Check if the current edition is should allow this feature
     *
     * @return bool
     */
    public function isAllowedForEdition()
    {
        return false;
    }

    /**
     * Get element HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->license->isUnreachable() || $this->isAllowedForEdition() && !$this->license->isTrial()) {
            return '';
        }

        return $this->_toHtml();
    }
}
