<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class LicenseType
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class LicenseType extends Template
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->license->getSimplifiedLicenseState(
            ) == \Cart2Quote\Quotation\Helper\Data\License::PENDING_LICENSE) {
            return '';
        }

        return parent::render($element);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function applyElementConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setValue(__($this->license->getLicenseType()));
        parent::applyElementConfig($element);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function afterGetElementHtml()
    {
        $html = parent::afterGetElementHtml();
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    protected function beforeGetElementHtml()
    {
        $html = parent::beforeGetElementHtml();
        $html .= '<div class="value-container">';

        return $html;
    }
}