<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Class TierItem
 *
 * @package Cart2Quote\Quotation\Block\Quote
 */
class TierItem
{
    /**
     * @var \Magento\Tax\Block\Item\Price\Renderer
     */
    protected $itemPriceRenderer;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationTaxHelper
     */
    protected $quotationTaxHelper;

    /**
     * TierItem constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper
     * @param \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\QuotationTaxHelper $quotationTaxHelper,
        \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer
    ) {
        $this->quotationTaxHelper = $quotationTaxHelper;
        $this->itemPriceRenderer = $itemPriceRenderer;
    }

    /**
     * Is tier selected
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param integer $tierId
     * @return boolean
     */
    public function isTierSelected($item, $tierId)
    {
        return $item->getCurrentTierItem()->getId() == $tierId;
    }

    /**
     * Generate the item html to display
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param string $column
     * @param boolean $isTierItem
     * @return string
     */
    public function getItemHtml($item, $column, $isTierItem = false)
    {
        $itemsData = $this->setItemData($item, $isTierItem);
        $itemHtml = '';
        foreach ($itemsData as $itemData) {
            foreach ($itemData as $data) {
                if ($column == 'qty') {
                    $itemHtml .= sprintf(
                        "<div class='%s'><span class='price'>%s</span></div>",
                        $data['class'],
                        $data[$column]
                    );
                } else {
                    $itemHtml .= sprintf(
                        "<div class='%s'><span class='tax-label'>%s</span> <span>%s</span></div>",
                        $data['class'],
                        $data['label'],
                        $data[$column]
                    );
                }
            }
        }

        return $itemHtml;
    }

    /**
     * Set tieritem data to array
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param boolean $isTierItem
     * @return array
     */
    public function setItemData($item, $isTierItem)
    {
        $tierItems = $item->getTierItems();
        $itemData = [];
        if (isset($tierItems)) {
            foreach ($tierItems as $tierItem) {
                $tierItemId = $tierItem->getId();
                $selectedTier = $this->isTierSelected($item, $tierItemId);
                if ((!$isTierItem && $selectedTier) || ($isTierItem && !$selectedTier)) {
                    if ($this->itemPriceRenderer->displayBothPrices()) {
                        $itemData[$tierItemId] = [
                            [
                                'label' => __('<span>Excl.</span> Tax') . ':',
                                'class' => 'both-prices-excluding',
                                'price' => $item->getQuote()->formatPriceTxt($this->quotationTaxHelper->getPriceExclTax($tierItem)),
                                'subtotal' => $item->getQuote()->formatPriceTxt($tierItem->getRowTotal()),
                                'qty' => $tierItem->getQty() * 1
                            ],
                            [
                                'label' => __('<span>Incl.</span> Tax') . ':',
                                'class' => 'both-prices-including',
                                'price' => $item->getQuote()->formatPriceTxt($tierItem->getPriceInclTax()),
                                'subtotal' => $item->getQuote()->formatPriceTxt($tierItem->getRowTotalInclTax()),
                                'qty' => ''
                            ],
                        ];
                    } elseif ($this->itemPriceRenderer->displayPriceInclTax()) {
                        $itemData[$tierItemId] = [
                            [
                                'label' => '',
                                'class' => 'price-including',
                                'price' => $item->getQuote()->formatPriceTxt($tierItem->getPriceInclTax()),
                                'subtotal' => $item->getQuote()->formatPriceTxt($tierItem->getRowTotalInclTax()),
                                'qty' => $tierItem->getQty() * 1
                            ],
                        ];
                    } else {
                        $itemData[$tierItemId] = [
                            [
                                'label' => '',
                                'class' => 'price-excluding',
                                'price' => $item->getQuote()->formatPriceTxt($this->quotationTaxHelper->getPriceExclTax($tierItem)),
                                'subtotal' => $item->getQuote()->formatPriceTxt($tierItem->getRowTotal()),
                                'qty' => $tierItem->getQty() * 1
                            ],
                        ];
                    }
                }
            }
        }

        return $itemData;
    }

    /**
     * Check if display both prices
     *
     * @return boolean
     */
    public function isDisplayBothPrices()
    {
        if ($this->itemPriceRenderer->displayBothPrices()) {
            return true;
        }

        return false;
    }
}
