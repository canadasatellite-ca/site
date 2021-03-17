<?php

declare(strict_types=1);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository */
$accountRepository = $objectManager->get(\Magento\Amazon\Api\AccountRepositoryInterface::class);

$storesData = require __DIR__ . '/data/stores.php';
foreach (array_keys($storesData) as $storeUuid) {
    try {
        $account = $accountRepository->getByUuid($storeUuid);
        $accountRepository->delete($account);
    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
    }
}
