<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$accountRepository = $objectManager->get(\Magento\Amazon\Model\Amazon\AccountRepository::class);
$collectionFactory = $objectManager->create(
    Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
);
$collection = $collectionFactory->create()
    ->addFieldToFilter('name', 'mage-test');
foreach ($collection as $account) {
    $accountRepository->delete($account);
}
