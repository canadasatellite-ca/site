<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Asset\Repository;
use Magento\Quote\Model\Quote\Item;

/**
 * Class ProductThumbnail
 *
 * @package Cart2Quote\Quotation\Helper
 */
class ProductThumbnail extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to configuration setting Show Thumb Nail in Request Mail
     */
    const XML_PATH_SHOW_THUMB_NAIL_REQUEST = "cart2quote_email/quote_request/product_thumbnail";

    /**
     * Path to configuration setting Show Thumb Nail in Proposal Mail
     */
    const XML_PATH_SHOW_THUMB_NAIL_PROPOSAL = "cart2quote_email/quote_proposal/product_thumbnail";

    /**
     * Path to configuration setting Show Thumb Nail in Proposal Mail
     */
    const XML_PATH_SHOW_THUMB_NAIL_PDF = "cart2quote_pdf/quote/product_thumbnail";

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Url interface
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;

    /**
     * ProductThumbnail constructor
     *
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\Block\Template\Context $blockContext
     */
    public function __construct(
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Block\Template\Context $blockContext
    ) {
        $this->productHelper = $productHelper;
        $this->urlModel = $context->getUrlBuilder();
        $this->storeManager = $blockContext->getStoreManager();
        $this->assetRepo = $blockContext->getAssetRepository();
        parent::__construct($context);
    }

    /**
     * Is the Product Thumbnail enabled for the Request Email
     *
     * @return bool
     */
    public function showProductThumbnailRequest()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOW_THUMB_NAIL_REQUEST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is the Product Thumbnail enabled for the Proposal Email
     *
     * @return bool
     */
    public function showProductThumbnailProposal()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOW_THUMB_NAIL_PROPOSAL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is the Product Thumbnail enabled for the PDF
     *
     * @return bool
     */
    public function showProductThumbnailPdf()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SHOW_THUMB_NAIL_PDF,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the Product Thumbnail for Request Email
     *
     * @param Product|\Magento\Framework\DataObject $product
     * @return string
     */
    public function getProductThumbnail($product)
    {
        $url = $this->productHelper->getThumbnailUrl($product);
        if (!empty($url)) {
            return $url;
        }

        $attribute = $product->getResource()->getAttribute('thumbnail');
        if (!$product->getThumbnail()) {
            $url = $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/thumbnail.jpg');
        } elseif ($attribute) {
            $url = $attribute->getFrontend()->getUrl($product);
        }

        return $url;
    }

    /**
     * Get the product url from the quote item
     * (This also works for multi store urls)
     *
     * @param Item $item
     * @return string
     */
    public function getProductUrl($item)
    {
        if ($item->getRedirectUrl()) {
            return $item->getRedirectUrl();
        }

        /** @var Product $product */
        $product = $item->getProduct();
        $option = $item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
    }
}
