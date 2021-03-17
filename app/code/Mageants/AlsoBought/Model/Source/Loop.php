<?php
/**
 * @category   Mageants AlsoBought
 * @package    Mageants_AlsoBought
 * @copyright  Copyright (c) 2017 Mageants
 * @author     Mageants Team <support@Mageants.com>
 */
namespace Mageants\AlsoBought\Model\Source;

class Loop implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Yes'),
                'value' => 'true',
            ],
            [
                'label' => __('No'),
                'value' => 'false',
            ],
        ];
        return $options;
    }
}