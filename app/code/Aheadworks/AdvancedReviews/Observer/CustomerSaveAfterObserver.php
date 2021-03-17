<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Updater as EmailSubscriberUpdater;

/**
 * Class CustomerSaveAfterObserver
 *
 * @package Aheadworks\AdvancedReviews\Observer
 */
class CustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * @var EmailSubscriberUpdater
     */
    private $subscriberUpdater;

    /**
     * @param EmailSubscriberUpdater $subscriberUpdater
     */
    public function __construct(
        EmailSubscriberUpdater $subscriberUpdater
    ) {
        $this->subscriberUpdater = $subscriberUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var Event $event */
        $event = $observer->getEvent();
        /** @var CustomerInterface $savedCustomer */
        $savedCustomer = $event->getData('customer_data_object');
        /** @var CustomerInterface $originalCustomer */
        $originalCustomer = $event->getData('orig_customer_data_object');
        if ($savedCustomer && $originalCustomer) {
            $this->subscriberUpdater->processCustomerModifications($savedCustomer, $originalCustomer);
        }
    }
}
