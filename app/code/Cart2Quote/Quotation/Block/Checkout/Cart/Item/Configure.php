<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Checkout\Cart\Item;

/**
 * Cart Item Configure block
 * - Updates templates and blocks to show 'Update Cart' button and set right form submit url
 *
 * @module     Checkout
 */
class Configure extends \Magento\Checkout\Block\Cart\Item\Configure
{
    /**
     * Configure product view blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $block->setSubmitRouteData(
                [
                    'route' => 'quotation/quote/updateItemOptions',
                    'params' => ['id' => $this->getRequest()->getParam('id')],
                ]
            );
        }

        return $this;
    }
}
