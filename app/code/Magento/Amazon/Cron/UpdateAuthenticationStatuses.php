<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Cron;

use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Collection as AccountCollection;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory as AccountCollectionFactory;
use Magento\Amazon\Service\Account\VerifyAuthentication;

class UpdateAuthenticationStatuses
{
    /**
     * @var VerifyAuthentication
     */
    private $verifyAuthentication;
    /**
     * @var AccountCollectionFactory
     */
    private $accountCollectionFactory;
    /**
     * @var AscClientLogger
     */
    private $logger;

    public function __construct(
        VerifyAuthentication $verifyAuthentication,
        AccountCollectionFactory $accountCollectionFactory,
        AscClientLogger $logger
    ) {
        $this->verifyAuthentication = $verifyAuthentication;
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->logger = $logger;
    }

    public function unauthenticated(): void
    {
        /** @var AccountCollection $accountCollection */
        $accountCollection = $this->accountCollectionFactory->create();
        $accountCollection->addFieldToFilter(
            'authentication_status',
            ['neq' => \Magento\Amazon\Model\Amazon\Definitions::ACCOUNT_AUTH_STATUS_AUTHENTICATED]
        );
        $this->updateAuthenticationStatuses($accountCollection->getItems());
    }

    public function authenticated(): void
    {
        /** @var AccountCollection $accountCollection */
        $accountCollection = $this->accountCollectionFactory->create();
        $accountCollection->addFieldToFilter(
            'authentication_status',
            ['eq' => \Magento\Amazon\Model\Amazon\Definitions::ACCOUNT_AUTH_STATUS_AUTHENTICATED]
        );
        $this->updateAuthenticationStatuses($accountCollection->getItems());
    }

    private function updateAuthenticationStatuses(array $accounts): void
    {
        foreach ($accounts as $account) {
            try {
                $this->verifyAuthentication->verifyByAccount($account);
            } catch (\Throwable $exception) {
                $this->logger->error(
                    'Exception occurred during account authentication check',
                    ['account' => $account, 'exception' => $exception]
                );
            }
        }
    }
}
