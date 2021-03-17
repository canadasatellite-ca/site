<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Model\ApiClient\ResponseFormatValidationException;
use Magento\Amazon\Model\ApiClient\ResponseValidationException;

/**
 * Class AccountSetup
 * @deprecated would be deleted on the next major release
 * @see \Magento\Amazon\Service\Account\UpdateAccount
 */
class AccountSetup
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
     * Update account on host
     *
     * @param AccountInterface $account
     * @return array
     * @throws ResponseFormatValidationException
     * @throws ResponseValidationException
     * @throws ApiException
     */
    public function execute(AccountInterface $account): array
    {
        return $this->apiClient->updateMerchant($account, [
            'email' => $account->getEmail(),
            'name' => $account->getName(),
        ]);
    }
}
