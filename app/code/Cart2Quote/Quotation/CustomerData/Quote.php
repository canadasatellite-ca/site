<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\CustomerData;

use Magento\Framework\Stdlib\DateTime;

/**
 * Quote source
 */
class Quote extends \Magento\Checkout\CustomerData\Cart
{
    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Quote|null
     */
    protected $quote = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Quote constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Cart2Quote\Quotation\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Cart2Quote\Quotation\Model\QuotationCart $checkoutCart
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        DateTime $dateTime,
        \Cart2Quote\Quotation\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Cart2Quote\Quotation\Model\QuotationCart $checkoutCart,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $checkoutSession,
            $catalogUrl,
            $checkoutCart,
            $checkoutHelper,
            $itemPoolInterface,
            $layout,
            $data
        );
        $this->dateTime = $dateTime;
        $this->quotationSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get section data
     */
    public function getSectionData()
    {
        $totals = $this->getQuote()->getTotals();

        return [
            'summary_count' => $this->getSummaryCount(),
            'subtotal' => isset($totals['subtotal'])
                ? $this->checkoutHelper->formatPrice($totals['subtotal']->getValue())
                : 0,
            'possible_onepage_checkout' => false,
            'quote_id' => $this->getQuote()->getId(),
            'last_update' => $this->dateTime->gmDate('Y-m-d H:i:s', (new \DateTime())->getTimestamp()),
            'items' => $this->getRecentItems(),
            'extra_actions' => $this->layout->createBlock(\Magento\Catalog\Block\ShortcutButtons::class)->toHtml(),
            'isGuestCheckoutAllowed' => $this->isGuestCheckoutAllowed(),
            'website_id' => $this->getQuote()->getStore()->getWebsiteId(),
        ];
    }

    /**
     * Get active quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->quotationSession->getQuote();
        }

        return $this->quote;
    }

    /**
     * Get quote items qty
     *
     * @return int|float
     */
    protected function getSummaryCount()
    {
        $useQty = $this->scopeConfig->getValue(
            'checkout/cart_link/use_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($useQty) {
            $return = $this->getQuote()->getItemsQty() * 1;
        } else {
            $return = $this->getQuote()->getItemsCount() * 1;
        }

        return $return;
    }
}
