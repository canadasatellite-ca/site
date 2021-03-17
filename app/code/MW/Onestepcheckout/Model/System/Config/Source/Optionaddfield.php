<?php

namespace MW\Onestepcheckout\Model\System\Config\Source;

class Optionaddfield implements \Magento\Framework\Option\ArrayInterface
{
    const STATUS_OPTIONAL	= 1;
    const STATUS_REQUIRED	= 2;
    const STATUS_DISABLED	= 0;

    public function toOptionArray()
    {
        return [
            self::STATUS_OPTIONAL => __('Optional'),
            self::STATUS_REQUIRED => __('Required'),
            self::STATUS_DISABLED => __('Disable')
        ];
    }
}
