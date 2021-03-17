<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Settings\Orders;

use Magento\Amazon\Api\AccountOrderRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Save
 */
class Save extends Action
{
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var AccountOrderRepositoryInterface $accountOrderRepository */
    protected $accountOrderRepository;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Amazon\Ui\AdminStorePageUrl
     */
    private $adminStorePageUrl;

    /**
     * @param Action\Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param AccountOrderRepositoryInterface $accountOrderRepository
     * @param AscClientLogger $logger
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl
     */
    public function __construct(
        Action\Context $context,
        AccountRepositoryInterface $accountRepository,
        AccountOrderRepositoryInterface $accountOrderRepository,
        AscClientLogger $logger,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl
    ) {
        parent::__construct($context);
        $this->accountRepository = $accountRepository;
        $this->accountOrderRepository = $accountOrderRepository;
        $this->logger = $logger;
        $this->frontendUrl = $frontendUrl;
        $this->adminStorePageUrl = $adminStorePageUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Save account listing settings
     *
     * @return ResultInterface|ResponseInterface
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var array */
        $data = $this->getRequest()->getParams();
        /** @var string */
        $merchantId = $this->getRequest()->getParam('merchant_id');

        if (!$data['id']) {
            unset($data['id']);
        }

        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->logger->notice(
                'Cannot find merchant by id during orders settings saving',
                [
                    'merchant_id' => $merchantId,
                    'exception' => $e,
                ]
            );
            $this->messageManager->addErrorMessage(__('Unable to load Amazon account. Please try again.'));
            return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
        }

        /** @var AccountOrderInterface */
        $accountOrder = $this->accountOrderRepository->getByMerchantId($merchantId);

        // add form data
        $accountOrder->setData($data);

        try {
            $this->accountOrderRepository->save($accountOrder);
            $this->messageManager->addSuccessMessage(
                __('You have successfully saved the order settings.')
            );
        } catch (CouldNotSaveException $e) {
            $this->logger->error(
                'Unable to save order settings',
                [
                    'merchant_uuid' => $merchantId,
                    'exception' => $e,
                ]
            );
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $resultRedirect->setUrl($this->adminStorePageUrl->settingsOrders($account));
        }

        return $resultRedirect->setUrl($this->adminStorePageUrl->settingsOrders($account));
    }
}
