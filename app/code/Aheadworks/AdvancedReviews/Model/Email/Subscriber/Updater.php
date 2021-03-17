<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\AdvancedReviews\Model\Data\Extractor as DataExtractor;

/**
 * Class Updater
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber
 */
class Updater
{
    /**
     * @var EmailSubscriberManagementInterface
     */
    private $subscriberManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataExtractor
     */
    protected $dataExtractor;

    /**
     * @param EmailSubscriberManagementInterface $subscriberManagement
     * @param LoggerInterface $logger
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataExtractor $dataExtractor
     */
    public function __construct(
        EmailSubscriberManagementInterface $subscriberManagement,
        LoggerInterface $logger,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        DataExtractor $dataExtractor
    ) {
        $this->subscriberManagement = $subscriberManagement;
        $this->logger = $logger;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * Process customer profile update
     *
     * @param CustomerInterface $savedCustomer
     * @param CustomerInterface $originalCustomer
     */
    public function processCustomerModifications($savedCustomer, $originalCustomer)
    {
        if (($savedCustomer->getEmail() !== $originalCustomer->getEmail())
            || ($savedCustomer->getWebsiteId() !== $originalCustomer->getWebsiteId())
        ) {
            try {
                $originalCustomerSubscriber = $this->subscriberManagement->getSubscriber(
                    $originalCustomer->getEmail(),
                    $originalCustomer->getWebsiteId()
                );
                $savedCustomerSubscriber = $this->subscriberManagement->getSubscriber(
                    $savedCustomer->getEmail(),
                    $savedCustomer->getWebsiteId()
                );

                $originalCustomerNotificationFlags = $this->dataExtractor->extractFields(
                    $this->dataObjectProcessor->buildOutputDataArray(
                        $originalCustomerSubscriber,
                        SubscriberInterface::class
                    )
                );
                $this->dataObjectHelper->populateWithArray(
                    $savedCustomerSubscriber,
                    $originalCustomerNotificationFlags,
                    SubscriberInterface::class
                );
                $this->subscriberManagement->updateSubscriber($savedCustomerSubscriber);
            } catch (LocalizedException $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }
    }
}
