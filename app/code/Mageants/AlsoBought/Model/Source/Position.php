<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Model\Source;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Content Top'),
                'value' => 'content:before:-',
            ],
            [
                'label' => __('Content Bottom'),
                'value' => 'content:after:-',
            ],
            [
                'label' => __('Before Related Product Block'),
                'value' => 'content.aside:before:catalog.product.related',
            ],
            [
                'label' => __('Replace with Related Product Block'),
                'value' => 'replace:catalog.product.related',
            ],
            [
                'label' => __('After Related Product Block'),
                'value' => 'content.aside:after:catalog.product.related',
            ],
            [
                'label' => __('Before Up-sell Product Block'),
                'value' => 'content.aside:before:product.info.upsell',
            ],
            [
                'label' => __('Replace with Up-sell Block'),
                'value' => 'replace:product.info.upsell',
            ],
            [
                'label' => __('After Up-sell Block'),
                'value' => 'content.aside:after:product.info.upsell',
            ],
        ];
        return $options;
    }
}