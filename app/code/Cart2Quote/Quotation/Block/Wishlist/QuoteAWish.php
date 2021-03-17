<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Wishlist;

/**
 * Class QuoteAWish
 * @package Cart2Quote\Quotation\Block\Wishlist
 */
class QuoteAWish extends \Magento\Wishlist\Block\AbstractBlock
{

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data:USE_DEFAULT
     */
    protected $cart2QuoteHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * QuoteAWish constructor
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        $this->customerSession = $customerSession;
        $this->product = $product;
        $this->scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        parent::__construct(
            $context,
            $httpContext,
            $data
        );
    }

    /**
     * @return bool
     */
    public function checkForWishlistDisplayVariables()
    {
        if ($this->isQuotationEnabled() && $this->isQuoteAWishEnabled() && $this->isWishlistEmpty()) {
            return true;
        }
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isWishlistQuotable()
    {
        if (!$this->isProductQuotable() || $this->isProductProblem()) {
            return true;
        }
    }

    /**
     * Check for non-quotable products in wish list
     *
     * return bool
     */
    public function isProductQuotable()
    {
        $wishlistItems = $this->getWishlistItems();
        foreach ($wishlistItems as $wishlistItem) {
            try {
                $this->cart2QuoteHelper->isQuotable(
                    $this->getAsCatalogProduct($wishlistItem),
                    $this->customerSession->getCustomerGroupId()
                );
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }
    }

    /**
     * Find product as catalog product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @var \Magento\Wishlist\Model\Item $item
     */
    public function getAsCatalogProduct($item)
    {
        $productId = $this->product->getIdBySku($item->getProduct()->getSku());
        return $this->productRepositoryInterface->getById($productId);
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isProductProblem()
    {
        $wishlistItems = $this->getWishlistItems();
        foreach ($wishlistItems as $wishlistItem) {
            $catalogItem = $this->getAsCatalogProduct($wishlistItem);
            if ($catalogItem->getTypeId() != 'configurable') {

                return true;
            }
        }
    }

    /**
     * Check if wish list contains any products
     *
     * @return bool
     */
    public function isWishlistEmpty()
    {
        $items = $this->getWishlistItems();
        return $items->getSize() > 0;
    }

    /**
     * Check if Cart2Quote visibility is enabled
     *
     * @return bool
     */
    public function isQuotationEnabled()
    {
        if (isset($this->_visibilityEnabled)) {
            return $this->_visibilityEnabled;
        }

        if ($this->cart2QuoteHelper->isFrontendEnabled()) {
            $this->_visibilityEnabled = true;
            return true;
        }

        $this->_visibilityEnabled = false;
        return $this->_visibilityEnabled;
    }

    /**
     * Check if the quote a wish functionality has been enabled
     *
     * @return bool
     */
    public function isQuoteAWishEnabled()
    {
        return $this->cart2QuoteHelper->isQuoteAWishEnabled();
    }
}
