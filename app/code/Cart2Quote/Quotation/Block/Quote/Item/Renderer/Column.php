<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Quotation\Block\Quote\Item\Renderer;

/**
 * Class Column
 *
 * @package Cart2Quote\Quotation\Block\Quote\Item\Renderer
 */
class Column extends DefaultRenderer
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepositoryInterface;

    /**
     * Column constructor
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock,
        array $data = []
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        parent::__construct(
            $context,
            $string,
            $productOptionFactory,
            $quotationHelper,
            $tierItemBlock,
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
     * Get tier item quantity
     *
     * @return string
     * @throws \Exception
     */
    public function getTierQtyHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'qty', true);
    }

    /**
     * Get item quantity
     *
     * @return string
     * @throws \Exception
     */
    public function getQtyHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'qty');
    }

    /**
     * Get tier item price
     *
     * @return string
     * @throws \Exception
     */
    public function getTierPriceHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'price', true);
    }

    /**
     * Get item price
     *
     * @return string
     * @throws \Exception
     */
    public function getPriceHtml()
    {
        $formattedPrice = $this->tierItemBlock->getItemHtml($this->getItem(), 'price');
        return sprintf("<span class='c2q-price price-excluding-tax'> %s </span>", $formattedPrice);
    }

    /**
     * Get tier item row total
     *
     * @return string
     * @throws \Exception
     */
    public function getTierRowTotalHtml()
    {
        return $this->tierItemBlock->getItemHtml($this->getItem(), 'subtotal', true);
    }

    /**
     * Get item row total
     *
     * @return string
     * @throws \Exception
     */
    public function getRowTotalHtml()
    {
        $formattedPrice = $this->tierItemBlock->getItemHtml($this->getItem(), 'subtotal');
        return sprintf("<span class='c2q-price price-excluding-tax'> %s </span>", $formattedPrice);
    }

    /**
     * Get selected tier radio button html
     *
     * @return string
     * @throws \Exception
     */
    public function getSelectedRadiobuttonHtml()
    {
        return sprintf(
            "<input checked type='radio' class='qty-tier' name='%s' value='%s'>",
            $this->getItem()->getId(),
            $this->getItem()->getCurrentTierItem()->getId()
        );
    }

    /**
     * Get tier radio buttons html
     *
     * @return string
     * @throws \Exception
     */
    public function getTierRadiobuttonsHtml()
    {
        $linebreak = '<div class="single-price-break"></div>';
        if ($this->tierItemBlock->isDisplayBothPrices()) {
            $linebreak = '<div class="both-prices-break"></div>';
        }
        $tierHtml = $linebreak;
        $item = $this->getItem();
        $tierItems = $item->getTierItems();

        if (isset($tierItems)) {
            foreach ($tierItems as $tierItemId => $tierItem) {
                if (!$this->tierItemBlock->isTierSelected($item, $tierItem->getId())) {
                    $tierHtml .= sprintf(
                        "<input type='radio' class='qty-tier' name='%s' value='%s'>%s",
                        $this->getItem()->getId(),
                        $tierItem->getId(),
                        $linebreak
                    );
                }
            }
        }

        return $tierHtml;
    }

    /**
     * Get config setting for hide prices dashboard
     *
     * @return bool
     */
    public function isHidePrices()
    {
        return $this->quotationHelper->isHidePrices($this->getQuote());
    }

    /**
     * Get config setting for show images customer dashboard
     *
     * @return bool
     */
    public function isShowCustomerDashboardImages()
    {
        return $this->quotationHelper->isShowCustomerDashboardImages($this->getQuote());
    }

    /**
     * Get store specific product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct()
    {
        return $this->productRepositoryInterface->getById(
            $this->getItem()->getProduct()->getId(), false, $this->getQuote()->getStoreId()
        );
    }
}
