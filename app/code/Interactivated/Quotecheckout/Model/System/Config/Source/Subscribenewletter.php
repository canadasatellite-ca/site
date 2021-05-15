<?php

namespace Interactivated\Quotecheckout\Model\System\Config\Source;

class Subscribenewletter implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_ENABLE_CHECKED	= 2;
    const STATUS_ENABLE_UNCHECKED = 1;
    const STATUS_DISABLE = 0;

    function toOptionArray()
    {
        return [
            self::STATUS_ENABLE_CHECKED => __('Enable & Checked'),
            self::STATUS_ENABLE_UNCHECKED => __('Enable & UnChecked'),
            self::STATUS_DISABLE =>__('Disable')
        ];
    }
}
