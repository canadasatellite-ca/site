<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Interactivated\Quotecheckout\Plugin\Checkout;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;

//
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Session
{
    protected $request;
    protected $quotationSession;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Cart2Quote\Quotation\Model\Session $quotationSession
    )
    {
        $this->request = $request;
        $this->quotationSession = $quotationSession;
    }

    /**
     * Get checkout quote instance by current session
     *
     * @return Quote
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function aroundGetQuote(\Magento\Checkout\Model\Session $session, \Closure $process)
    {
        $checkout_session = $session;
        $name = $this->request->getModuleName();

        if ($name == 'quotecheckout' && !$checkout_session->hasQuote() && !$checkout_session->getData('break_recurcive')) {
            $checkout_session->setData('break_recurcive',true);
            $quote = $this->quotationSession->getQuote();
            $checkout_session->unsetData('break_recurcive');
            return $quote;
        } else {
            return $process();
        }

    }
}
