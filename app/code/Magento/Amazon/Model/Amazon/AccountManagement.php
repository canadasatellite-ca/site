<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\AccountManagementInterface;
use Magento\Amazon\Api\Data\AccountInterface;

/**
 * Class AccountManagement
 */
class AccountManagement implements AccountManagementInterface
{
    /** @var AccountListingRepositoryInterface */
    private $accountListingRepository;

    /**
     * @param AccountListingRepositoryInterface $accountListingRepository
     */
    public function __construct(
        AccountListingRepositoryInterface $accountListingRepository
    ) {
        $this->accountListingRepository = $accountListingRepository;
    }

    /**
     * @param AccountInterface $account
     * @return bool
     */
    public function isAccountReadyToPushCommands(AccountInterface $account): bool
    {
        $accountListing = $this->accountListingRepository->getByMerchantId($account->getMerchantId());
        $importThirdPartyListings = (bool)$accountListing->getThirdpartyIsActive();
        $reportRun = $account->getReportRun();

        return !$importThirdPartyListings || $reportRun;
    }
}
