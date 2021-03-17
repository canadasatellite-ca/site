<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Cart2QuoteEdition
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class Cart2QuoteEdition extends Template
{
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        if ($this->license->isUnreachable() || $this->license->isMpVersion()) {
            return '';
        }

        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'compare_editions',
                'label' => __('Compare editions'),
                'class' => 'scalable secondary button-container',
                'onclick' => \sprintf('window.open("%s")', $this->getCart2quoteComparisonUrl())
            ]
        )->toHtml();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->license->getSimplifiedLicenseState() == \Cart2Quote\Quotation\Helper\Data\License::PENDING_LICENSE) {
            return '';
        }

        return parent::render($element);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function afterGetElementHtml()
    {
        $html = parent::afterGetElementHtml();
        $html .= '</div>';
        $html .= $this->getButtonHtml();

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

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function applyElementConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setValue(\ucfirst(__($this->license->getEdition())));
        parent::applyElementConfig($element);
    }
}
