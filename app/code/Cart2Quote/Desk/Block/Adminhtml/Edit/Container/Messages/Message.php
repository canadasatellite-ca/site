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

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Container\Messages;

/**
 * Class Message
 */
class Message extends \Magento\Backend\Block\Template
{

    /**
     * Get the message name
     *
     * @return string
     */
    public function getName()
    {
        return $this->escapeHtml($this->getMessage()->getName());
    }

    /**
     * Get the message updated at value
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getMessage()->getUpdatedAt();
    }

    /**
     * Get the message content value
     *
     * @return string
     */
    public function getContent()
    {
        return nl2br($this->escapeHtml($this->getMessage()->getMessage()));
    }

    /**
     * Get the CSS classes for a single message container
     *
     * @return string
     */
    public function getMessageClasses()
    {
        return $this->getOwnerClass() . ' ' . $this->getIsPrivateClass() . ' ' . $this->getIsNewClass();
    }

    /**
     * Get the for the owner: customer or admin user.
     *
     * @return string
     */
    public function getOwnerClass()
    {
        $class = 'customer-message';
        if ($this->getMessage()->getUserId()) {
            $class = 'admin-message';
        }
        return $class;
    }

    /**
     * Get the class for an internal note.
     *
     * @return string
     */
    public function getIsPrivateClass()
    {
        $class = '';
        if ($this->getMessage()->getIsPrivate()) {
            $class = 'internal-note';
        }
        return $class;
    }

    /**
     * Get the new css class by the is_new data variable
     *
     * @return string
     */
    public function getIsNewClass()
    {
        $class = '';
        if ($this->getIsNew()) {
            $class = 'new-message';
        }
        return $class;
    }

    /**
     * Get the NEW notice for a new ticket.
     *
     * @return string
     */
    public function getNewHtml()
    {
        $html = '';
        if ($this->getIsNew()) {
            $html = '<span class="new-message-notice">' . __("NEW") . '</span>';
        }
        return $html;
    }
}
