<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Items;

/**
 * Class QuoteItem
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Items
 */
class QuoteItem extends AbstractItems
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Items\QuoteItem {
        draw as private traitDraw;
        getQuoteItemOptions as private traitGetQuoteItemOptions;
        setTierItemsPdf as private traitSetTierItemsPdf;
        drawProductImage as private traitDrawProductImage;
        drawProductPrices as private traitDrawProductPrices;
        saveSpaceFonts as private traitSaveSpaceFonts;
        _setFontRegular as private _traitSetFontRegular;
        _setFontBold as private _traitSetFontBold;
        _setFontItalic as private _traitSetFontItalic;
    }

    /**
     * Directory structure for Product Images
     */
    const CATALOG_PRODUCT_PATH = '/catalog/product';

    /**
     * Feed distance for Item Price
     */
    const PRICE_FEED = 360;

    /**
     * Feed distance Item Qty
     */
    const QTY_FEED = 420;

    /**
     * Feed distance for Item Tax
     */
    const TAX_FEED = 490;

    /**
     * Feed distance Item Row Total
     */
    const ROW_TOTAL_FEED = 560;

    /**
     * Path to pdf_save_space in system.xml
     */
    const XML_PATH_SAVE_SPACE_FONTS = 'cart2quote_pdf/quote/pdf_save_space';

    /**
     * Interface to get information about products
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\ProductThumbnail
     */
    protected $thumbnailHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\CustomProduct
     */
    private $customProductHelper;

    /**
     * QuoteItem constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\CustomProduct $customProductHelper
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\CustomProduct $customProductHelper,
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        $this->thumbnailHelper = $thumbnailHelper;
        parent::__construct(
            $quotationTaxHelper,
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
        $this->customProductHelper = $customProductHelper;
    }

    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $this->traitDraw();
    }

    /**
     * Get Selected Custom Options from a Product
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuoteItemOptions()
    {
        return $this->traitGetQuoteItemOptions();
    }

    /**
     * Set the tier quantity to PDF
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $item
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setTierItemsPdf($quote, $item)
    {
        return $this->traitSetTierItemsPdf($quote, $item);
    }

    /**
     * Print Thumbnails Next to Product Name if enabled
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Sales\Model\Order\Pdf\AbstractPdf $pdf
     * @param \Zend_Pdf_Page $page
     * @param int $feed
     * @param int $skuFeed
     * @param int $split
     * @return bool
     * @throws \Zend_Pdf_Exception
     */
    private function drawProductImage(
        \Magento\Framework\DataObject $item,
        \Magento\Sales\Model\Order\Pdf\AbstractPdf $pdf,
        \Zend_Pdf_Page $page,
        &$feed,
        &$skuFeed,
        &$split
    ) {
        return $this->traitDrawProductImage($item, $pdf, $page, $feed, $skuFeed, $split);
    }

    /**
     * Draw product prices
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array $line
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function drawProductPrices(
        \Magento\Framework\DataObject $item,
        \Cart2Quote\Quotation\Model\Quote $quote,
        array $line
    ) {
        return $this->traitDrawProductPrices($item, $quote, $line);
    }

    /**
     * Get the setting to save fonts
     *
     * @return boolean
     */
    protected function saveSpaceFonts()
    {
        return $this->traitSaveSpaceFonts();
    }

    /**
     * Set font as regular
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($size = 7)
    {
        return $this->_traitSetFontRegular($size);
    }

    /**
     * Set font as bold
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($size = 7)
    {
        return $this->_traitSetFontBold($size);
    }

    /**
     * Set font as italic
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($size = 7)
    {
        return $this->_traitSetFontItalic($size);
    }
}
