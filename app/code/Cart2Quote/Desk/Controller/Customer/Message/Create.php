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

namespace Cart2Quote\Desk\Controller\Customer\Message;

use Magento\Framework\App\Action\Context;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Create
 */
class Create extends \Magento\Framework\App\Action\Action
{
    /**
     * JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageInterfaceFactory
     */
    protected $_messageInterfaceFactory;

    /**
     * Cart2Quote ticket message
     *
     * @var \Cart2Quote\Desk\Api\MessageRepositoryInterface
     */
    protected $_messageRepositoryInterface;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * Class Create constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory
     * @param \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory,
        \Cart2Quote\Desk\Api\MessageRepositoryInterface $messageRepositoryInterface,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        Context $context
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_messageInterfaceFactory = $messageInterfaceFactory;
        $this->_messageRepositoryInterface = $messageRepositoryInterface;
        $this->_currentCustomer = $currentCustomer;
        parent::__construct($context);
    }

    /**
     * Render my tickets
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $ticketId = $this->getRequest()->getParam('id');
        $message = $this->getRequest()->getParam('message');
        if (is_array($message) && isset($message['message'])) {
            $message = $message['message'];
        }
        $customerId = $this->_currentCustomer->getCustomerId();

        $messageData = $this->_messageInterfaceFactory->create();
        $messageData->setTicketId($ticketId);
        $messageData->setCustomerId($customerId);
        $messageData->setMessage($message);

        $resultJson = $this->_resultJsonFactory->create();

        try{
            $httpCode = 200;
            $response = [
                'errors' => false,
                'message' => __("Message send.")
            ];
            $this->_messageRepositoryInterface->save($messageData);
        }catch(Exception $e){
            $httpCode = 400;
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }

        return $resultJson->setHttpResponseCode($httpCode)->setData($response);
    }
}
