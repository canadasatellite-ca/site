<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Order\Cancel;

use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Domain\Command\CancelOrderFactory;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Model\Order\OrderHandlerResolver;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\Collection;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cve
 */
class Save extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /** @var OrderRepositoryInterface $orderRepository */
    protected $orderRepository;
    /** @var OrderItemCollectionFactory $orderItemCollectionFactory */
    protected $orderItemCollectionFactory;
    /** @var OrderHandlerResolver */
    protected $orderHandlerResolver;
    /** @var CancelOrderFactory $cancelOrderFactory */
    private $cancelOrderFactory;
    /** @var CommandDispatcher $commandDispatcher */
    private $commandDispatcher;
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Amazon\Api\AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param OrderHandlerResolver $orderHandlerResolver
     * @param CancelOrderFactory $cancelOrderFactory
     * @param CommandDispatcher $commandDispatcher
     * @param \Magento\Amazon\Ui\FrontendUrl $frontendUrl
     * @param \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        OrderRepositoryInterface $orderRepository,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        OrderHandlerResolver $orderHandlerResolver,
        CancelOrderFactory $cancelOrderFactory,
        CommandDispatcher $commandDispatcher,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->orderHandlerResolver = $orderHandlerResolver;
        $this->cancelOrderFactory = $cancelOrderFactory;
        $this->commandDispatcher = $commandDispatcher;
        $this->frontendUrl = $frontendUrl;
        $this->accountRepository = $accountRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Cancel Amazon order
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var string */
        $errorMessage = __('Unable to cancel the order. Please try again.');
        /** @var string */
        $successMessage = __('The Amazon order has been canceled and the request has been submitted to Amazon.');
        /** @var int */
        $orderId = $this->getRequest()->getParam('id');

        try {
            /** @var OrderInterface */
            $order = $this->orderRepository->getByOrderId($orderId);
            $merchantId = (int)$order->getMerchantId();
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($errorMessage);
            return $resultRedirect->setPath($this->frontendUrl->getHomeUrl());
        }

        $url = $this->frontendUrl->getStoreDetailsUrl($account);

        /** @var string */
        $orderId = $order->getOrderId();

        // cancel amazon order
        $orderHandler = $this->orderHandlerResolver->resolve();
        $orderHandler->cancel($orderId);

        // schedule api call to cancel order
        if ($this->cancelAmazonOrder($merchantId, $orderId)) {
            $this->messageManager->addSuccessMessage($successMessage);
            return $resultRedirect->setPath($url);
        }

        // failed to cancel order
        $this->messageManager->addErrorMessage($errorMessage);
        return $resultRedirect->setPath($url);
    }

    /**
     * Submits Amazon order cancellation to request to Amazon
     * and updates the order status to "Canceled"
     *
     * @param int $merchantId
     * @param int $orderId
     * @return bool
     */
    private function cancelAmazonOrder($merchantId, $orderId)
    {
        /** @var Collection $collection */
        $collection = $this->orderItemCollectionFactory->create();
        $collection->addFieldToFilter('order_id', $orderId);

        $orderItemIds = [];
        foreach ($collection as $item) {
            $orderItemIds[] = $item->getOrderItemId();
        }

        if (empty($orderItemIds)) {
            return false;
        }

        $commandData = [
            'body' => [
                'order_id' => $orderId,
                'reason' => $this->getRequest()->getParam('reason'),
                'order_item_ids' => $orderItemIds
            ],
            'identifier' => (string)$orderId
        ];

        $command = $this->cancelOrderFactory->create($commandData);
        $this->commandDispatcher->dispatch($merchantId, $command);

        return true;
    }
}
