<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionAdvancedPricing\Model\Product\Option\Value;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;
use MageWorx\OptionAdvancedPricing\Helper\Data as Helper;
use MageWorx\OptionAdvancedPricing\Model\TierPrice as TierPriceModel;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;

class AdditionalHtml
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var BasePriceHelper
     */
    protected $basePriceHelper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Option
     */
    protected $option;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var TierPriceModel
     */
    protected $tierPriceModel;

    /**
     * @var \DOMDocument
     */
    protected $dom;

    /**
     * @param Helper $helper
     * @param BaseHelper $baseHelper
     * @param BasePriceHelper $basePriceHelper
     * @param TierPriceModel $tierPriceModel
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Helper $helper,
        BaseHelper $baseHelper,
        BasePriceHelper $basePriceHelper,
        TierPriceModel $tierPriceModel,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->helper          = $helper;
        $this->baseHelper      = $baseHelper;
        $this->basePriceHelper = $basePriceHelper;
        $this->priceCurrency   = $priceCurrency;
        $this->tierPriceModel  = $tierPriceModel;
    }

    /**
     * @param \DOMDocument $dom
     * @param Option $option
     * @return void
     */
    public function getAdditionalHtml($dom, $option)
    {
        if ($this->out($dom, $option)) {
            return;
        }

        $this->dom     = $dom;
        $this->option  = $option;
        $this->product = $option->getProduct();

        if ($this->baseHelper->isCheckbox($this->option) || $this->baseHelper->isRadio($this->option)) {
            $this->addHtmlToMultiSelectionOption();
        } elseif ($this->baseHelper->isDropdown($this->option) || $this->baseHelper->isMultiselect($this->option)) {
            $this->addHtmlToSingleSelectionOption();
        }

        libxml_clear_errors();

        return;
    }

    /**
     * @param \DOMDocument $dom
     * @param Option $option
     * @return bool
     */
    protected function out($dom, $option)
    {
        if (!$this->helper->isTierPriceEnabled()
            || !$this->helper->isDisplayTierPriceTableNeeded()
            || !$dom
            || !$option
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param \DOMElement $node
     * @return string
     */
    protected function getInnerHtml(\DOMElement $node)
    {
        $innerHTML = '';
        $children  = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }

        return $innerHTML;
    }

    /**
     * Get tier price table html
     *
     * @param ProductCustomOptionValuesInterface|Option\Value $value
     * @return string
     */
    protected function getTierPriceHtml($value)
    {
        $tierPrices = $this->tierPriceModel->getSuitableTierPrices($value, true);
        if (!$tierPrices) {
            return '';
        }
        $index = 1;
        $html  = '<ul id="value_' . $value->getOptionTypeId()
            . '_tier_price" class="prices-tier items" style="display: none">';
        foreach ($tierPrices as $tierPriceItem) {
            $index++;
            $html .= '<li class="item">';
            $for = '<span class="price-container price-tier_price tax weee">';
            $for .= '<span class="price-wrapper price-including-tax">';
            $for .= '<span class="price">';
            if ($this->basePriceHelper->isPriceDisplayModeBothTax()) {
                $for .= mb_convert_encoding(
                    $this->priceCurrency->format($this->basePriceHelper->getTaxPrice(
                        $this->product,
                        $tierPriceItem['price'],
                        true
                    )),
                    'HTML-ENTITIES',
                    'UTF-8'
                );
                $for .= '</span></span>' . ' ';
                $for .= '<span data-label="' . __('Excl. Tax') . '" class="price-wrapper price-excluding-tax">';
                $for .= '<span class="price">';
                $for .= mb_convert_encoding(
                    $this->priceCurrency->format($this->basePriceHelper->getTaxPrice(
                        $this->product,
                        $tierPriceItem['price'],
                        false
                    )),
                    'HTML-ENTITIES',
                    'UTF-8'
                );
            } elseif ($this->basePriceHelper->isPriceDisplayModeIncludeTax()) {
                $for .= htmlentities($this->priceCurrency->format($tierPriceItem['price_incl_tax'], false));
            } else {
                $for .= htmlentities($this->priceCurrency->format($tierPriceItem['price'], false));
            }
            $for .= '</span></span></span>';

            $qtyAndTitle = $tierPriceItem['qty'];
            if ($this->baseHelper->isMultiselect($value->getOption())) {
                $qtyAndTitle = $tierPriceItem['qty'] . " (" . $value->getTitle() . ")";
            }
            $html .= __(
                'Buy %1 for %2 each and',
                $qtyAndTitle,
                $for
            );
            $html .= ' ' . '<strong class="benefit">' . __('save');
            $html .= '<span class="percent tier-' . $index . '">' . ' ' . $tierPriceItem['percent'] . '</span>%';
            $html .= '</strong>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * Get qty input html for checkbox, radio
     *
     * @return void
     */
    protected function addHtmlToMultiSelectionOption()
    {
        if (empty($this->option->getValues())) {
            return;
        }
        $count = 1;
        foreach ($this->option->getValues() as $value) {
            $count++;
            $html = $this->getTierPriceHtml($value);
            if (!$html) {
                continue;
            }

            $tpl = new \DOMDocument('1.0', 'UTF-8');
            $tpl->loadHtml($html);

            $xpath    = new \DOMXPath($this->dom);
            $idString = 'options_' . $this->option->getId() . '_' . $count;
            $input    = $xpath->query("//*[@id='$idString']")->item(0);

            $input->setAttribute('style', 'vertical-align: middle');
            $input->parentNode->appendChild($this->dom->importNode($tpl->documentElement, true));
        }
    }

    /**
     * Get qty input html for dropdown, swatch
     *
     * @return void
     */
    protected function addHtmlToSingleSelectionOption()
    {
        if (empty($this->option->getValues())) {
            return;
        }
        foreach ($this->option->getValues() as $value) {
            $html = $this->getTierPriceHtml($value);
            if (!$html) {
                continue;
            }
            $body = $this->dom->documentElement->firstChild;
            $tpl  = new \DOMDocument();
            $tpl->loadHtml($html);
            $body->appendChild($this->dom->importNode($tpl->documentElement, true));
        }
    }
}
