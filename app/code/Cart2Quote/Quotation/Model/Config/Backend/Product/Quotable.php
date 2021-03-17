<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Product;

/**
 * Backend model for products quotable field
 */
class Quotable extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Product\Quotable {
        getAllOptions as private traitGetAllOptions;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->traitGetAllOptions();
    }
}
