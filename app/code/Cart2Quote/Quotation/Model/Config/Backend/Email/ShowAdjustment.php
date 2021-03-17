<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Email;

/**
 * Backend model for showing quote adjustment setting
 * 'sales_email/general/async_sending'.
 */
class ShowAdjustment implements \Magento\Framework\Option\ArrayInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Email\ShowAdjustment {
        toOptionArray as private traitToOptionArray;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }
}
