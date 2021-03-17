<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Settings\Orders;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Ui\FrontendUrl;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /**
     * @var FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AccountRepositoryInterface $accountRepository
     * @param FrontendUrl $frontendUrl
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AccountRepositoryInterface $accountRepository,
        FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->accountRepository = $accountRepository;
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates primary account credentials page
     *
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int $merchantId */
        $merchantId = $this->getRequest()->getParam('merchant_id');

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to load Amazon store settings. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var \Magento\Framework\View\Result\Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Amazon::channel_amazon_index');
        $resultPage->getConfig()->getTitle()->prepend(__('Order Settings'));

        return $resultPage;
    }
}
