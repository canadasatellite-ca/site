<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Service\Email;

use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Finder as SubscriberFinder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class SubscriberService
 *
 * @package Aheadworks\AdvancedReviews\Model\Service\Email
 */
class SubscriberService implements EmailSubscriberManagementInterface
{
    /**
     * @var EmailSubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var SubscriberInterfaceFactory
     */
    private $subscriberFactory;

    /**
     * @var SubscriberFinder
     */
    private $subscriberFinder;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param EmailSubscriberRepositoryInterface $subscriberRepository
     * @param SubscriberInterfaceFactory $subscriberFactory
     * @param SubscriberFinder $subscriberFinder
     * @param DataObjectHelper $dataObjectHelper
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        EmailSubscriberRepositoryInterface $subscriberRepository,
        SubscriberInterfaceFactory $subscriberFactory,
        SubscriberFinder $subscriberFinder,
        DataObjectHelper $dataObjectHelper,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->subscriberFinder = $subscriberFinder;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createSubscriber($email, $websiteId)
    {
        $existingSubscriber = $this->subscriberFinder->find($email, $websiteId);
        if ($existingSubscriber) {
            throw new LocalizedException(__('Subscriber already exists'));
        }
        /** @var SubscriberInterface $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber
            ->setEmail($email)
            ->setWebsiteId($websiteId)
            ->setIsReviewApprovedEmailEnabled(true)
            ->setIsNewCommentEmailEnabled(true)
            ->setIsReviewReminderEmailEnabled(true);

        $savedSubscriber = $this->subscriberRepository->save($subscriber);
        return $savedSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriber($email, $websiteId)
    {
        try {
            $customer = $this->customerRepository->get($email, $websiteId);
        } catch (LocalizedException $exception) {
            $customer = null;
        }

        if ($customer) {
            $subscriber = $this->getSubscriberByCustomer($customer);
        } else {
            $subscriber = $this->getSubscriberByEmail($email, $websiteId);
        }

        return $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscriberByCustomerId($customerId)
    {
        $customer = $this->customerRepository->getById($customerId);
        $subscriber = $this->getSubscriberByCustomer($customer);
        return $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function updateSubscriber(SubscriberInterface $subscriber)
    {
        $updatedSubscriber = $this->prepareUpdatedSubscriber($subscriber);
        $savedSubscriber = $this->subscriberRepository->save($updatedSubscriber);
        return $savedSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSubscriber(SubscriberInterface $subscriber)
    {
        $result = $this->subscriberRepository->delete($subscriber);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSubscriberById($subscriberId)
    {
        $subscriber = $this->subscriberRepository->getById($subscriberId);
        $result = $this->deleteSubscriber($subscriber);
        return $result;
    }

    /**
     * Retrieve subscriber by customer instance
     *
     * @param CustomerInterface $customer
     * @return SubscriberInterface
     * @throws LocalizedException
     */
    private function getSubscriberByCustomer($customer)
    {
        return $this->getSubscriberByEmail(
            $customer->getEmail(),
            $customer->getWebsiteId()
        );
    }

    /**
     * Retrieve subscriber by email
     *
     * @param string $email
     * @param int $websiteId
     * @return SubscriberInterface
     * @throws LocalizedException
     */
    private function getSubscriberByEmail($email, $websiteId)
    {
        $existingSubscriber = $this->subscriberFinder->find($email, $websiteId);
        if (empty($existingSubscriber)) {
            $newSubscriber = $this->createSubscriber($email, $websiteId);
            return $newSubscriber;
        } else {
            return $existingSubscriber;
        }
    }

    /**
     * Merges edited subscriber object with existing
     *
     * @param SubscriberInterface $editedSubscriber
     * @return SubscriberInterface
     * @throws LocalizedException
     */
    private function prepareUpdatedSubscriber($editedSubscriber)
    {
        $subscriberToMerge = $this->subscriberRepository->getById($editedSubscriber->getId());
        $this->dataObjectHelper->mergeDataObjects(
            SubscriberInterface::class,
            $subscriberToMerge,
            $editedSubscriber
        );
        return $subscriberToMerge;
    }
}
