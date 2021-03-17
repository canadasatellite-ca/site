<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote;


/**
 * Class MoveToCart
 *
 * @package Cart2Quote\Quotation\Model\Quote
 */
class MoveToCart
{
    use \Cart2Quote\Features\Traits\Model\Quote\MoveToCart {
        getUrl as private traitGetUrl;
        cloneQuote as private traitCloneQuote;
    }

    /**
     * @var \Cart2Quote\Quotation\Helper\StockCheck
     */
    protected $stockCheckHelper;

    /**
     * Quote Model
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quotationQuote;

    /**
     * Checkout Session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Quotation Cart
     *
     * @var \Cart2Quote\Quotation\Model\QuotationCart
     */
    protected $quotationCart;

    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * MoveToCart constructor.
     * @param \Cart2Quote\Quotation\Helper\StockCheck $stockCheckHelper
     * @param \Cart2Quote\Quotation\Model\Quote $quotationQuote
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Model\QuotationCart $quotationCart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\StockCheck $stockCheckHelper,
        \Cart2Quote\Quotation\Model\Quote $quotationQuote,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Model\QuotationCart $quotationCart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->stockCheckHelper = $stockCheckHelper;
        $this->quotationQuote = $quotationQuote;
        $this->checkoutSession = $checkoutSession;
        $this->quotationSession = $quotationSession;
        $this->quotationCart = $quotationCart;
        $this->quoteRepository = $quoteRepository;
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @param string $data
     * @return string
     */
    public function getUrl($data = '')
    {
        return $this->traitGetUrl($data);
    }

    /**
     * Clone quote model
     *
     * @return bool|\Magento\Quote\Model\Quote
     * @throws \Exception
     */
    public function cloneQuote()
    {
        return $this->traitCloneQuote();
    }
}
