<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Quote;

/**
 * Backend model for products quotable by default setting
 * Class QuotableCustomerGroups
 * @package Cart2Quote\Quotation\Model\Config\Backend\Quote
 */
class QuotableCustomerGroups implements \Magento\Framework\Option\ArrayInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Quote\QuotableCustomerGroups {
        toOptionArray as private traitToOptionArray;
    }

    /**
     * path for quotable customer group config
     */
    const QUOTABLE_CUSTOMER_GROUP = 'cart2quote_quotation/global/quotable_customer_group';

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroupCollection;

    /**
     * QuotableCustomerGroups constructor.
     *
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection
     */
    public function __construct(\Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection)
    {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * To options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }
}
