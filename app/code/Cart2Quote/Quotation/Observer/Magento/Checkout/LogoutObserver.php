<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Magento\Checkout;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class ConfirmModeObserver
 *
 * @package Cart2Quote\Quotation\Observer\Magento\Checkout
 */
class LogoutObserver implements ObserverInterface
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationDataHelper;

    /**
     * Checkout session
     *
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Quote Repository
     *
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Cart2Quote\Quotation\Helper\Cart
     */
    protected $cartHelper;

    /**
     * LogoutObserver constructor
     *
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     * @param \Cart2Quote\Quotation\Helper\Cart $cartHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Cart2Quote\Quotation\Helper\Cart $cartHelper
    ) {
        $this->quotationDataHelper = $quotationDataHelper;
        $this->cartHelper = $cartHelper;
    }

    /**
     * Remove confirmation mode from session
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $activeMode = $this->quotationDataHelper->getActiveConfirmMode();
        if ($activeMode) {
            $this->quotationDataHelper->setActiveConfirmMode(false);
            $this->cartHelper->clearCart();
        }
    }
}
