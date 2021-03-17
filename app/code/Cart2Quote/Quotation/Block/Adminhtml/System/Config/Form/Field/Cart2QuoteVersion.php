<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Cart2QuoteVersion
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class Cart2QuoteVersion extends Template
{
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function applyElementConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setValue(__('%1 (Installed)', $this->metadata->getCart2QuoteVersion()));

        parent::applyElementConfig($element);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function afterGetElementHtml()
    {
        $html = parent::afterGetElementHtml();
        $html .= $this->getLayout()->createBlock(
            \Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\UpdateAvailable::class,
            'license_update_available_version',
            [
                'data' => [
                    'jsLayout' => [
                        'components' => [
                            'license_update_available_version' => [
                                'component' => 'Cart2Quote_Quotation/js/system/config/license/update/version',
                                'config' => [
                                    'expiredUrl' => $this->getCart2QuoteUpgradeUrl(),
                                    'updateUrl' => $this->getCart2QuoteUpdateUrl(),
                                    'isUpdateAllowed' => $this->isUpdateAllowed(),
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        )->toHtml();
        $html .= '</div>';

        return $html;
    }

    /**
     * @return bool
     */
    protected function isUpdateAllowed()
    {
        $expiredStates = [
            \Cart2Quote\Quotation\Helper\Data\License::VALID_LICENSE,
            \Cart2Quote\Quotation\Helper\Data\License::VALID_SUBSCRIPTION,
            \Cart2Quote\Quotation\Helper\Data\License::VALID_TRIAL
        ];

        return \in_array($this->license->getSimplifiedLicenseState(), $expiredStates);
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