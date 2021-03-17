<?php

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Magento\Amazon\Api\Data\AccountInterface;
use Monolog\Processor\ProcessorInterface;

class ExtractAccountData implements ProcessorInterface
{
    public function __invoke(array $records)
    {
        $merchant = $records['context']['merchant'] ?? null;
        if (!$merchant instanceof AccountInterface) {
            $account = $records['context']['account'] ?? null;
            if (!$account instanceof AccountInterface) {
                return $records;
            }
            $merchant = $account;
        }

        unset($records['context']['merchant'], $records['context']['account']);

        $records['extra']['merchant_id'] = $merchant->getMerchantId();
        $records['extra']['uuid'] = $merchant->getUuid();

        return $records;
    }
}
