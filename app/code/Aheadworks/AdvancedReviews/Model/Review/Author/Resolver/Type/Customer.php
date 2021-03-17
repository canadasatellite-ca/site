<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\ResolverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class Customer
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type
 */
class Customer implements ResolverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        UrlInterface $urlBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendLabel($customerId)
    {
        $authorName = __('Not specified');
        $customer = $this->getCustomer($customerId);
        if ($customer) {
            $authorName = $customer->getFirstname() . ' ' . $customer->getLastname();
        }
        return $authorName;
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendUrl($customerId)
    {
        $authorUrl = '';
        $customer = $this->getCustomer($customerId);
        if ($customer) {
            $authorUrl = $this->urlBuilder->getUrl(
                'customer/index/edit',
                ['id' => $customer->getId()]
            );
        }
        return $authorUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getName($review)
    {
        $authorName = null;
        $customer = $this->getCustomer($review->getCustomerId());
        if ($customer) {
            $authorName = $customer->getFirstname();
        }
        return $authorName;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail($review)
    {
        $authorEmail = null;
        $customer = $this->getCustomer($review->getCustomerId());
        if ($customer) {
            $authorEmail = $customer->getEmail();
        }
        return $authorEmail;
    }

    /**
     * Retrieve customer by id
     *
     * @param int|null $customerId
     * @return CustomerInterface|null
     */
    private function getCustomer($customerId)
    {
        $customer = null;
        if (!empty($customerId)) {
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (LocalizedException $e) {
                $customer = null;
            }
        }
        return $customer;
    }
}
