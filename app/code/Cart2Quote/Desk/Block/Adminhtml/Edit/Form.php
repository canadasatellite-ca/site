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

/**
 * Adminhtml Cart2Quote Edit Form
 */
namespace Cart2Quote\Desk\Block\Adminhtml\Edit;

/**
 * Class Form
 */
class Form extends \Magento\Backend\Block\Template
{
    /**
     * Ticket
     *
     * @var \Cart2Quote\Desk\Model\Ticket
     */
    protected $_ticket = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Class Form constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get the ticket from registry
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function getTicket()
    {
        if (!$this->_ticket) {
            $this->_ticket = $this->_coreRegistry->registry('ticket_data');
        }

        return $this->_ticket;
    }

    /**
     * Get the ticket id
     *
     * @return int
     */
    public function getTicketId()
    {
        if ($this->getTicket()) {
            return $this->getParentBlock()->getTicket()->getId();
        } else {
            return 0;
        }
    }
}
