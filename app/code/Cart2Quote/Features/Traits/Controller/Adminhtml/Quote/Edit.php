<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Adminhtml\Quote;
/**
 * Trait Edit
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
trait Edit
{
    /**
     * Cancel original quotation and create new quotation
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    private function execute()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($results = parent::execute()) {
            return $results;
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        /** @var \Cart2Quote\Quotation\Model\Quote $quotation */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            //Cancel Original Quote
            $originalQuote = $this->quoteFactory->create()->load($this->getRequest()->getPost('quote_id'));
            $originalQuote->setData('state', \Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
            $originalQuote->setData('status', \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED);
            $originalQuote->save();
            if ($this->quoteEditedSender->send($originalQuote)) {
                $this->messageManager->addSuccessMessage(__('The customer is notified'));
            }
            //Create New Quote
            $newQuote = $this->cloningHelper->cloneQuote($originalQuote);
            $newQuote->setData('state', \Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN);
            $newQuote->setData('status', \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN);
            $newIncrementId = $this->getNewIncrementId(
                $this->getRequest()->getPost('increment_id'),
                $originalQuote->getStoreId()
            );
            $newQuote->setData('increment_id', $newIncrementId);
            $newQuote->save();
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $newQuote->getId()]);
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
    /**
     * Get increment id for created new
     *
     * @param $incrementId
     * @param int $storeId
     * @return string
     */
    private function getNewIncrementId($incrementId, $storeId = 0)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$prefix = $this->helperData->getQuotePrefix($storeId);
        //remove the prefix from the increment id
        $prefixPosition = strpos($incrementId, $prefix);
        if ($prefixPosition !== false && $prefixPosition === 0) {
            $incrementId = substr_replace($incrementId, '', $prefixPosition, strlen($prefix));
        }
        //get the original increment id without the edit count
        $splitIncrementId = explode('-', $incrementId);
        if (is_array($splitIncrementId) && (count($splitIncrementId) > 1)) {
            //remove only last element form increment id
            array_pop($splitIncrementId);
            $parentIncrementId = implode('-', $splitIncrementId);
        } else {
            $parentIncrementId = $incrementId;
        }
        //add the prefix again
        $parentIncrementId = $prefix . $parentIncrementId;
        //find all quotes with the same prefix
        $quoteCollection = $this->quoteFactory->create()->getCollection();
        $quoteCollection = $quoteCollection
            ->addFieldToSelect('*')
            ->addFieldToFilter(
                'main_table.increment_id',
                ['like' => '%' . $parentIncrementId . '%']
            );
        $quoteCollectionCount = $quoteCollection->getSize();
        //add the edit counter to the increment id
        if ($quoteCollectionCount) {
            return $parentIncrementId . '-' . $quoteCollectionCount;
        }
        return $parentIncrementId;
		}
	}
}
