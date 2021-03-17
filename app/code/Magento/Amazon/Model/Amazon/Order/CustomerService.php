<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Order;

use Magento\Amazon\Api\CustomerManagementInterface;
use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Store\Model\Store;

/**
 * Class CustomerService
 */
class CustomerService implements CustomerManagementInterface
{
    /** @var CustomerInterfaceFactory $customerFactory */
    protected $customerFactory;
    /** @var CustomerRepositoryInterface $customerRepository */
    protected $customerRepository;

    /**
     * Constructor
     *
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Create Magento customer account (if applicable)
     *
     * @param OrderInterface $marketplaceOrder
     * @param Store $store
     * @return CustomerInterfaceFactory
     */
    public function create(OrderInterface $marketplaceOrder, Store $store)
    {
        /** @var int */
        $websiteId = $store->getWebsiteId();
        /** @var string */
        $email = $marketplaceOrder->getOrderId() . '@amazon.com';
        /** @var string */
        $name = explode(' ', $marketplaceOrder->getBuyerName(), 2);

        /** @var array */
        $buyer = [
            'firstname' => (isset($name[0])) ? $name[0] : 'N/A',
            'lastname' => (isset($name[1])) ? $name[1] : 'N/A'
        ];

        /** @var array */
        $customerData = [
            'firstname' => $buyer['firstname'],
            'lastname' => $buyer['lastname'],
            'email' => ($marketplaceOrder->getBuyerEmail()) ? $marketplaceOrder->getBuyerEmail() : $email
        ];

        try {
            $customer = $this->customerRepository->get($customerData['email'], $websiteId);
        } catch (NoSuchEntityException $e) {
            /** @var CustomerInterfaceFactory $customer */
            $customer = $this->customerFactory->create();

            // create new customer account
            $customer->setWebsiteId($websiteId)
                ->setStoreId($store->getId())
                ->setFirstname($customerData['firstname'])
                ->setLastname($customerData['lastname'])
                ->setEmail($customerData['email']);

            try {
                $customer = $this->customerRepository->save($customer);
            } catch (AlreadyExistsException $e) {
                return $customer;
            } catch (InputException $e) {
                return $customer;
            } catch (InputMismatchException $e) {
                return $customer;
            }
        } catch (LocalizedException $e) {
            return $this->customerFactory->create();
        }

        return $customer;
    }
}
