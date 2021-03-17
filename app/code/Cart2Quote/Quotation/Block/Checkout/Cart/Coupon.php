<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout\Cart;

/**
 * Class Coupon
 *
 * @package Cart2Quote\Quotation\Block\Checkout\Cart
 */
class Coupon extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $quotationHelper;

    /**
     * Coupon constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIsQuotationCouponDisabled()
    {
        //is_quotation_quote is not set on checkout quotes
        //$isQuotation = $this->_checkoutSession->getQuote()->getIsQuotationQuote();
        $isQuotation = (bool)$this->_checkoutSession->getQuote()->getLinkedQuotationId();
        $disableCoupon = $this->quotationHelper->isQuotationCouponDisabled();

        if ($disableCoupon && $isQuotation) {
            return true;
        }

        return false;
    }
}
