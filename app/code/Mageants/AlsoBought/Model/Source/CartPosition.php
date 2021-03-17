<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Model\Source;

class CartPosition implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Content Top'),
                'value' => 'checkout.cart.container:before:-',
            ],
            [
                'label' => __('Content Bottom'),
                'value' => 'content:after:-',
            ],
            [
                'label' => __('Before Cross-sell Product Block'),
                'value' => 'checkout.cart.container:before:checkout.cart.crosssell',
            ],
            [
                'label' => __('Replace with Cross-sell Block'),
                'value' => 'replace:checkout.cart.crosssell',
            ],
            [
                'label' => __('After Cross-sell Block'),
                'value' => 'content:after:checkout.cart.crosssell',
            ],
        ];
        return $options;
    }
}