<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend\Admin;
/**
 * Trait AdminUsers
 *
 * @package Cart2Quote\Quotation\Model\Config\Backend\Admin
 */
trait AdminUsers
{
    /**
     * Convert user list to option array
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getUserList();
		}
	}
    /**
     * Get a list of admin users
     *
     * @return array
     */
    private function getUserList()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->userCollection->addOrder('lastname', 'ASC');
        $users = [];
        foreach ($this->userCollection as $user) {
            $values['value'] = $user->getId();
            $values['label'] = $user->getName();
            $users[] = $values;
        }
        return $users;
		}
	}
}
