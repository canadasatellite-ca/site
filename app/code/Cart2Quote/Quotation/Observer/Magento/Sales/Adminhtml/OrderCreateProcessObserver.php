<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Magento\Sales\Adminhtml;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderCreateProcessObserver
 *
 * @package Cart2Quote\Quotation\Observer\Magento\Sales\Adminhtml
 */
class OrderCreateProcessObserver implements ObserverInterface
{
    /**
     * Quote model object
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * OrderCreateProcessObserver constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Unset guest mode and CustomerAddressId when changing customers
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $session \Magento\Backend\Model\Session\Quote */
        $session = $observer->getSession();
        if ($session->getQuotationQuoteId()
            && $session->getStoreId()
            && $session->getQuoteId()
            && $session->getCustomerId()
        ) {
            $this->quote = $this->quoteRepository->get($session->getQuoteId(), [$session->getStoreId()]);
            if ($this->quote->getCustomerIsGuest()) {
                $this->quote->setCustomerIsGuest("0");
                $this->quoteRepository->save($this->quote);
            }

            // If customer is different, unset CustomerAddressId
            if ($session->getCustomerId() != $this->quote->getCustomerId()) {
                // Billing Address processing
                $billingAddress = $this->quote->getBillingAddress();
                if ($billingAddress) {
                    if ($billingAddress->getCustomerAddressId()) {
                        $billingAddress->setCustomerAddressId(null);
                    }
                }

                // Shipping Address processing
                $shippingAddress = $this->quote->getShippingAddress();
                if ($shippingAddress) {
                    if ($shippingAddress->getCustomerAddressId()) {
                        $shippingAddress->setCustomerAddressId(null);
                    }
                }
            }
        }
    }
}
