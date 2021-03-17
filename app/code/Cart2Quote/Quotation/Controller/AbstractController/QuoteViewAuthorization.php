<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\AbstractController;

/**
 * Class QuoteViewAuthorization
 *
 * @package Cart2Quote\Quotation\Controller\AbstractController
 */
class QuoteViewAuthorization implements QuoteViewAuthorizationInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $quoteConfig;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
    ) {
        $this->customerSession = $customerSession;
        $this->quoteConfig = $quoteConfig;
    }

    /**
     * Check if quote can be viewed by user
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function canView(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $customerId = $this->customerSession->getCustomerId();
        $availableStatuses = $this->quoteConfig->getVisibleOnFrontStatuses();
        if ($quote->getId()
            && $quote->getCustomerId()
            && $quote->getCustomerId() == $customerId
            && in_array($quote->getStatus(), $availableStatuses, true)
        ) {
            return true;
        }
        return false;
    }
}
