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
 * @package     DeskEmail
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\DeskEmail\Model;

use Cart2Quote\Desk\Api\Data\TicketInterface;
use Magento\Customer\Api\Data\CustomerInterface as Customer;
use Cart2Quote\DeskEmail\Model\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Cart2Quote\Desk\Api\Data\MessageInterface;

/**
 * Class Sender
 */
abstract class Sender
{
    /**
     * Sender Builder Factory
     *
     * @var \Cart2Quote\DeskEmail\Model\SenderBuilderFactory
     */
    protected $_senderBuilderFactory;

    /**
     * Template Container
     *
     * @var Template
     */
    protected $_templateContainer;

    /**
     * Identity Container
     *
     * @var IdentityInterface
     */
    protected $_identityContainer;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Address
     *
     * @var \Magento\Customer\Model\Address
     */
    protected $_address;

    /**
     * Store Repository Interface
     *
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $_storeRepositoryInterface;

    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Class Sender constructor
     *
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param \Cart2Quote\DeskEmail\Model\SenderBuilderFactory $senderBuilderFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Model\Address $address
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface
     * @param \Magento\Store\Api\StoreRepositoryInterface $storeRepositoryInterface
     * @param \Magento\Store\Model\Store $store
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        \Cart2Quote\DeskEmail\Model\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Address $address,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepositoryInterface,
        \Magento\Store\Model\Store $store
    ) {
        $this->_templateContainer = $templateContainer;
        $this->_identityContainer = $identityContainer;
        $this->_senderBuilderFactory = $senderBuilderFactory;
        $this->_logger = $logger;
        $this->_address = $address;
        $this->_addressRepositoryInterface = $addressRepositoryInterface;
        $this->_storeRepositoryInterface = $storeRepositoryInterface;
        $this->_store = $store;
    }

    /**
     * Checks if the email is allowed to be send.
     * If true then the email will be send.
     *
     * @param TicketInterface $ticket
     * @param MessageInterface $message
     * @return bool
     */
    protected function checkAndSend(TicketInterface $ticket, MessageInterface $message)
    {
        $this->_identityContainer->setStore($this->getStore($ticket));
        if (!$this->_identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($message, $ticket);

        /** @var SenderBuilder $sender */
        $sender = $this->getSender();

        try {
            $sender->send();
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }

        return true;
    }

    /**
     * Prepare the template
     *
     * @param MessageInterface $message
     * @param TicketInterface $ticket
     *
     * @return $this
     */
    protected function prepareTemplate(MessageInterface $message, TicketInterface $ticket)
    {
        $this->_templateContainer->setTemplateOptions($this->getTemplateOptions());

        $templateId = $this->_identityContainer->getTemplateId();

        $this->_identityContainer->setMainName($message->getName());
        $this->_identityContainer->setMainEmail($message->getEmail());
        $this->_templateContainer->setTemplateId($templateId);

        return $this;
    }

    /**
     * Get the sender
     *
     * @return Sender
     */
    protected function getSender()
    {
        return $this->_senderBuilderFactory->create(
            [
                'templateContainer' => $this->_templateContainer,
                'identityContainer' => $this->_identityContainer,
            ]
        );
    }

    /**
     * Get the template options
     *
     * @return array
     */
    protected function getTemplateOptions()
    {
        return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->_identityContainer->getStore()->getStoreId()
        ];
    }

    /**
     * Get shipping address formatted
     *
     * @param Customer $customer
     * @return string|null
     */
    protected function getFormattedShippingAddress(Customer $customer)
    {
        return $this->getAddressFormatted($this->loadAddressData($customer->getDefaultShipping()));
    }

    /**
     * Get billing address formatted
     *
     * @param Customer $customer
     * @return string|null
     */
    protected function getFormattedBillingAddress(Customer $customer)
    {
        return $this->getAddressFormatted($this->loadAddressData($customer->getDefaultBilling()));
    }

    /**
     * Get address formatted
     *
     * @param \Magento\Customer\Api\Data\AddressInterface|false $addressData
     * @return null|string
     */
    protected function getAddressFormatted($addressData)
    {
        if (!empty($addressData)) {
            return $this->_address->updateData($addressData)->format('html');
        }
        return false;
    }

    /**
     * Load the address data
     *
     * @param int $addressId
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    protected function loadAddressData($addressId)
    {
        if (!empty($addressId)) {
            return $this->_addressRepositoryInterface->getById($addressId);
        } else {
            return false;
        }
    }

    /**
     * Get Store by Ticket
     *
     * @param TicketInterface $ticket
     * @return \Magento\Store\Model\Store
     */
    protected function getStore(TicketInterface $ticket)
    {
        if (!$this->_store->hasData('id')) {
            $this->_store->load($ticket->getStoreId());
        }
        return $this->_store;
    }
}
