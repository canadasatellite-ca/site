<?php

namespace CanadaSatellite\Theme\Controller\Subscriber;

use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Controller\Subscriber\NewAction as SubscriberNewAction;

class NewAction extends SubscriberNewAction
{

    /**
     * New subscription action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    function execute()
    {
        if ($this->getRequest()->getParam('success_v3')) {
            if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
                $email = (string)$this->getRequest()->getPost('email');

                try {
                    $this->validateEmailFormat($email);
                    $this->validateGuestSubscription();
                    $this->validateEmailAvailable($email);

                    $subscriber = $this->_subscriberFactory->create()->loadByEmail($email);
                    if ($subscriber->getId()
                        && $subscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED
                    ) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('This email address is already subscribed.')
                        );
                    }

                    $status = $this->_subscriberFactory->create()->subscribe($email);
                    if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                        $this->messageManager->addSuccess(__('The confirmation request has been sent.'));
                    } else {
                        $this->messageManager->addSuccess(__('Thank you for your subscription.'));
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addException(
                        $e,
                        __('There was a problem with the subscription: %1', $e->getMessage())
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong with the subscription.'));
                }
            }
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        } else {
            $this->messageManager->addError(__('Something went wrong with the subscription.'));
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}
