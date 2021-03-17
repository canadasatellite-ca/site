<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class OrderId
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class OrderId extends \Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field\Template
{
    /**
     * @var array
     */
    protected $elementConfigFields = [
        'placeholder'
    ];

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        return $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'check_license',
                'label' => __('Validate License'),
                'class' => 'scalable button-container',
                'data_attribute' => [
                    'mage-init' => [
                        'Cart2Quote_Quotation/js/system/config/license/check/button' => [
                            'url' => $this->getUrl('quotation/system_config_license/check')
                        ]
                    ],
                ]
            ]
        )->toHtml();
    }

    /**
     * @return string
     */
    protected function beforeGetElementHtml()
    {
        $html = parent::beforeGetElementHtml();
        $html .= '<div class="value-container clearfix">';

        return $html;
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function applyElementConfig(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!$element->getValue()) {
            $element->setValue($this->getValue());
        }
        parent::applyElementConfig($element);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    private function getValue()
    {
        $orderId = $this->license->getOrderId();

        return $orderId;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!\class_exists(\Cart2Quote\License\Http\Client::class)) {
            return '';
        }

        return parent::render($element);
    }
}