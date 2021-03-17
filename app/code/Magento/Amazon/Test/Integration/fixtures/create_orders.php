<?php

declare(strict_types=1);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @phpstan-ignore-next-line */
$orderFactory = $objectManager->get(\Magento\Amazon\Api\Data\OrderInterfaceFactory::class);
/** @var \Magento\Amazon\Api\OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->get(\Magento\Amazon\Api\OrderRepositoryInterface::class);
/** @var \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository */
$accountRepository = $objectManager->get(\Magento\Amazon\Api\AccountRepositoryInterface::class);
$store = $accountRepository->getByUuid('authentication_pending_account');

$ordersData = require __DIR__ . '/data/orders.php';
foreach ($ordersData as $orderData) {
    $orderData['merchant_id'] = $store->getMerchantId();
    /** @var \Magento\Amazon\Model\Amazon\Order $order */
    $order = $orderFactory->create();
    $order->setData($orderData);
    $orderRepository->save($order);
}
