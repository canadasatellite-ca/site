<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class UpdateTier
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
class UpdateTier extends \Cart2Quote\Quotation\Controller\AbstractController\View
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote
     */
    private $quoteResourceModel;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    private $quote;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $resultRawFactory;

    /**
     * UpdateTier constructor.
     *
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface $quoteLoader
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Framework\App\Action\Context $context,
        \Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface $quoteLoader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
        $this->quote = $quote;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context, $quoteLoader, $resultPageFactory);
    }

    /**
     * Update quote item tiers frontend ajax action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $quoteId = $this->getRequest()->getParam("quote_id");
        $this->quoteResourceModel->load($this->quote, $quoteId);
        if ($this->quote->getId()) {
            $recalculate = false;
            if (is_array($postData) && isset($postData['item_id'], $postData['tier_item_id'])) {
                $quoteItem = $this->quote->getItemById($postData['item_id']);
                $tierItemId = $postData['tier_item_id'];
                if ($quoteItem->getCurrentTierItem()->getId() != $tierItemId) {
                    $tierItemCollection = $quoteItem->getTierItems();
                    /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem */
                    $tierItem = $tierItemCollection->getItemById($tierItemId);

                    $tierItem->setItem($quoteItem);
                    $tierItem->setSelected();
                    $recalculate = true;
                }

                if ($recalculate) {
                    $this->quote->setRecollect(true)->recollectQuote()->save();
                }
            }
        }
        $response = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $layout = $response->addHandle('quotation_quote_ajax_updatetier')->getLayout();
        $block = $layout->getBlock('quote_totals');
        $block->setQuote($this->quote);
        $response = $block->toHtml();

        return $this->resultRawFactory->create()->setContents($response);
    }
}
