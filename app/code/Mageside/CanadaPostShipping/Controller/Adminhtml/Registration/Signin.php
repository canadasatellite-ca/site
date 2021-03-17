<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Adminhtml\Registration;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\Auth\Session;
use Mageside\CanadaPostShipping\Model\Service\Registration;

class Signin extends \Magento\Backend\App\Action
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
     * Signin constructor.
     * @param Action\Context $context
     * @param Registration $registration
     * @param Session $customerSession
     */
    public function __construct(
        Action\Context $context,
        Registration $registration,
        Session $customerSession
    ) {
        $this->registration = $registration;
        $this->session = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Exception
     */
    public function execute()
    {
        $result = $this->registration->getToken();
        if (!empty($result['tokenId'])) {
            $this->messageManager->addNoticeMessage('You will be redirected to the Canada Post website to sign in.');
            $this->session->setData('canada_post_token_id', $result['tokenId']);
        } else {
            $this->messageManager->addNoticeMessage('Unable get token ID. Please try again later.');
            $this->session->setData('canada_post_token_id', null);
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Canada Post Sign In'));

        return $resultPage;
    }
}
