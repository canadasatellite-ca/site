<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Customer;

use Aheadworks\AdvancedReviews\Controller\AbstractCustomerPostAction;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class UpdateSubscriber
 *
 * @package Aheadworks\AdvancedReviews\Controller\Customer
 */
class UpdateSubscriber extends AbstractCustomerPostAction
{
    /**
     * @var EmailSubscriberManagementInterface
     */
    private $subscriberManagement;

    /**
     * @var ProcessorInterface
     */
    private $subscriberPostDataProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param EmailSubscriberManagementInterface $subscriberManagement
     * @param ProcessorInterface $subscriberPostDataProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        FormKeyValidator $formKeyValidator,
        EmailSubscriberManagementInterface $subscriberManagement,
        ProcessorInterface $subscriberPostDataProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $formKeyValidator
        );
        $this->subscriberManagement = $subscriberManagement;
        $this->subscriberPostDataProcessor = $subscriberPostDataProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        if (!empty($postData)) {
            try {
                $this->validate();
                $currentSubscriber = $this->getCurrentSubscriber();
                if ($currentSubscriber) {
                    $preparedData = $this->subscriberPostDataProcessor->process($postData);
                    $this->performUpdate($currentSubscriber, $preparedData);
                    $this->messageManager->addSuccessMessage(__('You saved your notifications settings.'));
                } else {
                    $this->messageManager->addErrorMessage(__('Something went wrong while saving settings.'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $this->getPreparedRedirect();
    }

    /**
     * Retrieve current email subscriber
     *
     * @return SubscriberInterface
     * @throws LocalizedException
     */
    protected function getCurrentSubscriber()
    {
        $currentCustomerId = $this->customerSession->getCustomerId();
        $subscriber = $this->subscriberManagement->getSubscriberByCustomerId($currentCustomerId);
        return $subscriber;
    }

    /**
     * Perform email subscriber update
     *
     * @param SubscriberInterface $subscriber
     * @param array $data
     * @return SubscriberInterface
     * @throws LocalizedException
     */
    protected function performUpdate($subscriber, $data)
    {
        $this->dataObjectHelper->populateWithArray(
            $subscriber,
            $data,
            SubscriberInterface::class
        );
        return $this->subscriberManagement->updateSubscriber($subscriber);
    }

    /**
     * Retrieve redirect to the customer reviews page
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
