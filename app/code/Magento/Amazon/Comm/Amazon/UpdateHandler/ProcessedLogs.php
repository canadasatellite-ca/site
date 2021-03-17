<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

class ProcessedLogs
{
    private $logIdsChunks = [];

    public function addIdsAsKeys(array $logsWithIdsAsKeys): void
    {
        $this->logIdsChunks[] = array_keys($logsWithIdsAsKeys);
    }

    public function getProcessedIds(): array
    {
        return array_merge([], ...$this->logIdsChunks);
    }
}
