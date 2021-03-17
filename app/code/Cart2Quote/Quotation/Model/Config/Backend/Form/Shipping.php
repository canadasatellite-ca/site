<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Form;

/**
 * Class Shipping
 *
 * @package Cart2Quote\Quotation\Model\Config\Backend\Form
 */
class Shipping extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Form\Shipping {
        getAllOptions as private traitGetAllOptions;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Carrier\QuotationShipping
     */
    private $shipping;

    /**
     * Shipping constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Carrier\QuotationShipping $shipping
     */
    public function __construct(\Cart2Quote\Quotation\Model\Carrier\QuotationShipping $shipping)
    {
        $this->shipping = $shipping;
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
