<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Pricing\Rules;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
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
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AccountRepositoryInterface $accountRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AccountRepositoryInterface $accountRepository,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl
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
     * @return PageFactory
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int $merchantId */
        $merchantId = $this->getRequest()->getParam('merchant_id');

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Unable to load the pricing rules. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var Page */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Amazon::channel_amazon_index');
        $resultPage->getConfig()->getTitle()->prepend(__('Pricing Rules'));

        return $resultPage;
    }
}
