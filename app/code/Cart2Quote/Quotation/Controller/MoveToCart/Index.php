<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\MoveToCart;

use Magento\Framework\App\Action\Context;

/**
 * Class Index
 *
 * @package Cart2Quote\Quotation\Controller\MoveToCart
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\MoveToCart
     */
    protected $moveToCart;

    /**
     * Checkout Session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * Index constructor.
     * @param \Cart2Quote\Quotation\Model\Quote\MoveToCart $moveToCart
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\MoveToCart $moveToCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        Context $context
    ) {
        $this->moveToCart = $moveToCart;
        $this->checkoutSession = $checkoutSession;
        $this->quotationSession = $quotationSession;
        parent::__construct($context);
    }

    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $copiedQuote = $this->moveToCart->cloneQuote();
            $this->messageManager->addSuccessMessage(__('Your quote items have successfully been moved to your shopping cart.'));
            $this->quotationSession->clearQuote();
            $this->quotationSession->clearStorage();
            $this->checkoutSession->setQuoteId($copiedQuote->getId());
            $resultRedirect->setPath('checkout/cart/index');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        return $resultRedirect;
    }
}
