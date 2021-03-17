<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;

trait AddMerchantIdToUpdatesTrait
{
    private function addMerchantIdToUpdates(
        array $updates,
        AccountInterface $account,
        string $key = 'merchant_id'
    ): array {
        $merchantId = (int)$account->getMerchantId();
        foreach ($updates as $logId => $log) {
            $log[$key] = $merchantId;
            $updates[$logId] = $log;
        }
        return $updates;
    }
}
