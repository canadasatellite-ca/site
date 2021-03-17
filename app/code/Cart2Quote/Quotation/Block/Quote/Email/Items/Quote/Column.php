<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Quotation\Block\Quote\Email\Items\Quote;

use Cart2Quote\Quotation\Helper\CustomProduct;

/**
 * Class Column
 *
 * @package Cart2Quote\Quotation\Block\Quote\Email\Items\Quote
 */
class Column extends DefaultQuote
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * Column constructor
     *
     * @param \Cart2Quote\Quotation\Helper\CustomProduct $customProductHelper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $productThumbnailHelper
     * @param \Magento\GiftMessage\Helper\Message $giftMessageHelper
     * @param array $data
     */
    public function __construct(
        CustomProduct $customProductHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $productThumbnailHelper,
        \Magento\GiftMessage\Helper\Message $giftMessageHelper,
        array $data = []
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        parent::__construct(
            $customProductHelper,
            $context,
            $quotationHelper,
            $tierItemBlock,
            $productThumbnailHelper,
            $giftMessageHelper,
            $data
        );
    }

    /**
     * Get the item from the parent block
     *
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    public function getItem()
    {
        if ($parentBlock = $this->getParentBlock()) {
            return $parentBlock->getItem();
        } else {
            throw new \Exception('Undefined quote item in block ' . $this->getNameInLayout());
        }
    }

    /**
     * Get the quote from the parent block
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Exception
     */
    public function getQuote()
    {
        if ($parentBlock = $this->getParentBlock()) {
            return $parentBlock->getQuote();
        } else {
            throw new \Exception('Undefined quote in block ' . $this->getNameInLayout());
        }
    }
}
