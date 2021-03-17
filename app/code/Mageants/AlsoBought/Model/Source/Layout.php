<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Model\Source;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Single Row'),
                'value' => 'single',
            ],
            [
                'label' => __('Slider'),
                'value' => 'slider',
            ],
        ];
        return $options;
    }
}