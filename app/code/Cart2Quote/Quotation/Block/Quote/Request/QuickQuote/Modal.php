<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\QuickQuote;

/**
 * Class Modal
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\QuickQuote
 */
class Modal extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Cart2Quote\Quotation\Model\QuickQuote\ConfigProvider
     */
    private $configProvider;

    /**
     * Modal constructor.
     *
     * @param \Cart2Quote\Quotation\Model\QuickQuote\ConfigProvider $configProvider
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuickQuote\ConfigProvider $configProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * Get checkout config
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCheckoutConfig()
    {
        return $this->configProvider->getConfig();
    }
}
