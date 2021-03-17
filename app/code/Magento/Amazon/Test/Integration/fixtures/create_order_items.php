<?php

declare(strict_types=1);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$accountFactory = $objectManager->get(\Magento\Amazon\Api\Data\AccountInterfaceFactory::class);
/** @var \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository */
$accountRepository = $objectManager->get(\Magento\Amazon\Api\AccountRepositoryInterface::class);

$storesData = require __DIR__ . '/data/stores.php';
foreach ($storesData as $accountData) {
    /** @var \Magento\Amazon\Model\Amazon\Account $account */
    $account = $accountFactory->create();
    $account->setData($accountData);
    $accountRepository->save($account);
}
