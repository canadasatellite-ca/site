<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Report\Filter;

/**
 * Class Form
 * @package Cart2Quote\Quotation\Block\Adminhtml\Report\Filter
 */
class Form extends \Magento\Reports\Block\Adminhtml\Filter\Form
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Config 
     */
    protected $quoteConfig;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig,
        array $data = []
    ) {
        $this->quoteConfig = $quoteConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fields to base fieldset which are general to quote reports
     *
     * @return $this|\Magento\Reports\Block\Adminhtml\Filter\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && ($fieldset instanceof \Magento\Framework\Data\Form\Element\Fieldset)) {
            $statuses = $this->quoteConfig->getStatuses();
            $values = [];
            foreach ($statuses as $code => $label) {
                $values[] = ['label' => __($label), 'value' => $code];
            }

            $fieldset->addField(
                'show_quote_statuses',
                'select',
                [
                    'name' => 'show_quote_statuses',
                    'label' => __('Quote Status'),
                    'options' => ['0' => __('Any'), '1' => __('Specified')]
                ],
                'to'
            );

            $fieldset->addField(
                'quote_statuses',
                'multiselect',
                [
                    'name' => 'quote_statuses',
                    'label' => '',
                    'values' => $values,
                    'display' => 'none'
                ],
                'show_quote_statuses'
            );

            if ($this->getFieldVisibility('show_quote_statuses') && $this->getFieldVisibility('quote_statuses')) {
                $this->setChild(
                    'form_after',
                    $this->getLayout()->createBlock(
                        \Magento\Backend\Block\Widget\Form\Element\Dependence::class
                    )->addFieldMap(
                        "{$htmlIdPrefix}show_quote_statuses",
                        'show_quote_statuses'
                    )->addFieldMap(
                        "{$htmlIdPrefix}quote_statuses",
                        'quote_statuses'
                    )->addFieldDependence(
                        'quote_statuses',
                        'show_quote_statuses',
                        '1'
                    )
                );
            }
        }

        return $this;
    }
}
