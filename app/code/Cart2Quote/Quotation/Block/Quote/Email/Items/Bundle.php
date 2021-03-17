<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote\Email\Items;

/**
 * Class Bundle
 *
 * @package Cart2Quote\Quotation\Block\Quote\Email\Items
 */
class Bundle extends \Magento\Bundle\Block\Sales\Order\Items\Renderer
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
    protected $thumbnailHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * Bundle constructor
     *
     * @param \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Cart2Quote\Quotation\Helper\ProductThumbnail $thumbnailHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->tierItemBlock = $tierItemBlock;
        $this->quotationHelper = $quotationHelper;
        $this->thumbnailHelper = $thumbnailHelper;
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context, $string, $productOptionFactory, $data);
    }

    /**
     * Check if hide request email item price
     *
     * @return boolean
     */
    public function hidePrice()
    {
        return $this->quotationHelper->isHideEmailRequestPrice();
    }

    /**
     * Get price formated for HTML
     *
     * @return string
     */
    public function getPriceHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'price');
    }

    /**
     * Getter for the tumbnail helper
     *
     * @return \Cart2Quote\Quotation\Helper\ProductThumbnail
     */
    public function getThumbnailHelper()
    {
        return $this->thumbnailHelper;
    }

    /**
     * Getter for the pricing helper
     *
     * @return \Magento\Framework\Pricing\Helper\Data
     */
    public function getpricingHelper()
    {
        return $this->pricingHelper;
    }
}
