<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Email\Items\Quote;

use Cart2Quote\Quotation\Helper\CustomProduct;

/**
 * Quotation Quote Email items default renderer
 */
class DefaultQuote extends \Cart2Quote\Quotation\Block\Quote\Email\Items\DefaultItems
{
    /**
     * @var CustomProduct
     */
    private $customProductHelper;

    /**
     * DefaultQuote constructor
     *
     * @param \Cart2Quote\Quotation\Helper\CustomProduct $customProductHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $productThumbnailHelper
     * @param \Magento\GiftMessage\Helper\Message $giftMessageHelper
     * @param array $data
     */
    public function __construct(
        CustomProduct $customProductHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $productThumbnailHelper,
        \Magento\GiftMessage\Helper\Message $giftMessageHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $quotationHelper,
            $tierItemBlock,
            $productThumbnailHelper,
            $giftMessageHelper,
            $data
        );

        $this->customProductHelper = $customProductHelper;
    }

    /**
     * Get item options
     *
     * @return array
     */
    public function getItemOptions()
    {
        return $this->customProductHelper->getItemOptions($this->getItem());
    }

    /**
     * Get SKU
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return string
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku')) {
            return $item->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }

    /**
     *  Get the html for item price
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
}
