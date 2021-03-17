<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Subscriber;

use Aheadworks\AdvancedReviews\Controller\AbstractPostAction;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as EmailSubscriberResolver;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink\FormDataProvider;

/**
 * Class UpdateByCode
 *
 * @package Aheadworks\AdvancedReviews\Controller\Subscriber
 */
class UpdateByCode extends AbstractPostAction
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
     * @var EmailSubscriberResolver
     */
    private $subscriberResolver;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param Context $context
     * @param FormKeyValidator $formKeyValidator
     * @param EmailSubscriberManagementInterface $subscriberManagement
     * @param ProcessorInterface $subscriberPostDataProcessor
     * @param EmailSubscriberResolver $subscriberResolver
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        EmailSubscriberManagementInterface $subscriberManagement,
        ProcessorInterface $subscriberPostDataProcessor,
        EmailSubscriberResolver $subscriberResolver,
        DataObjectHelper $dataObjectHelper
    ) {
        parent::__construct(
            $context,
            $formKeyValidator
        );
        $this->subscriberManagement = $subscriberManagement;
        $this->subscriberPostDataProcessor = $subscriberPostDataProcessor;
        $this->subscriberResolver = $subscriberResolver;
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
     * @return SubscriberInterface|null
     */
    protected function getCurrentSubscriber()
    {
        $securityCode = $this->getRequest()->getParam(FormDataProvider::SECURITY_CODE_REQUEST_PARAM_KEY, '');
        $subscriber = $this->subscriberResolver->getBySecurityCode($securityCode);
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
     * Retrieve redirect to the subscriber edit page
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
