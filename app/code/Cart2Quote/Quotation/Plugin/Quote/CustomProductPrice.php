<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Quote;

/**
 * Class CustomProductPrice
 *
 * @package Cart2Quote\Quotation\Plugin\Quote
 */
class CustomProductPrice
{
    /**
     * After add product plugin
     *
     * @param \Cart2Quote\Quotation\Model\Quote $subject
     * @param \Magento\Quote\Model\Quote\Item $result
     * @return \Magento\Quote\Model\Quote\Item $result
     */
    public function afterAddProduct(\Cart2Quote\Quotation\Model\Quote $subject, $result)
    {
        if ($result->getSku() == 'custom-product') {
            $options = $result->getProduct()->getOptions();
            foreach ($options as $option) {
                if ($option->getTitle() == 'price') {
                    $optionId = 'option_' . $option->getOptionId();
                    if (!empty($result->getOptionsByCode()[$optionId])) {
                        $customPrice = $result->getOptionsByCode()[$optionId]->getValue();
                        $result->getProduct()->setPrice($customPrice);
                    }
                }
            }
        }

        return $result;
    }
}