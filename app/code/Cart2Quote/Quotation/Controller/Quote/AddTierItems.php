<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class AddTierItems
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class AddTierItems extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    private $tierItemModel;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $quoteSession;

    /**
     * AddTierItems constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Model\Quote\TierItem $tierItemModel,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->quoteSession = $quoteSession;
        $this->tierItemModel = $tierItemModel;
        parent::__construct($context);
    }

    /**
     * Update quote item tiers frontend ajax action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response = null;
        $postData = $this->getRequest()->getPostValue();
        $quote = $this->quoteSession->getQuote();
        $item = $quote->getItemById($postData['item_id']);

        try {
            $itemQty = $item->getQty();
            $newTierQty = $postData['qty'];

            if ($itemQty == $newTierQty) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Quantity already exist'));
            }
            $item->setQty($newTierQty);
            //CollectTotals is needed so that the tier item price will be correct.
            $quote->collectTotals();
            if (!array_key_exists('tier_id', $postData)) {
                $tierItem = $this->tierItemModel->addNewTierItem($item);
                $response = ['error' => false, 'tier_id' => $tierItem->getId()];
                $this->messageManager->addSuccessMessage(__('Tier quantity added'));
            } else {
                $this->tierItemModel->editExistingTierItem($item, $postData);
                $response = ['error' => false];
                $this->messageManager->addSuccessMessage(__('Tier quantity updated'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true];
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultJson->setData($response);
    }
}
