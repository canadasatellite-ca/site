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

namespace Cart2Quote\Desk\Controller\Adminhtml\Ticket;

use Magento\Backend\App\Action;
use Magento\Review\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Edit
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $_ticketFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $_messageFactory;

    /**
     * Cart2Quote data helper
     *
     * @var \Cart2Quote\Desk\Helper\Data
     */
    protected $_helperData;

    /**
     * CLass Edit constructor
     *
     * @param \Cart2Quote\Desk\Helper\Data $helperData
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param Action\Context $context
     */
    public function __construct(
        \Cart2Quote\Desk\Helper\Data $helperData,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        Action\Context $context
    ) {
        $this->_helperData = $helperData;
        $this->_ticketFactory = $ticketFactory;
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->_messageFactory = $messageFactory;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        parent::__construct($context);
    }

    /**
     * Render the edit page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        if (!$this->_helperData->getDeskEnabled()) {
            $this->getMessageManager()->addError(
                __("Customer Support Desk is currently disabled. " .
                    "Please contact your Magento administrator to enable Customer Support Desk again.")
            );
            return $this->_redirect('admin/dashboard');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Cart2Quote_Desk::desk_tickets');
        $editBlock = $resultPage->getLayout()->createBlock('Cart2Quote\Desk\Block\Adminhtml\Edit');
        $resultPage->addContent($editBlock);
        $resultPage->getConfig()->getTitle()->prepend($editBlock->getHeaderText());

        return $resultPage;
    }
}
