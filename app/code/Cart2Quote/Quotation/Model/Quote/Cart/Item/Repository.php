<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Cart\Item;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Repository
 * @package Cart2Quote\Quotation\Model\Quote\Cart\Item
 */
class Repository implements \Cart2Quote\Quotation\Api\QuoteCartItemRepositoryInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Cart\Item\Repository {
        getList as private traitGetList;
        save as private traitSave;
        editQuoteItem as private traitEditQuoteItem;
        deleteById as private traitDeleteById;
    }

    /**
     * Quote repository.
     *
     * @var \Cart2Quote\Quotation\Api\QuoteRepositoryInterface
     */
    protected $quotationRepository;

    /**
     * Product repository.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor
     */
    private $cartItemOptionsProcessor;

    /**
     * @var QuoteRepository|\Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * Repository constructor.
     *
     * @param \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quotationRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $cartItemOptionsProcessor
     */
    public function __construct(
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quotationRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor $cartItemOptionsProcessor
    ) {
        $this->quotationRepository = $quotationRepository;
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->itemDataFactory = $itemDataFactory;
        $this->cartItemOptionsProcessor = $cartItemOptionsProcessor;
    }

    /**
     * Get array of items from active quote cart for logged in customer
     *
     * @param int $customerId The customer ID.
     * @return \Magento\Quote\Api\Data\CartItemInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getList($customerId)
    {
        return $this->traitGetList($customerId);
    }

    /**
     * @param int $customerId The customer ID.
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     * @throws NoSuchEntityException
     */
    public function save($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        return $this->traitSave($customerId, $cartItem);
    }

    /**
     * @param int $customerId The customer ID.
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     * @throws NoSuchEntityException
     */
    public function editQuoteItem($customerId, \Magento\Quote\Api\Data\CartItemInterface $cartItem)
    {
        return $this->traitEditQuoteItem($customerId, $cartItem);
    }

    /**
     * Delete specified item in quote cart for logged in customer
     *
     * @param int $customerId
     * @param int $itemId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function deleteById($customerId, $itemId)
    {
        return $this->traitDeleteById($customerId, $itemId);
    }
}
