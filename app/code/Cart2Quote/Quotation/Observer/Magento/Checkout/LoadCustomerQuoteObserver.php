<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Magento\Checkout;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadCustomerQuoteObserver
 *
 * @package Cart2Quote\Quotation\Observer\Magento\Checkout
 */
class LoadCustomerQuoteObserver implements ObserverInterface
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Message Manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * LoadCustomerQuoteObserver constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteSession = $quoteSession;
        $this->messageManager = $messageManager;
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * Load the quote on the checkout session and on the quote session.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->quotationHelper->isFrontendEnabled()) {
                $this->quoteSession->loadCustomerQuote();
                $this->quoteSession->loadProductComments();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Load customer quote error'));
        }
    }
}
