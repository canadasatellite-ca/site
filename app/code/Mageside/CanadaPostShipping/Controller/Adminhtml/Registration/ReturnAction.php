<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Adminhtml\Registration;

use Magento\Framework\Controller\ResultFactory;
use Mageside\CanadaPostShipping\Model\Carrier;
use Mageside\CanadaPostShipping\Model\Service\Registration;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;

class ReturnAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageside_CanadaPostShipping::mageside_canadapost_shipping';

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\Registration
     */
    private $registration;

    /**
     * @var Session
     */
    private $session;

    /**
     * ReturnAction constructor.
     * @param Context $context
     * @param Registration $registration
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Registration $registration,
        Session $customerSession
    ) {
        $this->registration = $registration;
        $this->session = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $sessionTokenId = $this->session->getData('canada_post_token_id');
        $tokenId = $this->getRequest()->getParam('token-id');
        $status = $this->getRequest()->getParam('registration-status');
        $website = $this->getRequest()->getParam('website', 0);

        if ($tokenId && $status == 'SUCCESS') {
            if ($tokenId != $sessionTokenId) {
                $this->messageManager->addErrorMessage(__('Token ID is not valid.'));
            } else {
                $result = $this->registration->getMerchantInfo($tokenId);
                if ($result['error']) {
                    foreach ($result['messages'] as $message) {
                        $this->messageManager->addErrorMessage($message['message']);
                    }
                } elseif ($result['merchant']) {
                    try {
                        $this->registration->saveMerchantInfo($result['merchant'], $website);
                        $this->messageManager->addSuccessMessage(__('Canada Post account settings successfully saved.'));
                    } catch (\Exception $exception) {
                        $this->messageManager->addErrorMessage(__('Something went wrong.'));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__('Something went wrong.'));
                }
            }
        } elseif ($status == 'CANCELLED') {
            $this->messageManager->addErrorMessage(
                __('Registration status: %1. Unable to submit requests on your behalf '
                    .'until you accept the terms and conditions, '
                    .'but your relationship with Canada Post remains in effect.',
                    $status
                )
            );
        } else {
            $this->messageManager->addErrorMessage(
                __('Registration status: %1. Please try again later.', $status)
            );
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('adminhtml/system_config/edit', ['section'=>'carriers']);
    }
}
