<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Payment\Info;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Ui\FrontendUrl;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Info;

/**
 * Class Marketplaces
 */
class Marketplaces extends Info
{
    /** string */
    protected $_template = 'Magento_Amazon::info/marketplaces.phtml';
    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    /** @var AccountRepositoryInterface $orderRepository */
    private $accountRepository;

    /**
     * @var FrontendUrl
     */
    private $frontendUrl;

    /**
     * Constructor
     *
     * @param Context $context
     * @param OrderRepositoryInterface $orderRepository
     * @param FrontendUrl $frontendUrl
     * @param AccountRepositoryInterface $accountRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        FrontendUrl $frontendUrl,
        AccountRepositoryInterface $accountRepository,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->frontendUrl = $frontendUrl;
        $this->accountRepository = $accountRepository;
        parent::__construct($context, $data);
    }

    /**
     * Returns order id by amazon order id
     *
     * @return string
     * @var string $amazonOrderId
     */
    public function getIdByAmazonOrderId($amazonOrderId)
    {
        try {
            /** @var OrderInterface */
            $order = $this->orderRepository->getOrderByMarketplaceOrderId($amazonOrderId);
        } catch (NoSuchEntityException $e) {
            return '';
        }

        return $order->getId();
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Magento_Amazon::info/pdf/marketplaces.phtml');
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getOrderDetailsUrl(): string
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            return $this->frontendUrl->getHomeUrl();
        }
        try {
            $order = $this->orderRepository->getOrderBySalesOrderId($orderId);
            $account = $this->accountRepository->getByMerchantId($order->getMerchantId());
        } catch (NoSuchEntityException $e) {
            return $this->frontendUrl->getHomeUrl();
        }
        return $this->frontendUrl->getOrderDetailsUrl($account, $order);
    }
}
