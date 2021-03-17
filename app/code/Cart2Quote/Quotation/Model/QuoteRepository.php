<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Quote\Model\QuoteRepository as MageQuoteRepository;
use Cart2Quote\Quotation\Api\QuoteRepositoryInterface;

/**
 * Class QuoteRepository
 *
 * @package Cart2Quote\Quotation\Model
 */
class QuoteRepository extends MageQuoteRepository implements QuoteRepositoryInterface
{
    use \Cart2Quote\Features\Traits\Model\QuoteRepository {
        get as private traitGet;
        getQuotesList as private traitGetQuotesList;
        saveQuote as private traitSaveQuote;
        deleteQuote as private traitDeleteQuote;
        getItems as private traitGetItems;
        saveItems as private traitSaveItems;
        deleteById as private traitDeleteById;
        getQuoteCollection as private traitGetQuoteCollection;
        getCartItemOptionsProcessor as private traitGetCartItemOptionsProcessor;
        loadQuote as private traitLoadQuote;
        submitQuote as private traitSubmitQuote;
    }

    /**
     * @var \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor
     */
    private $cartItemOptionsProcessor;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quotationFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var \Magento\Quote\Api\Data\CartInterfaceFactory
     */
    private $cartFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Quote\Email\Sender\QuoteProposalSender
     */
    private $quoteProposalSender;

    /**
     * QuoteRepository constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Api\Data\CartSearchResultsInterfaceFactory $searchResultsDataFactory
     * @param ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param Quote\Email\Sender\QuoteProposalSender $quoteProposalSender
     * @param \Magento\Quote\Api\Data\CartInterfaceFactory|null $cartFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\Data\CartSearchResultsInterfaceFactory $searchResultsDataFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender $quoteProposalSender,
        \Magento\Quote\Api\Data\CartInterfaceFactory $cartFactory
    ) {
        $this->storeManager = $storeManager;
        $this->quotationFactory = $quotationFactory;
        $this->quoteFactory = $quoteFactory;
        $this->searchResultsDataFactory = $searchResultsDataFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteProposalSender = $quoteProposalSender;
        $this->cartFactory = $cartFactory;
    }

    /**
     * Get by quote id
     *
     * @param int $quoteId
     * @param array $sharedStoreIds
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Exception
     */
    public function get($quoteId, array $sharedStoreIds = [])
    {
        return $this->traitGet($quoteId, $sharedStoreIds);
    }


    /**
     * Get all the quotes with search
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Quote\Api\Data\CartSearchResultsInterface
     * @throws \Exception
     */
    public function getQuotesList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->traitGetQuotesList($searchCriteria);
    }

    /**
     * Save quote
     *
     * @param \Cart2Quote\Quotation\Api\Data\QuoteInterface $quote
     */
    public function saveQuote(\Cart2Quote\Quotation\Api\Data\QuoteInterface $quote)
    {
        $this->traitSaveQuote($quote);
    }

    /**
     * Delete quote
     *
     * @param int $quoteId
     * @param array $sharedStoreIds
     * @throws \Exception
     */
    public function deleteQuote($quoteId, array $sharedStoreIds)
    {
        $this->traitDeleteQuote($quoteId, $sharedStoreIds);
    }

    /**
     * Get items
     *
     * @param int $quoteId
     * @return array
     * @throws \Exception
     */
    public function getItems($quoteId)
    {
        return $this->traitGetItems($quoteId);
    }

    /**
     * Adds new item or updates existing item to quote
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return array|\Magento\Quote\Api\Data\CartItemInterface[]
     * @throws \Exception
     */
    public function saveItems(\Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        return $this->traitSaveItems($cartItem);
    }

    /**
     * Delete quote item by id
     *
     * @param int $quoteId
     * @param int $itemId
     * @return bool
     * @throws \Exception
     */
    public function deleteById($quoteId, $itemId)
    {
        return $this->traitDeleteById($quoteId, $itemId);
    }

    /**
     * Get quote collection
     */
    protected function getQuoteCollection()
    {
        return $this->traitGetQuoteCollection();
    }

    /**
     * Get cart item options processor
     *
     * @return \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor
     * @deprecated 100.1.0
     */
    private function getCartItemOptionsProcessor()
    {
        return $this->traitGetCartItemOptionsProcessor();
    }

    /**
     * @param string $loadMethod
     * @param string $loadField
     * @param int $identifier
     * @param array $sharedStoreIds
     * @return CartInterface|\Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function loadQuote($loadMethod, $loadField, $identifier, array $sharedStoreIds = [])
    {
        return $this->traitLoadQuote($loadMethod, $loadField, $identifier, $sharedStoreIds);
    }

    /**
     * @param int $quoteId
     * @return \Cart2Quote\Quotation\Api\Data\QuoteCartInterface|\Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Exception
     */
    public function submitQuote($quoteId)
    {
        return $this->traitSubmitQuote($quoteId);
    }
}
