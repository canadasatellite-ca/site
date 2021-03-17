<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Request\Compare\Form;

use Cart2Quote\Quotation\Block\Product\Listing\Form as ListingForm;
use Cart2Quote\Quotation\Model\Strategy\StrategyInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ActionInterface;

/**
 * Class QuoteList
 *
 * @package Cart2Quote\Quotation\Block\Quote\Request\Form
 */
class QuoteList extends ListingForm implements StrategyInterface
{
    /**
     * @var string
     */
    protected $_template = "Cart2Quote_Quotation::product/list/quote/request/quotelist/compare/form.phtml";

    /**
     * @var Data
     */
    private $urlHelper;

    /**
     * @var Magento\Catalog\Helper\Product\Compare
     */
    private $compareHelper;

    /**
     * QuoteList constructor.
     * @param \Cart2Quote\Quotation\Helper\QuotationCart $quotationCartHelper
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\QuotationCart $quotationCartHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($quotationCartHelper, $quotationHelper, $customerSession, $context, $data);
        $this->urlHelper = $urlHelper;
        $this->compareHelper = $context->getCompareProduct();
    }

    /**
     * Get post parameters
     *
     * @param Product $product
     * @return array
     */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * Get the add to quote product url
     *
     * @param Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToQuoteUrl($product, $additional = [])
    {
        $productTypeInstance = $product->getTypeInstance();
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE ||
            $product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE ||
            $product->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $notBuyableFromList = true;
        } else {
            //if the product has options, we can't add it to the quote form list/grid pages
            $notBuyableFromList = $productTypeInstance->hasRequiredOptions($product);
        }

        if ($notBuyableFromList) {
            return $this->compareHelper->getAddToCartUrl($product);
        }

        return $this->quotationCartHelper->getAddUrl($product, $additional);
    }
}
