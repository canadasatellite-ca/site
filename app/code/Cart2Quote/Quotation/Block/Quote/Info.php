<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote;

use Cart2Quote\Quotation\Model\Quote\Address\Renderer as AddressRenderer;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateContext;
use Magento\Quote\Model\Quote\Address;

/**
 * Invoice view comments form
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'quote/info.phtml';

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Info constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Model\Quote\Address\Renderer $addressRenderer
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Registry $registry,
        AddressRenderer $addressRenderer,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        $this->coreRegistry = $registry;
        $this->addressRenderer = $addressRenderer;
        $this->quotationHelper = $quotationHelper;
        $this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * @return \Cart2Quote\Quotation\Helper\Data
     */
    public function getIsFrontendCreatedByVisible() {
        return $this->quotationHelper->isFrontendCreatedByVisible();
    }

    /**
     * Get payment info formated in HTML
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }

    /**
     * Prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Quote # %1', $this->getQuote()->getIncrementId()));
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }
}
