<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Source;

/**
 * Class GuestRequest
 * @package Cart2Quote\Quotation\Model\Config\Source
 */
class GuestRequest implements \Magento\Framework\Data\OptionSourceInterface
{

    use \Cart2Quote\Features\Traits\Model\Config\Source\GuestRequest {
        toOptionArray as private traitToOptionArray;
        toArray as private traitToArray;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->traitToArray();
    }
}