<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\Customer\Account\Dashboard;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Info
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\Customer\Account\Dashboard
 */
class Info implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var EmailSubscriberManagementInterface
     */
    protected $emailSubscriberManagement;

    /**
     * @param UrlInterface $urlBuilder
     * @param CustomerSession $customerSession
     * @param EmailSubscriberManagementInterface $emailSubscriberManagement
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CustomerSession $customerSession,
        EmailSubscriberManagementInterface $emailSubscriberManagement
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
        $this->emailSubscriberManagement = $emailSubscriberManagement;
    }

    /**
     * Get url to edit notifications
     *
     * @return string
     */
    public function getEditNotificationsUrl()
    {
        return $this->urlBuilder->getUrl(
            'aw_advanced_reviews/customer'
        );
    }

    /**
     * Get is subscriber receive notifications
     *
     * @param SubscriberInterface|null $subscriber
     * @return bool
     */
    public function getIsSubscriberReceiveNotifications($subscriber)
    {
        $isSubscriberReceiveNotifications = false;
        if ($subscriber) {
            $isSubscriberReceiveNotifications = $subscriber->getIsReviewReminderEmailEnabled()
                || $subscriber->getIsNewCommentEmailEnabled()
                || $subscriber->getIsReviewApprovedEmailEnabled();
        }
        return $isSubscriberReceiveNotifications;
    }

    /**
     * Retrieve current email subscriber
     *
     * @return SubscriberInterface|null
     */
    public function getCurrentSubscriber()
    {
        $currentCustomerId = $this->customerSession->getCustomerId();
        try {
            $subscriber = $this->emailSubscriberManagement->getSubscriberByCustomerId($currentCustomerId);
        } catch (LocalizedException $exception) {
            $subscriber = null;
        }
        return $subscriber;
    }
}
