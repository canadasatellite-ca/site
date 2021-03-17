<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Ui\Component\Control;

/**
 * Class DuplicateSplitButton
 * @package Cart2Quote\Form\Ui\Component\Control
 */
class DuplicateSplitButton extends \Magento\Ui\Component\Control\SplitButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Duplicate Quote'),
            'onclick' => 'quote.duplicate(" ' . $this->getDuplicateUrl() . ' ");',
            'class_name' => \Magento\Ui\Component\Control\Container::SPLIT_BUTTON,
            'options' => $this->getOptions()
        ];
    }

    /**
     * @return array
     */
    private function getOptions()
    {
        return [
            [
                'label' => __('Duplicate quote (assign customer)'),
                'class' => 'duplicate-assign',
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [
                                [
                                    'targetName' => 'customer_select_form.customer_select_form.select_customer_modal',
                                    'actionName' => 'toggleModal'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'label' => __('Duplicate Quote'),
                'class' => 'duplicate',
                'onclick' => 'quote.duplicate(" ' . $this->getDuplicateUrl() . ' ");',
            ]
        ];
    }

    /**
     * Duplicate URL getter
     *
     * @return string
     */
    public function getDuplicateUrl()
    {
        return $this->getUrl('quotation/quote/duplicate');
    }
}
