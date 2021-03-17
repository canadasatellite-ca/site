<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Sales\Order\Create;

/**
 * Class Quote
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Sales\Order\Create
 */
class Quote extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote\Edit
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private $tierItemModel;

    /**
     * Quote constructor
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteEditedSender $quoteEditedSender
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
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteEditedSender $quoteEditedSender,
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
        \Magento\Framework\Json\Helper\Data $jsonDataHelper,
        \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel
    ) {
        parent::__construct(
            $quoteEditedSender,
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

        $this->tierItemModel = $tierItemModel;
    }

    /**
     * Execute (controller entrypoint)
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $quote = $this->_getSession()->getQuote();
            $quoteId = $this->_getSession()->getQuoteId();
            $quotation = $this->quoteFactory->create()->load($quoteId);
            if (!$quotation->getId()) {
                /** @var $quoteCreateModel \Cart2Quote\Quotation\Model\Quote */
                $quoteCreateModel = $this->quoteFactory->create();
                $quotation = $quoteCreateModel->create($quote)->load($quoteId);
                $this->_getSession()->clearStorage();
            } else {
                //update the exting quote

                //check if qty is changed that doesn't reflect an exiting tier item
                $quoteItems = $quotation->getAllVisibleItems();
                foreach ($quoteItems as $quoteItem) {
                    $tierItemExists = $this->tierItemModel->checkQtyExistTiers(
                        $quoteItem->getId(),
                        $quoteItem->getQty()
                    );

                    if (!$tierItemExists) {
                        $this->messageManager->addNoticeMessage(
                            __(
                                "The quantity on the product '%1' has changed, it is added as a new tier on the existing product.",
                                $quoteItem->getName()
                            )
                        );
                    }
                }

                /**
                 * The first save is needed to create tier items to the database.
                 * RecollectQuote function needs these tier items to calculate the totals.
                 * We need the second save to save the totals to database
                 */
                $quotation->save();
                $quotation->setRecollect(true);
                $quotation->recollectQuote();
                $quotation->save();
            }

            if ($this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view')) {
                $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $quoteId]);
            } else {
                $resultRedirect->setRefererUrl();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(sprintf(
                "%s: %s",
                __("Cannot convert Quote"),
                $e->getMessage()
            ));

            $resultRedirect->setRefererUrl();
        }

        return $resultRedirect;
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::actions');
    }
}
