<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\CatalogInventory\Model\Quote\Item;

/**
 * Class QuantityValidatorPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\CatalogInventory\Model\Quote\Item
 */
class QuantityValidatorPlugin
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationDataHelper;

    /**
     * QuantityValidatorPlugin constructor
     *
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     */
    public function __construct(\Cart2Quote\Quotation\Helper\Data $quotationDataHelper)
    {
        $this->quotationDataHelper = $quotationDataHelper;
    }

    /**
     * Around validate plugin
     *
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $subject
     * @param callable $proceed
     * @param \Magento\Framework\Event\Observer $observer
     * @return callable|void
     */
    public function aroundValidate(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $subject,
        callable $proceed,
        $observer
    ) {
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem ||
            !$quoteItem->getProductId() ||
            !$quoteItem->getQuote() ||
            $quoteItem->getQuote()->getIsSuperMode() ||
            ($quoteItem->getQuote()->getIsQuotationQuote() &&
                !$this->quotationDataHelper->isStockEnabledFrontend())
        ) {
            return;
        }

        return $proceed($observer);
    }
}
