<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\MiniCart;

/**
 * Class Sidebar
 * @package Cart2Quote\Quotation\Model\MiniCart
 */
class Sidebar
{
    use \Cart2Quote\Features\Traits\Model\MiniCart\Sidebar {
        removeQuoteItem as private traitRemoveQuoteItem;
        updateQuoteItem as private traitUpdateQuoteItem;
        getResponseData as private traitGetResponseData;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\QuotationCart
     */
    private $cart;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $resolver;

    /**
     * Sidebar constructor.
     *
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Magento\Framework\Locale\ResolverInterface $resolver
    ) {
        $this->cart = $cart;
        $this->resolver = $resolver;
    }

    /**
     * Remove item from miniquote
     *
     * @param int $itemId
     */
    public function removeQuoteItem($itemId)
    {
        $this->traitRemoveQuoteItem($itemId);
    }

    /**
     * Update miniquote item quantity
     *
     * @param int $itemId
     * @param int $itemQty
     * @return \Magento\Quote\Model\Quote\Item|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateQuoteItem($itemId, $itemQty)
    {
        return $this->traitUpdateQuoteItem($itemId, $itemQty);
    }

    /**
     * Compile response data
     *
     * @param string $error
     * @return array
     */
    public function getResponseData($error = '')
    {
        return $this->traitGetResponseData($error);
    }
}
