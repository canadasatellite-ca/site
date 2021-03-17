<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Subscriber;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as EmailSubscriberResolver;
use Magento\Framework\Controller\Result\Redirect;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Layout\Processor\UnsubscribeLink\FormDataProvider;

/**
 * Class Edit
 *
 * @package Aheadworks\AdvancedReviews\Controller\Subscriber
 */
class Edit extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var EmailSubscriberResolver
     */
    protected $subscriberResolver;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param EmailSubscriberResolver $subscriberResolver
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        EmailSubscriberResolver $subscriberResolver
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->subscriberResolver = $subscriberResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->getCurrentSubscriber()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Reviews Notifications'));

            return $resultPage;
        } else {
            $this->messageManager->addErrorMessage(__('Unsubscribe link has already expired.'));
            return $this->getPreparedRedirect();
        }
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
     * Retrieve redirect to base page
     *
     * @return Redirect
     */
    protected function getPreparedRedirect()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = $this->_url->getBaseUrl();
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
