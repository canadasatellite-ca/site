<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Email\Items;

/**
 * Quote Email items default renderer
 *
 * Class DefaultItems
 * @package Cart2Quote\Quotation\Block\Quote\Email\Items
 */
class DefaultItems extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @var \Cart2Quote\Quotation\Block\Quote\TierItem
     */
    protected $tierItemBlock;

    /**
     * @var \Cart2Quote\Quotation\Helper\ProductThumbnail
     */
    private $productThumbnailHelper;

    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    private $giftMessageHelper;

    /**
     * DefaultItems constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $productThumbnailHelper
     * @param \Magento\GiftMessage\Helper\Message $giftMessageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $productThumbnailHelper,
        \Magento\GiftMessage\Helper\Message $giftMessageHelper,
        array $data = []
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->tierItemBlock = $tierItemBlock;
        $this->productThumbnailHelper = $productThumbnailHelper;
        $this->giftMessageHelper = $giftMessageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->getItem()->getQuote();
    }

    /**
     * Get item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getQuoteItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * Get value html formated
     *
     * @param string|array $value
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf(
                '%d x %d %d',
                $value['qty'],
                $this->escapeHtml($value['title']),
                $this->getItem()->getQuote()->formatPrice($value['price'])
            );
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * Get SKU from item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return string
     */
    public function getSku($item)
    {
        if ($item->getQuoteItem()->getProductOptionByCode('simple_sku')) {
            return $item->getQuoteItem()->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }

    /**
     * Return product additional information block
     *
     * @return boolean|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get the html for item price
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemPrice($item)
    {
        $block = $this->getLayout()->getBlock('item_row_total');
        $block->setItem($item);
        return $block->toHtml();
    }

    /**
     * Check disabled product comment field
     *
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->quotationHelper->isProductRemarkDisabled();
    }

    /**
     * Get tier item quantity
     *
     * @return string
     */
    public function getTierQtyHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'qty', true);
    }

    /**
     * Get item quantity
     *
     * @return string
     */
    public function getQtyHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'qty');
    }

    /**
     * Get tier item price
     *
     * @return string
     */
    public function getTierPriceHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'price', true);
    }

    /**
     * Get item price
     *
     * @return string
     */
    public function getPriceHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'price');
    }

    /**
     * Get tier item row total
     *
     * @return string
     */
    public function getTierRowTotalHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'subtotal', true);
    }

    /**
     * Get item row total
     *
     * @return string
     */
    public function getRowTotalHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'subtotal');
    }

    /**
     * Check hide item price in request email configuration
     *
     * @return boolean
     */
    public function hidePrice()
    {
        return $this->quotationHelper->isHideEmailRequestPrice();
    }

    /**
     * Getter for the product thumbnail helper
     *
     * @return \Cart2Quote\Quotation\Helper\ProductThumbnail
     */
    public function getProductThumbnailHelper()
    {
        return $this->productThumbnailHelper;
    }

    /**
     * Getter for gift message helper
     *
     * @return \Magento\GiftMessage\Helper\Message
     */
    public function getGiftMessageHelper()
    {
        return $this->giftMessageHelper;
    }
}
