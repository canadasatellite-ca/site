<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Send
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Send extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender
     */
    protected $quoteProposalSender;

    /**
     * Send constructor
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helperData
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloningHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Framework\Json\Helper\Data $jsonDataHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Helper\Data $helperData,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cart2Quote\Quotation\Helper\Cloning $cloningHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Framework\Json\Helper\Data $jsonDataHelper
    ) {
        parent::__construct(
            $customerRepositoryInterface,
            $store,
            $escaper,
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $helperData,
            $quoteFactory,
            $statusCollection,
            $quoteCreate,
            $scopeConfig,
            $cloningHelper,
            $logger,
            $backendQuoteSession,
            $productHelper,
            $giftMessageSave,
            $jsonDataHelper
        );

        $this->quoteProposalSender = $quoteProposalSender;
    }

    /**
     * Saving quote quotation
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        /** @var \Cart2Quote\Quotation\Model\Quote $quotation */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_initQuote();
            $this->_processActionData('save');

            //done
            $this->_getSession()->clearStorage();
            $this->messageManager->addSuccessMessage(__('You updated the quote.'));

            $quote = $this->getCurrentQuote();

            //check for quotes with empty empty email address
            if (!$quote->getCustomerEmail()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We can\'t send an email to a quote without an email address.')
                );
            }

            $quote->setProposalSent((new \DateTime())->getTimestamp());
            $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_PENDING);
            $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT);
            $emailSendingResult = $this->quoteProposalSender->send($quote);

            //->send already does a save
            //$quote->save();

            //show email sending result message
            if ($emailSendingResult !== false) {
                $this->messageManager->addSuccessMessage(__('Proposal email has been sent'));
            } else {
                $this->messageManager->addErrorMessage(__('Proposal email has not been sent, check the logs for more information.'));
            }

            $this->_reloadQuote();

            if ($this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view')) {
                $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
            } else {
                $resultRedirect->setPath('quotation/quote/index');
            }
        } catch (\Magento\Framework\Exception\PaymentException $e) {
            $this->getCurrentQuote()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addErrorMessage($message);
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        }

        return $resultRedirect;
    }
}
