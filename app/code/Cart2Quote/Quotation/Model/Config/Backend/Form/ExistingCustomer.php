<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Form;

/**
 * Class ExistingCustomer
 *
 * @package Cart2Quote\Quotation\Model\Config\Backend\Form
 */
class ExistingCustomer extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Form\ExistingCustomer {
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
