<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote;

use Cart2Quote\Quotation\Model\Quote;

/**
 * Class Tax
 *
 * @package Cart2Quote\Quotation\Block\Quote
 */
class Tax extends \Magento\Tax\Block\Sales\Order\Tax
{
    /**
     * @var Quote
     */
    protected $_quote;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        array $data = []
    ) {
        $this->_config = $taxConfig;
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct(
            $context,
            $taxConfig,
            $data
        );
    }

    /**
     * Check if we nedd display full tax total info
     *
     * @return bool
     */
    public function displayFullSummary()
    {
        return $this->_config->displaySalesFullSummary($this->getQuote()->getStore());
    }

    /**
     * Get quote
     *
     * @return Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Initialize all order totals relates with tax
     *
     * @return \Cart2Quote\Quotation\Block\Quote\Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_quote = $parent->getQuote();
        $this->_source = $parent->getSource();

        $store = $this->getStore();
        $allowTax = $this->_source->getTaxAmount() > 0 || $this->_config->displaySalesZeroTax($store);
        $grandTotal = (double)$this->_source->getGrandTotal();
        if (!$grandTotal || $allowTax && !$this->_config->displaySalesTaxWithGrandTotal($store)) {
            $this->_addTax();
        }

        $this->_initOriginalSubtotal();
        $this->_initSubtotal();
        $this->_initShipping();
        $this->_initDiscount();
        $this->_initGrandTotal();
        return $this;
    }

    /**
     * Get order store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_quote->getStore();
    }

    /**
     * Init subtotal
     *
     * @return $this
     */
    protected function _initOriginalSubtotal()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $origSubtotal = $parent->getTotal('original_subtotal');
        if (!$origSubtotal) {
            return $this;
        }
        if ($this->_config->displaySalesSubtotalBoth($store)) {
            $origSubtotal = (double)$this->_source->getOriginalSubtotal();
            $origBaseSubtotal = (double)$this->_source->getBaseOriginalSubtotal();
            $origSubtotalInclTax = (double)$this->_source->getOriginalSubtotalInclTax();
            $origBaseSubtotalInclTax = (double)$this->_source->getBaseOriginalSubtotalInclTax();
            if (!$origSubtotalInclTax || !$origBaseSubtotalInclTax) {
                return $this;
            }

            $origSubtotalInclTax = max(0, $origSubtotalInclTax);
            $origBaseSubtotalInclTax = max(0, $origBaseSubtotalInclTax);

            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'original_subtotal_excl',
                    'value' => $origSubtotal,
                    'base_value' => $origBaseSubtotal,
                    'label' => __('Original Subtotal (Excl. Tax)'),
                ]
            );
            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'original_subtotal_incl',
                    'value' => $origSubtotalInclTax,
                    'base_value' => $origBaseSubtotalInclTax,
                    'label' => __('Original Subtotal (Incl. Tax)'),
                ]
            );

            $parent->addTotal($totalExcl, 'first');
            $parent->addTotal($totalIncl, 'original_subtotal_excl');
            $parent->removeTotal('original_subtotal');

        } elseif ($this->_config->displaySalesSubtotalInclTax($store)) {
            $origSubtotalInclTax = (double)$this->_source->getOriginalSubtotalInclTax();
            $origBaseSubtotalInclTax = (double)$this->_source->getBaseOriginalSubtotalInclTax();

            if (!$origSubtotalInclTax || !$origBaseSubtotalInclTax) {
                return $this;
            }

            $total = $parent->getTotal('original_subtotal');
            if ($total) {
                $total->setValue(max(0, $origSubtotalInclTax));
                $total->setBaseValue(max(0, $origBaseSubtotalInclTax));
            }

            $quoteAdjustment = $parent->getTotal('quote_adjustment');
            $baseSubtotal = (double)$this->_source->getBaseSubtotal();
            $baseSubtotalIncl = (double)$this->_source->getBaseSubtotalInclTax();
            if ($quoteAdjustment && $baseSubtotal && $baseSubtotalIncl) {
                $quoteAdjustment->setValue(max(0, $baseSubtotal - $origSubtotalInclTax));
                $quoteAdjustment->setBaseValue(max(0, $origBaseSubtotalInclTax - $baseSubtotalIncl));
            }
        }

        return $this;
    }

    /**
     * Init subtotal
     *
     * @return $this
     */
    protected function _initSubtotal()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $subtotal = $parent->getTotal('subtotal');
        if (!$subtotal) {
            return $this;
        }
        if ($this->_config->displaySalesSubtotalBoth($store)) {
            $subtotal = (double)$this->_source->getSubtotal();
            $baseSubtotal = (double)$this->_source->getBaseSubtotal();
            $subtotalIncl = (double)$this->_source->getSubtotalInclTax();
            $baseSubtotalIncl = (double)$this->_source->getBaseSubtotalInclTax();

            if (!$subtotalIncl || !$baseSubtotalIncl) {
                // Calculate the subtotal if it is not set
                $subtotalIncl = $subtotal
                    + $this->_source->getTaxAmount()
                    - $this->_source->getShippingTaxAmount();
                $baseSubtotalIncl = $baseSubtotal
                    + $this->_source->getBaseTaxAmount()
                    - $this->_source->getBaseShippingTaxAmount();

                if ($this->_source instanceof Quote) {
                    // Adjust for the discount tax compensation
                    foreach ($this->_source->getAllItems() as $item) {
                        $subtotalIncl += $item->getDiscountTaxCompensationAmount();
                        $baseSubtotalIncl += $item->getBaseDiscountTaxCompensationAmount();
                    }
                }
            }

            $subtotalIncl = max(0, $subtotalIncl);
            $baseSubtotalIncl = max(0, $baseSubtotalIncl);
            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'subtotal_excl',
                    'value' => $subtotal,
                    'base_value' => $baseSubtotal,
                    'label' => __('Subtotal (Excl. Tax)'),
                ]
            );
            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'subtotal_incl',
                    'value' => $subtotalIncl,
                    'base_value' => $baseSubtotalIncl,
                    'label' => __('Subtotal (Incl. Tax)'),
                ]
            );
            $parent->addTotal($totalExcl, 'subtotal');
            $parent->addTotal($totalIncl, 'subtotal_excl');
            $parent->removeTotal('subtotal');
        } elseif ($this->_config->displaySalesSubtotalInclTax($store)) {
            $subtotalIncl = (double)$this->_source->getSubtotalInclTax();
            $baseSubtotalIncl = (double)$this->_source->getBaseSubtotalInclTax();

            if (!$subtotalIncl) {
                $subtotalIncl = $this->_source->getSubtotal() +
                    $this->_source->getTaxAmount() -
                    $this->_source->getShippingTaxAmount();
            }
            if (!$baseSubtotalIncl) {
                $baseSubtotalIncl = $this->_source->getBaseSubtotal() +
                    $this->_source->getBaseTaxAmount() -
                    $this->_source->getBaseShippingTaxAmount();
            }

            $total = $parent->getTotal('subtotal');
            if ($total) {
                $total->setValue(max(0, $subtotalIncl));
                $total->setBaseValue(max(0, $baseSubtotalIncl));
            }
        }
        return $this;
    }

    /**
     * Init shipping
     *
     * @return $this
     */
    protected function _initShipping()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $shipping = $parent->getTotal('shipping');
        if (!$shipping) {
            return $this;
        }

        if ($this->_config->displaySalesShippingBoth($store)) {
            $shipping = (double)$this->_source->getShippingAmount();
            $baseShipping = (double)$this->_source->getBaseShippingAmount();
            $shippingIncl = (double)$this->_source->getShippingInclTax();
            if (!$shippingIncl) {
                $shippingIncl = $shipping + (double)$this->_source->getShippingTaxAmount();
            }
            $baseShippingIncl = (double)$this->_source->getBaseShippingInclTax();
            if (!$baseShippingIncl) {
                $baseShippingIncl = $baseShipping + (double)$this->_source->getBaseShippingTaxAmount();
            }

            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'value' => $shipping,
                    'base_value' => $baseShipping,
                    'label' => __('Shipping & Handling (Excl. Tax)'),
                ]
            );
            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping_incl',
                    'value' => $shippingIncl,
                    'base_value' => $baseShippingIncl,
                    'label' => __('Shipping & Handling (Incl. Tax)'),
                ]
            );
            $parent->addTotal($totalExcl, 'shipping');
            $parent->addTotal($totalIncl, 'shipping');
        } elseif ($this->_config->displaySalesShippingInclTax($store)) {
            $shippingIncl = $this->_source->getShippingInclTax();
            if (!$shippingIncl) {
                $shippingIncl = $this->_source->getShippingAmount() + $this->_source->getShippingTaxAmount();
            }
            $baseShippingIncl = $this->_source->getBaseShippingInclTax();
            if (!$baseShippingIncl) {
                $baseShippingIncl = $this->_source->getBaseShippingAmount() +
                    $this->_source->getBaseShippingTaxAmount();
            }
            $total = $parent->getTotal('shipping');
            if ($total) {
                $total->setValue($shippingIncl);
                $total->setBaseValue($baseShippingIncl);
            }
        }
        return $this;
    }

    /**
     * Init discount
     *
     * @return void
     */
    protected function _initDiscount()
    {
        //nothing
    }

    /**
     * Init grand total
     *
     * @return $this
     */
    protected function _initGrandTotal()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $grandTotal = $parent->getTotal('grand_total');
        if (!$grandTotal || !(double)$this->_source->getGrandTotal()) {
            return $this;
        }

        if ($this->_config->displaySalesTaxWithGrandTotal($store)) {
            $grandTotal = $this->_source->getGrandTotal();
            $baseGrandTotal = $this->_source->getBaseGrandTotal();
            $grandTotalExcl = $this->getGrandTotalExcl($grandTotal);
            $baseGrandTotalExcl = $this->getBaseGrandTotalExcl($baseGrandTotal);
            $grandTotalExcl = max($grandTotalExcl, 0);
            $baseGrandTotalExcl = max($baseGrandTotalExcl, 0);

            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'grand_total',
                    'strong' => true,
                    'value' => $grandTotalExcl,
                    'base_value' => $baseGrandTotalExcl,
                    'label' => __('Grand Total (Excl.Tax)'),
                ]
            );

            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'grand_total_incl',
                    'strong' => true,
                    'value' => $grandTotal,
                    'base_value' => $baseGrandTotal,
                    'label' => __('Grand Total (Incl.Tax)'),
                ]
            );

            $parent->addTotal($totalExcl, 'grand_total');
            $this->_addTax('grand_total');
            $parent->addTotal($totalIncl, 'tax');
        }

        return $this;
    }

    /**
     * Get label properties
     *
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get value properties
     *
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Check disabled product comment field
     *
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->cart2QuoteHelper->isProductRemarkDisabled();
    }

    /**
     * Has Optional Products
     *
     * @return bool
     */
    public function hasOptionalProducts()
    {
        return $this->getParentBlock() &&
            $this->getParentBlock()->hasOptionalProducts();
    }

    /**
     * Get base grand total
     *
     * @param float $baseGrandTotal
     * @return float
     */
    private function getBaseGrandTotalExcl($baseGrandTotal)
    {
        return $baseGrandTotal - $this->_source->getBaseTaxAmount() - $this->_source->getBaseShippingTaxAmount();
    }

    /**
     * Get grand total excluding tax
     *
     * @param float $grandTotal
     * @return float
     */
    private function getGrandTotalExcl($grandTotal)
    {
        return $grandTotal - $this->_source->getTaxAmount() - $this->_source->getShippingTaxAmount();
    }
}
