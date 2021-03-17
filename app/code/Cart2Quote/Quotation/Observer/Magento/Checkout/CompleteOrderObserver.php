<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Observer\Magento\Checkout;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CompleteOrderObserver
 *
 * @package Cart2Quote\Quotation\Observer\Magento\Checkout
 */
class CompleteOrderObserver implements ObserverInterface
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * ConfirmModeObserver constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Remove confirmation mode from session when completing order
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $activeMode = $this->helper->getActiveConfirmMode();
        if ($activeMode) {
            $this->helper->setActiveConfirmMode(false);
        }
    }
}
