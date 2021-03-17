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

namespace Cart2Quote\Desk\Block\Adminhtml\Edit\Overwrite;

/**
 * Split button widget
 *
 * @method array getOptions()
 * @method string getButtonClass()
 * @method string getClass()
 * @method string getLabel()
 * @method string getTitle()
 * @method bool getDisabled()
 * @method string getStyle()
 * @method array getDataAttribute()
 */
class SplitButton extends \Magento\Backend\Block\Widget\Button\SplitButton
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Class SplitButton constructor
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
     * Retrieve button attributes html
     *
     * @return string
     */
    public function getButtonAttributesHtml()
    {
        $disabled = $this->getDisabled() ? 'disabled' : '';
        $title = $this->getTitle();
        if (!$title) {
            $title = $this->getLabel();
        }
        $classes = [];
        $classes[] = 'action-default';
        $classes[] = 'primary';

        if ($this->getClass()) {
            $classes[] = $this->getClass();
        }
        if ($disabled) {
            $classes[] = $disabled;
        }
        $attributes = [
            'id' => $this->getId() . '-button',
            'title' => $title,
            'class' => join(' ', $classes),
            'disabled' => $disabled,
            'style' => $this->getStyle(),
            'onclick' => $this->getOnClick() // added to original
         ];

        if ($this->getDataAttribute()) {
            $this->_getDataAttributes($this->getDataAttribute(), $attributes);
        }

        $html = $this->_getAttributesString($attributes);
        $html .= $this->getUiId();

        return $html;
    }

    /**
     * Get the onclick event for the default ticket submit button
     *
     * @return string
     */
    public function getOnClick()
    {
        $ticket = $this->_coreRegistry->registry('ticket_data');
        if ($ticket) {
            $ticketId = $ticket->getId();
            $statusId = $ticket->getStatusId();
        } else {
            $ticketId = 0;
            $statusId = 1;
        }
        $onClick =
            "document.getElementById('edit_form').action = " .
            "'{$this->_getTicketSubmitUrl($ticketId, $statusId)}';" .
            " document.getElementById('edit_form').submit();";

        return $onClick;
    }

    /**
     * Get the Ticket submit url by specified status type
     *
     * @param int $ticketId
     * @param int $statusId
     * @return string
     */
    protected function _getTicketSubmitUrl($ticketId, $statusId)
    {
        return $this->getUrl(
            '*/*/save',
            ['id' => $ticketId, 'status_id' => $statusId]
        );
    }
}


