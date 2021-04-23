<?php
/**
 * Cart2Quote
 */

namespace MageSuper\Casat\Model\PurchaseOrder\Pdf\Items;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Model\Sales\Quote\Pdf\Items
 */
class QuoteItem extends AbstractItems
{
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
     * QuoteItem constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct(
            $context,
            $registry,
            $taxData,
            $filesystem,
            $filterManager,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Draw item line
     *
     * @return void
     */
    function draw()
    {
        $quote = $this->getQuote();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $this->_setFontRegular();
        $prevOptionId = '';
        $drawItems = [];
        $line = [];
        $isShowQtyReceived = $quote->getPoBlock()->checkIsShowQtyReceived();

        $attributes = $this->getSelectionAttributes($item);
        if (is_array($attributes)) {
            $optionId = $attributes['option_id'];
        } else {
            $optionId = 0;
        }
        if (!isset($drawItems[$optionId])) {
            $drawItems[$optionId] = ['lines' => [], 'height' => 15];
        }

        $name = $quote->getPoBlock()->getProductName($item);
        /* in case Product name is longer than 80 chars - it is written in a few lines */

        $feed = 35;
        $name = str_replace('‐','-',$name);
        $nameArray['text'] = $pdf->getStringUtils()->split($name, 50, true, true);
        $nameArray['feed'] = $feed;
        $nameArray['addToTop'] = -5;
        $nameArray['isProductLine'] = true;

        $line[] = $nameArray;

        // draw SKUs
        $text = [];
        $ProductSupplierSku = str_replace('‐','-',$item->getProductSupplierSku());
        foreach ($pdf->getStringUtils()->split($ProductSupplierSku, 17) as $part) {
            $text[] = $part;
        }
        $line[] = ['text' => $text, 'feed' => 400, 'isProductLine' => true, 'addToTop' => -5, 'align' => 'right',];

        // draw prices

        if ($item->getCost() != null) {
            $price = $pdf->headerblock->getPriceFormat($item->getCost());

            $line[] = [
                'text' => $item->getQtyOrderred() * 1,
                'feed' => 445,
                'isProductLine' => true,
                'addToTop' => -5
            ];
            /*if($isShowQtyReceived){
                $line[] = [
                    'text' => $item->getQtyReceived() * 1,
                    'feed' => 450,
                    'align' => 'right',
                    'isProductLine' => true,
                    'addToTop' => -5
                ];
            }*/
            $line[] = [
                'text' => $price,
                'feed' => 505,
                'align' => 'right',
                'isProductLine' => true,
                'addToTop' => -5
            ];
            $row_total = $pdf->headerblock->getItemTotal($item);
            $line[] = [
                'text' => $row_total,
                'feed' => 570,
                'font' => 'bold',
                'align' => 'right',
                'isProductLine' => true,
                'addToTop' => -5
            ];
        }
        $drawItems[$optionId]['lines'][] = $line;

        // custom options

        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
        $this->setPage($page);
    }
}
