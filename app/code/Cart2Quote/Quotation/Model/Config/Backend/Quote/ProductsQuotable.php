<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Quote;

/**
 * Backend model for products quotable by default setting
 */
class ProductsQuotable extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Quote\ProductsQuotable {
        getAllOptions as private traitGetAllOptions;
        getIndexOptionText as private traitGetIndexOptionText;
    }

    const QUOTABLE = 'cart2quote_quotation/global/quotable';
    /**
     * Add extra option value
     */
    const VALUE_CUSTOMERGROUP = 2;

    /**
     * Retrieve all options array ( rewritten from parent )
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->traitGetAllOptions();
    }

    /**
     * Get a text for index option value ( rewritten from parent )
     *
     * @param  string|int $value
     * @return string|bool
     */
    public function getIndexOptionText($value)
    {
        return $this->traitGetIndexOptionText($value);
    }
}
