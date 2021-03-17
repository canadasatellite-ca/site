<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\QuoteCheckout;

/**
 * Class Onepage
 *
 * @package Cart2Quote\Quotation\Block\Quote\QuoteCheckout
 */
class Onepage extends \Magento\Checkout\Block\Onepage
{

    /**
     * Layout Processor
     *
     * @var \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\LayoutProcessor
     */
    protected $layoutProcessor;

    /**
     * Address helper
     *
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    protected $addressHelper;

    /**
     * Onepage constructor
     *
     * @param \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\LayoutProcessor $layoutProcessor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Cart2Quote\Quotation\Model\Quote\CompositeConfigProvider $configProvider
     * @param \Cart2Quote\Quotation\Helper\Address $addressHelper
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\LayoutProcessor $layoutProcessor,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Cart2Quote\Quotation\Model\Quote\CompositeConfigProvider $configProvider,
        \Cart2Quote\Quotation\Helper\Address $addressHelper,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->layoutProcessor = $layoutProcessor;
        $this->addressHelper = $addressHelper;
        parent::__construct($context, $formKey, $configProvider, $layoutProcessors, $data);
    }

    /**
     * Get the JS layout
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $name => $processor) {
            if (in_array($name, $this->getAllowedLayoutProcessors())) {
                $this->jsLayout = $processor->process($this->jsLayout);
            }
        }

        $this->jsLayout = $this->layoutProcessor->process($this->jsLayout);
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Get the allowed layout processors
     * - Other layout processors are ignored.
     *
     * @return array
     */
    protected function getAllowedLayoutProcessors()
    {
        return [
            'addressFormAttributes',
            'directoryData'
        ];
    }

    /**
     * Get allowed to use form
     *
     * @return bool
     */
    public function getEnableForm()
    {
        return $this->addressHelper->getEnableForm();
    }
}
