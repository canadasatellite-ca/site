<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Magento\Quote\Model\Quote\Item;

/**
 * Class CustomProduct
 *
 * @package Cart2Quote\Quotation\Helper
 */
class CustomProduct extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Checker if item is a custom product
     *
     * @param Item $item
     * @return bool
     */
    public function isCustomProduct(Item $item)
    {
        return $item->getSku() == \Cart2Quote\Quotation\Model\Quote\CustomProduct::SKU;
    }

    /**
     * Getter for the custom product SKU
     *
     * @param Item $item
     * @return string|null
     */
    public function getCustomProductSku(Item $item)
    {
        return $this->getItemOptionByLabel($item, 'sku');
    }

    /**
     * Getter for the custom product name
     *
     * @param Item $item
     * @return string|null
     */
    public function getCustomProductName(Item $item)
    {
        return $this->getItemOptionByLabel($item, 'name');
    }

    /**
     * Get item options by lable
     *
     * @param Item $item
     * @param string $label
     * @return string|null
     */
    private function getItemOptionByLabel(Item $item, $label)
    {
        foreach ($this->getItemOptions($item) as $option) {
            if ($option['label'] === $label) {
                return $option['value'];
            }
        }
        return null;
    }

    /**
     * Get item options
     *
     * @param Item $item
     * @return array
     */
    public function getItemOptions(Item $item)
    {
        $result = [];
        $product = $item->getProduct();
        $options = $product->getTypeInstance()->getOrderOptions($product);
        if ($options) {
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
}
