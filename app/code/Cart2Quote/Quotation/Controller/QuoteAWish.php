<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller;

/**
 * Class QuoteAWish
 *
 * @package Cart2Quote\Quotation\Controller
 */
abstract class QuoteAWish extends \Magento\Framework\App\Action\Action
{
    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * @var \Magento\Wishlist\Model\Wishlist $wishlist
     */
    protected $wishlist;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory
     */
    protected $resultFactoryRedirect;

    /**
     * QuoteAWish constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Magento\Wishlist\Model\Wishlist $wishlist,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->quotationSession = $quotationSession;
        $this->wishlist = $wishlist;
        $this->customerSession = $customerSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * @return bool|\Cart2Quote\Quotation\Model\Quote
     * @throws \Exception
     */
    protected function quoteAWish()
    {
        $customerData = $this->customerSession->getCustomerData();
        $customerId = $customerData->getId();
        $wishlist = $this->wishlist->loadByCustomerId($customerId);
        $wishlistItems = $wishlist->getItemCollection()->getItems();
        $quotationQuote = $this->quotationSession->getQuote();

        /* @var \Magento\Wishlist\Model\Item $item */
        foreach ($wishlistItems as $item) {
            $catalogProduct = $item->getProduct();
            if ($catalogProduct->getTypeId() == 'bundle' && is_null($item->getBuyRequest()->getBundleOption())) {
                $this->messageManager->addErrorMessage(
                    __(
                        'Please specify product option(s) for %1 before moving this product to your quotation.',
                        $catalogProduct->getName()
                    )
                );
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
                return false;
            }
            $quotationQuote->addProduct($catalogProduct, $item->getBuyRequest());
            $item->delete();
        }

        $quotationQuote->collectTotals();
        $this->messageManager->addSuccessMessage(__('The wish list has successfully been moved to your quote.'));
        $quotationQuote->save();

        return $quotationQuote;
    }
}
