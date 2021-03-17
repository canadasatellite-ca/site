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

namespace Cart2Quote\Desk\Controller\Ticket;

use Exception;
use \Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Review\Model\Review;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Create
 */
class Create extends \Magento\Framework\App\Action\Action
{
    const DEFAULT_ERROR_MESSAGE = 'We can\'t process your ticket right now. Please try again later.';
    const PRODUCT_SUCCESS_MESSAGE = 'Thank you for your question about %s';
    const DEFAULT_SUCCESS_MESSAGE = 'Thank you for reaching out to us. ';
    const PRODUCT_QUESTION = 'A question about product: %s';
    const LIST_MESSAGE_BLOCK = 'ticket.message';

    /**
     * Is new flag
     *
     * @var bool
     */
    protected $_isNew;

    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Customer model
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $_ticketSession;

    /**
     * Catalog catgory model
     *
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * Desk model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\TicketRepositoryInterface
     */
    protected $_ticketRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory
     */
    protected $_ticketFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $_messageRepositoryInterface;

    /**
     * Cart2Quote ticketFactory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $_messageFactory;

    /**
     * Class Create constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Session\Generic $ticketSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Session\Generic $ticketSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Cart2Quote\Desk\Api\TicketRepositoryInterface $ticketRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\TicketInterfaceFactory $ticketFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerSession = $customerSession;
        $this->_ticketSession = $ticketSession;
        $this->_categoryRepository = $categoryRepository;
        $this->_logger = $logger;
        $this->_productRepository = $productRepository;
        $this->_ticketRepositoryInterface = $ticketRepositoryInterface;
        $this->_ticketFactory = $ticketFactory;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_messageFactory = $messageFactory;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customer;

        parent::__construct($context);
    }

    /**
     * Submit new ticket action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $layout = $this->resultFactory->create(ResultFactory::TYPE_PAGE)->getLayout();
        $this->_isNew = $this->getRequest()->getParam('ticket_id', 0) == 0;
        $postData = $this->getRequest()->getParams();
        $this->_ticketSession->setFormData($postData);
        $response = [
            'errors' => true,
            'ticket_id' => 0,
            'message_id' => 0
        ];

        try{
            $customer = $this->getCustomer();
            $ticket = $this->getTicket($customer->getId());
            $message = $this->_saveMessage($ticket);

            $this->_setSuccessMessage($layout, $ticket);

            if ($this->_isNew && $message && $ticket) {
                $this->dispatchNewTicketEvent($message, $ticket);
            }

            if (isset($postData['render_message']) && $postData['render_message']) {
                $response['message_html'] = $this->renderMessage($layout, $message);
            }

            $response['ticket_id'] = $ticket->getId();
            $response['message_id'] = $message->getId();
            $response['errors'] = false;
            $httpCode = 200;
        }catch(Exception $e){
            $this->messageManager->addError($e->getMessage());
            $httpCode = 400;
        }

        $layout->initMessages();
        $response['messages'] = $layout->getMessagesBlock()->getGroupedHtml();

        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setHttpResponseCode($httpCode)->setData($response);
    }

    /**
     * Initialize and check product
     *
     * @return \Magento\Catalog\Model\Product|bool
     */
    protected function initProduct()
    {
        $this->_eventManager->dispatch('ticket_controller_product_init_before', ['controller_action' => $this]);
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $productId = (int)$this->getRequest()->getParam('id');

        $product = $this->loadProduct($productId);
        if (!$product) {
            return false;
        }

        if ($categoryId) {
            $category = $this->_categoryRepository->get($categoryId);
            $this->_coreRegistry->register('current_category', $category);
        }

        try {
            $this->_eventManager->dispatch('ticket_controller_product_init', ['product' => $product]);
            $this->_eventManager->dispatch(
                'ticket_controller_product_init_after',
                ['product' => $product, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->critical($e);
            return false;
        }

        return $product;
    }

    /**
     * Load product model with data by passed id.
     * Return false if product was not loaded or has incorrect status.
     *
     * @param int $productId
     * @return bool|CatalogProduct
     */
    protected function loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        if ($this->_coreRegistry->registry('current_product')) {
            $product = $this->_coreRegistry->registry('current_product');
        } elseif ($this->_coreRegistry->registry('product')) {
            $product = $this->_coreRegistry->registry('product');
        } else {
            try {
                $product = $this->_productRepository->getById($productId);
                if (!$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
                    throw new NoSuchEntityException();
                }
            } catch (NoSuchEntityException $noEntityException) {
                return false;
            }
            $this->_coreRegistry->register('current_product', $product);
            $this->_coreRegistry->register('product', $product);
        }

        return $product;
    }

    /**
     * Try to save the customer, if the customer already exists load the customer.
     *
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface
     */
    protected function _saveCustomer()
    {
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($this->_storeManager->getWebsite()->getId());

        if ($postData = $this->getRequest()->getParam('customer')) {
            if ($postData['firstname']) {
                $customer->setFirstname($postData['firstname']);
            }

            if ($postData['lastname']) {
                $customer->setLastname($postData['lastname']);
            }

            if ($postData['email']) {
                $customer->setEmail($postData['email']);
            }
        }

        try {
            $customer = $this->_customerRepositoryInterface->save($customer);
        } catch (AlreadyExistsException $e) {
            $customer = $this->_customerRepositoryInterface->get(
                $postData['email'],
                $this->_storeManager->getWebsite()->getId()
            );
        }
        return $customer;
    }

    /**
     * Save the ticket
     *
     * @param int $customerId
     * @return bool|\Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function _saveTicket($customerId)
    {
        $ticket = $this->_ticketFactory->create();
        $ticket->setStoreId($this->_storeManager->getStore()->getId());
        $ticket->setCustomerId($customerId);
        $ticket->setStatusId(1);

        if ($postData = $this->getRequest()->getParam('ticket')) {
            if ($postData['subject']) {
                $ticket->setSubject($postData['subject']);
            }
        }

        if ($product = $this->initProduct()) {
            $ticket->setSubject(__(sprintf(self::PRODUCT_QUESTION, $product->getName())));
        }

        $this->dispatchSaveTicketEventBefore($ticket);
        $ticket = $this->_ticketRepositoryInterface->save($ticket);
        $this->dispatchSaveTicketEventAfter($ticket);

        return $ticket;
    }

    /**
     * Save the message
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return bool|\Cart2Quote\Desk\Api\Data\MessageInterface
     */
    protected function _saveMessage(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $message = $this->_messageFactory->create();
        if ($postData = $this->getRequest()->getParam('message')) {
            if (isset($postData['message'])) {
                $message->setMessage($postData['message']);
            }
        }
        $message
            ->setTicketId($ticket->getId())
            ->setCustomerId($ticket->getCustomerId())
            ->setIsPrivate(false);

        $this->dispatchSaveMessageEventBefore($message, $ticket);
        $message = $this->_messageRepositoryInterface->save($message);
        $this->dispatchSaveMessageEventAfter($message, $ticket);
        return $message;
    }

    /**
     * Dispatch new ticket event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchNewTicketEvent(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_frontend_new_ticket',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save ticket after event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    private function dispatchSaveTicketEventAfter(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_frontend_save_ticket_after',
            ['ticket' => $ticket]
        );
    }

    /**
     * Dispatch save ticket before event
     *
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     * @return void
     */
    private function dispatchSaveTicketEventBefore(\Cart2Quote\Desk\Api\Data\TicketInterface $ticket)
    {
        $this->_eventManager->dispatch(
            'desk_frontend_save_ticket_before',
            ['ticket' => $ticket]
        );
    }

    /**
     * Dispatch save message after event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchSaveMessageEventAfter(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_frontend_save_message_after',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Dispatch save message before event
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return void
     */
    private function dispatchSaveMessageEventBefore(
        \Cart2Quote\Desk\Api\Data\MessageInterface $message,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $this->_eventManager->dispatch(
            'desk_frontend_save_message_before',
            [
                'message' => $message,
                'ticket' => $ticket
            ]
        );
    }

    /**
     * Set success message
     *
     * @param \Magento\Framework\View\Layout $layout
     * @param \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
     *
     * @return $this
     */
    protected function _setSuccessMessage(
        $layout,
        \Cart2Quote\Desk\Api\Data\TicketInterface $ticket
    ) {
        $state = 'updated';
        $url = $this->_url->getUrl('desk/customer/view', ['id' => $ticket->getId()]);
        if ($this->_isNew) {
            $state = 'created';
        }

        $layout->getMessagesBlock()->addSuccess(
            __(
                self::DEFAULT_SUCCESS_MESSAGE) . __("<a href=\"%1\">Ticket #%2 (%3) has been ".$state.'.</a>',
                $url,
                $ticket->getId(),
                $ticket->getSubject()
            )
        );

        return $this;
    }

    /**
     * Get the customer by session or save the new customer
     *
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface|\Magento\Customer\Model\Customer
     */
    protected function getCustomer()
    {
        if ($this->_customerSession->getCustomerId() == null) {
            $customer = $this->_saveCustomer();
            $this->_customerSession->setCustomerAsLoggedIn($this->_customer->updateData($customer));
        } else {
            $customer = $this->_customerSession->getCustomer();
        }

        return $customer;
    }

    /**
     * Get a single message HTML
     * The key set to the message indicates it's the first message in the list
     *
     * @param \Magento\Framework\View\Layout $layout
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return string
     */
    protected function renderMessage(
        $layout,
        \Cart2Quote\Desk\Api\Data\MessageInterface $message
    ) {
        $html = '';

        if ($block = $layout->getBlock(self::LIST_MESSAGE_BLOCK)) {
            $html = $block->setKey(0)->setMessage($message)->toHtml();
        }

        return $html;
    }

    /**
     * Create the ticket or load by ID
     *
     * @param int $customerId
     * @return bool|\Cart2Quote\Desk\Api\Data\TicketInterface
     */
    protected function getTicket($customerId)
    {
        if ($this->_isNew) {
            $ticket = $this->_saveTicket($customerId);
            return $ticket;
        } else {
            $ticket = $this->_ticketRepositoryInterface->getById($this->getRequest()->getParam('ticket_id', 0));
            return $ticket;
        }
    }
}