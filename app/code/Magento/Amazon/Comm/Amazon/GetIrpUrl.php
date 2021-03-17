<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ApiClient;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GetIrpUrl
 * @deprecated would be deleted in the next major release
 */
class GetIrpUrl
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
     * Creates integration account on host
     *
     * @param AccountInterface $account
     * @return string
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function execute(AccountInterface $account): string
    {
        return $this->apiClient->getIrpUrl($account);
    }
}
