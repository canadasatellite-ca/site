<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class DeleteQuotationItem
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class DeleteQuotationItem extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * Quote item factory
     *
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $itemFactory;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Quotation Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;

    /**
     * DeleteQuotationItem constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $itemFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->itemFactory = $itemFactory;
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory,
            $logger
        );
    }

    /**
     * Delete quotation quote tier item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if ($this->getItemId() && $this->isCustomerQuote()) {
            try {
                $this->getQuote()->removeQuoteItem($this->getItemId())->saveQuote();
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
                $this->logger->critical($e);
            }
        }

        return $this->resultRedirectFactory->create()->setUrl(
            $this->_redirect->getRedirectUrl(
                $this->_url->getUrl('*/*')
            )
        );
    }

    /**
     * Get item id
     *
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->getRequest()->getParam('id', 0);
    }

    /**
     * Is the same customer as the quote
     *
     * @return bool
     */
    public function isCustomerQuote()
    {
        return $this->getQuote()->getCustomerId() == $this->customerSession->getCustomerId();
    }

    /**
     * Get Quotation Quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (!$this->quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            $quoteId = (int)$this->getRequest()->getParam('quote_id');
            $this->quote = $this->_quoteFactory->create();
            $this->quote->load($quoteId);
        }

        return $this->quote;
    }

    /**
     * Checks if the customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}
