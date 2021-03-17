<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class ClearCart
 *
 * @package Cart2Quote\Quotation\Controller\Cart
 */
class ClearCart extends Action
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationDataHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\Cart
     */
    private $cartHelper;

    /**
     * ClearCart constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     * @param \Cart2Quote\Quotation\Helper\Cart $cartHelper
     */
    public function __construct(
        Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Cart2Quote\Quotation\Helper\Cart $cartHelper
    ) {
        parent::__construct($context);
        $this->quotationDataHelper = $quotationDataHelper;
        $this->cartHelper = $cartHelper;
    }

    /**
     * Remove confirmation mode from session
     * Clear the cart
     * Redirect to refererUrl
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $activeMode = $this->quotationDataHelper->getActiveConfirmMode();
        if ($activeMode) {
            $this->quotationDataHelper->setActiveConfirmMode(false);
        }
        $this->cartHelper->clearCart();

        return $this->goBack();
    }

    /**
     * Set redirect url to referer url
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function goBack()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}