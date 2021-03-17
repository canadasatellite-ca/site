<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\Customer;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\AbstractFormDataProvider;
use Aheadworks\AdvancedReviews\Model\Data\Extractor as DataExtractor;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\Customer
 * @codeCoverageIgnore
 */
class FormDataProvider extends AbstractFormDataProvider
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var EmailSubscriberManagementInterface
     */
    protected $emailSubscriberManagement;

    /**
     * @param ArrayManager $arrayManager
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Config $config
     * @param DataExtractor $dataExtractor
     * @param CustomerSession $customerSession
     * @param EmailSubscriberManagementInterface $emailSubscriberManagement
     */
    public function __construct(
        ArrayManager $arrayManager,
        DataObjectProcessor $dataObjectProcessor,
        Config $config,
        DataExtractor $dataExtractor,
        CustomerSession $customerSession,
        EmailSubscriberManagementInterface $emailSubscriberManagement
    ) {
        parent::__construct(
            $arrayManager,
            $dataObjectProcessor,
            $config,
            $dataExtractor
        );
        $this->customerSession = $customerSession;
        $this->emailSubscriberManagement = $emailSubscriberManagement;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscriberFormProviderPath()
    {
        return 'components/awArEmailSubscriberFormProvider';
    }

    /**
     * {@inheritdoc}
     */
    protected function getCurrentSubscriber()
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
