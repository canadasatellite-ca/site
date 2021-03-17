<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket\Message;

/**
 * Message collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\Ticket\Message', 'Cart2Quote\Desk\Model\ResourceModel\Ticket\Message');
    }

    /**
     * Add user information to the message
     *
     * @return $this
     */
    public function innerJoinUser()
    {
        $this->getSelect()->joinLeft(
            ['au' => $this->getTable('admin_user')],
            'au.user_id = main_table.user_id',
            [
                'user_firstname' => 'au.firstname',
                'user_lastname' => 'au.lastname',
                'email' => 'au.email'
            ]
        );
        return $this;
    }

    /**
     * Add customer information to the message.
     *
     * @return $this
     */
    public function innerJoinCustomer()
    {
        $this->getSelect()->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'ce.entity_id = main_table.customer_id',
            [
                'email' => 'ce.email',
            ]
        );
        return $this;
    }

    /**
     * Add customer and user information.
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        $this
            ->innerJoinCustomer()
            ->innerJoinUser();

        return parent::_beforeLoad();
    }
}
