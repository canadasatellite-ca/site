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

namespace Cart2Quote\Desk\Model\Ticket;

use Magento\Catalog\Model\Product;

/**
 * Message model
 */
class Message extends \Magento\Framework\Model\AbstractModel
{
    const OWNER_TYPE_USER = 'user';
    const OWNER_TYPE_CUSTOMER = 'customer';

    /**
     * Message Interface Factory
     *
     * @var \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory
     */
    protected $_messageInterfaceFactory;

    /**
     * Data Object Processor
     *
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $_dataObjectProcessor;

    /**
     * Data Object Helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $_dataObjectHelper;

    /**
     * Customer Repository Interface
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * Customer Name API Interface
     *
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    protected $_customerName;

    /**
     * User Model
     *
     * @var \Magento\User\Model\User
     */
    protected $_user;

    /**
     * Customer API Interface
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $_customer;

    /**
     * Class message constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message $resource
     * @param \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection $resourceCollection
     * @param \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Api\CustomerNameGenerationInterface $customerName
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\User\Model\User $user
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message $resource,
        \Cart2Quote\Desk\Model\ResourceModel\Ticket\Message\Collection $resourceCollection,
        \Cart2Quote\Desk\Api\Data\MessageInterfaceFactory $messageInterfaceFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Api\CustomerNameGenerationInterface $customerName,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        \Magento\User\Model\User $user,
        array $data = []
    ) {
        $this->_messageInterfaceFactory = $messageInterfaceFactory;
        $this->_dataObjectProcessor = $dataObjectProcessor;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerName = $customerName;
        $this->_user = $user;
        $this->_customer = $customer;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Desk\Model\ResourceModel\Ticket\Message');
    }

    /**
     * Retrieve Message model with message data
     *
     * @return \Cart2Quote\Desk\Api\Data\MessageInterface
     */
    public function getDataModel()
    {
        $messageData = $this->getData();
        $messageDataObject = $this->_messageInterfaceFactory->create();
        $this->_dataObjectHelper->populateWithArray(
            $messageDataObject,
            $messageData,
            '\Cart2Quote\Desk\Api\Data\MessageInterface'
        );
        $messageDataObject->setId($this->getId());
        return $messageDataObject;
    }

    /**
     * Update message data
     *
     * @param \Cart2Quote\Desk\Api\Data\MessageInterface $message
     * @return $this
     */
    public function updateData(\Cart2Quote\Desk\Api\Data\MessageInterface $message)
    {
        $messageDataAttributes = $this->_dataObjectProcessor->buildOutputDataArray(
            $message,
            '\Cart2Quote\Desk\Api\Data\MessageInterface'
        );

        foreach ($messageDataAttributes as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $customAttributes = $message->getCustomAttributes();
        if ($customAttributes !== null) {
            foreach ($customAttributes as $attribute) {
                $this->setDataUsingMethod($attribute->getAttributeCode(), $attribute->getValue());
            }
        }

        $messageId = $message->getId();
        if ($messageId) {
            $this->setId($messageId);
        }

        return $this;
    }

    /**
     * Get the type message owner depending on the customer_id or user_id
     *
     * @return string
     */
    public function getOwnerType()
    {
        if ($this->getUserId()) {
            return self::OWNER_TYPE_USER;
        } else {
            return self::OWNER_TYPE_CUSTOMER;
        }
    }

    /**
     * Loads the name depending on the user_id or the customer_id
     *
     * @return $this
     */
    public function loadName()
    {
        if ($this->getOwnerType() == self::OWNER_TYPE_CUSTOMER && $this->getCustomer()->getId() != null) {
            $this->setName($this->_customerName->getCustomerName($this->_customer));
        } elseif ($this->getOwnerType() == self::OWNER_TYPE_USER) {
            if ($this->getUserFirstname() == null && $this->getUserLastname() == null) {
                $this->setName(
                    $this->getUser()->getFirstname() . ' ' .
                    $this->getUser()->getLastname()
                );
            } else {
                $this->setName($this->getUserFirstname() . ' ' . $this->getUserLastname());
            }
        }
        return $this;
    }

    /**
     * Loads the email depending on the user_id or the customer_id
     *
     * @return $this
     */
    public function loadEmail()
    {
        if ($this->getEmail() == null) {
            if ($this->getOwnerType() == self::OWNER_TYPE_USER) {
                $this->setEmail($this->getUser()->getEmail());
            } else {
                $this->setEmail($this->getCustomer()->getEmail());
            }
        }
        return $this;
    }

    /**
     * Get the user data
     *
     * @return \Magento\User\Model\User
     */
    public function getUser()
    {
        if ($this->_user->getId() == null && $this->getOwnerType() == self::OWNER_TYPE_USER) {
            $this->_user->load($this->getUserId());
        }
        return $this->_user;
    }

    /**
     * Get the customer data
     *
     * @return \Magento\User\Model\User
     */
    public function getCustomer()
    {
        if ($this->_customer->getId() == null && $this->getOwnerType() == self::OWNER_TYPE_CUSTOMER) {
            $this->_customer = $this->_customerRepositoryInterface->getById($this->getCustomerId());
        }
        return $this->_customer;
    }

    /**
     * Load name and email on the message
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->loadName();
        $this->loadEmail();
        return parent::_afterLoad();
    }
}
