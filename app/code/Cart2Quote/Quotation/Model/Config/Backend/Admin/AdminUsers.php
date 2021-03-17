<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Backend\Admin;

/**
 * Class AdminUsers
 *
 * @package Cart2Quote\Quotation\Model\Config\Backend\Admin
 */
class AdminUsers implements \Magento\Framework\Option\ArrayInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Backend\Admin\AdminUsers {
        toOptionArray as private traitToOptionArray;
        getUserList as private traitGetUserList;
    }

    /**
     * User Collection
     *
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $userCollection = null;

    /**
     * AdminUsers constructor
     *
     * @param \Magento\User\Model\ResourceModel\User\Collection $userCollection
     */
    public function __construct(\Magento\User\Model\ResourceModel\User\Collection $userCollection)
    {
        $this->userCollection = $userCollection;
    }

    /**
     * Convert user list to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }

    /**
     * Get a list of admin users
     *
     * @return array
     */
    protected function getUserList()
    {
        return $this->traitGetUserList();
    }
}
