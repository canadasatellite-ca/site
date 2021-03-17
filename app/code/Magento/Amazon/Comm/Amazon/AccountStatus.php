<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ApiClient;

/**
 * Class AccountSetup
 * @deprecated would be deleted on the next major release
 * @see \Magento\Amazon\Service\Account\ChangeAccountStatus
 */
class AccountStatus
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(
        ApiClient $apiClient
    ) {
        $this->apiClient = $apiClient;
    }

    /**
     * Updates merchant account status on host
     *
     * @param AccountInterface $account
     * @return void
     * @throws ApiClient\ApiException
     * @throws ApiClient\ResponseValidationException
     */
    public function updateOnHost(AccountInterface $account)
    {
        $this->apiClient->updateMerchantStatus($account, (int)$account->getIsActive());
    }
}
