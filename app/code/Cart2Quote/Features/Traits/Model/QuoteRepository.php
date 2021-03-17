<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
use Magento\Framework\Api\SortOrder;
use Magento\Quote\Model\QuoteRepository as MageQuoteRepository;
use Cart2Quote\Quotation\Api\QuoteRepositoryInterface;
/**
 * Trait QuoteRepository
 *
 * @package Cart2Quote\Quotation\Model
 */
trait QuoteRepository
{
    /**
     * Get by quote id
     *
     * @param int $quoteId
     * @param array $sharedStoreIds
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Exception
     */
    private function get($quoteId, array $sharedStoreIds = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->quotationFactory->create()->load($quoteId);
        if ($quote->getIsQuotationQuote()) {
            return $quote;
        } else {
            throw new \Exception(sprintf(__("Cart2Quote Quote Id \"%s\" does not exist."), $quoteId));
        }
		}
	}
    /**
     * Get all the quotes with search
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Quote\Api\Data\CartSearchResultsInterface
     * @throws \Exception
     */
    private function getQuotesList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteCollection = $this->quoteCollectionFactory->create();
        /** @var \Magento\Quote\Api\Data\CartSearchResultsInterface $searchData */
        $searchData = $this->searchResultsDataFactory->create();
        $searchData->setSearchCriteria($searchCriteria);
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $quoteCollection);
        }
        $searchData->setTotalCount($quoteCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $quoteCollection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'ASC' : 'DESC'
                );
            }
        }
        $quoteCollection->setCurPage($searchCriteria->getCurrentPage());
        $quoteCollection->setPageSize($searchCriteria->getPageSize());
        foreach ($quoteCollection->getItems() as $quote) {
            if ($quote->getIsQuotationQuote()) {
                $quotes[] = $quote;
            }
        }
        if (empty($quotes)) {
            $message = ["message" => __("No Requests for Quote available.")];
            return $searchData->setItems($message);
        }
        $searchData->setItems($quotes);
        $searchData->setTotalCount(count($quotes));
        return $searchData;
		}
	}
    /**
     * Save quote
     *
     * @param \Cart2Quote\Quotation\Api\Data\QuoteInterface $quote
     */
    private function saveQuote(\Cart2Quote\Quotation\Api\Data\QuoteInterface $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$quote->getId()) {
            $quote->create($this->quoteFactory->create($quote->getData()));
        } else {
            $quote->save();
        }
		}
	}
    /**
     * Delete quote
     *
     * @param int $quoteId
     * @param array $sharedStoreIds
     * @throws \Exception
     */
    private function deleteQuote($quoteId, array $sharedStoreIds)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->delete($this->get($quoteId, $sharedStoreIds));
		}
	}
    /**
     * Get items
     *
     * @param int $quoteId
     * @return array
     * @throws \Exception
     */
    private function getItems($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$output = [];
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->get($quoteId);
        /** @var  \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $item = $this->getCartItemOptionsProcessor()->addProductOptions($item->getProductType(), $item);
            $output[] = $this->getCartItemOptionsProcessor()->applyCustomOptions($item);
        }
        return $output;
		}
	}
    /**
     * Adds new item or updates existing item to quote
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return array|\Magento\Quote\Api\Data\CartItemInterface[]
     * @throws \Exception
     */
    private function saveItems(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Magento\Quote\Model\Quote $quote */
        $quoteId = $cartItem->getQuoteId();
        $quote = $this->get($quoteId);
        if ($quote->getIsQuotationQuote()) {
            $quoteItems = $quote->getAllVisibleItems();
            $quoteItems[] = $cartItem;
            $quote->setItems($quoteItems);
            $this->save($quote);
            $quote->collectTotals();
            return $this->getItems($quoteId);
        } else {
            throw new \Exception(sprintf(__('Cart2Quote Quote Id %1 does not exist.'), $quoteId));
        }
		}
	}
    /**
     * Delete quote item by id
     *
     * @param int $quoteId
     * @param int $itemId
     * @return bool
     * @throws \Exception
     */
    private function deleteById($quoteId, $itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->get($quoteId);
        if ($quote->getIsQuotationQuote()) {
            $quoteItem = $quote->getItemById($itemId);
            if (!$quoteItem) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The %1 Quote doesn\'t contain the %2 item.', $quoteId, $itemId)
                );
            }
            try {
                $quote->removeQuotationItem($itemId);
                $this->save($quote);
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\CouldNotSaveException(__("The item couldn't be removed from the quote."));
            }
        } else {
            throw new \Exception(sprintf(__("Cart2Quote Quote Id \"%s\" does not exist."), $quoteId));
        }
        return true;
		}
	}
    /**
     * Get quote collection
     */
    private function getQuoteCollection()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory */
        $collectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory::class);
        return $collectionFactory->create();
		}
	}
    /**
     * Get cart item options processor
     *
     * @return \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor
     * @deprecated 100.1.0
     */
    private function getCartItemOptionsProcessor()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->cartItemOptionsProcessor instanceof \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor) {
            $this->cartItemOptionsProcessor = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor::class);
        }
        return $this->cartItemOptionsProcessor;
		}
	}
    /**
     * @param string $loadMethod
     * @param string $loadField
     * @param int $identifier
     * @param array $sharedStoreIds
     * @return CartInterface|\Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function loadQuote($loadMethod, $loadField, $identifier, array $sharedStoreIds = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/** @var CartInterface $quote */
        $quote = $this->cartFactory->create();
        if ($sharedStoreIds && method_exists($quote, 'setSharedStoreIds')) {
            $quote->setSharedStoreIds($sharedStoreIds);
        }
        $quote->setIsQuotationQuote(1);
        $quote->setStoreId($this->storeManager->getStore()->getId())->$loadMethod($identifier);
        if (!$quote->getId()) {
            throw \Magento\Framework\Exception\NoSuchEntityException::singleField($loadField, $identifier);
        }
        return $quote;
		}
	}
    /**
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Exception
     */
    private function submitQuote($quoteId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->get($quoteId);
        if ($quote->getIsQuotationQuote()) {
            $quote->setProposalSent((new \DateTime())->getTimestamp());
            $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_PENDING);
            $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT);
            $this->quoteProposalSender->send($quote);
            return $quote;
        } else {
            throw new \Exception(sprintf(__("Cart2Quote Quote Id \"%s\" does not exist."), $quoteId));
        }
		}
	}
}
