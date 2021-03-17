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

namespace Cart2Quote\Desk\Model\ResourceModel\Ticket;

/**
 * Ticket priority resource model
 */
class Priority extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource status model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('desk_ticket_priority', 'priority_id');
    }

    /**
     * Load ticket priority by code
     *
     * @param \Cart2Quote\Desk\Model\Ticket\Priority $ticketPriority
     * @param string $priorityCode
     * @return $this
     */
    public function loadByCode(\Cart2Quote\Desk\Model\Ticket\Priority $ticketPriority, $priorityCode)
    {
        $this->load($ticketPriority, $priorityCode, 'code');
        return $this;
    }
}
