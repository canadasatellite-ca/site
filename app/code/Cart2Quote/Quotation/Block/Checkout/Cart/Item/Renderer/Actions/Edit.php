<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout\Cart\Item\Renderer\Actions;

/**
 * Class Edit
 *
 * @package Cart2Quote\Quotation\Block\Checkout\Cart\Item\Renderer\Actions
 */
class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
{
    /**
     * Get item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'quotation/quote/configure',
            [
                'id' => $this->getItem()->getId(),
                'product_id' => $this->getItem()->getProduct()->getId(),
            ]
        );
    }
}
