<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\DeadlockRetriesTrait;

class ChunkedHandler
{
    use DeadlockRetriesTrait;

    /**
     * @var AscClientLogger
     */
    private $logger;

    public function __construct(AscClientLogger $logger)
    {
        $this->logger = $logger;
    }

    public function handleUpdatesWithChunks(
        callable $handlerFn,
        array $updates,
        AccountInterface $account,
        string $logErrorMessage,
        int $chunkSize = 300
    ): array {
        $processedLogs = new ProcessedLogs();
        foreach (array_chunk($updates, $chunkSize, true) as $chunkData) {
            try {
                $this->doWithDeadlockRetries(
                    static function () use ($chunkData, $handlerFn) {
                        return $handlerFn($chunkData);
                    }
                );
                $processedLogs->addIdsAsKeys($chunkData);
            } catch (\Exception $e) {
                // check for potential error with data consistency
                $keyOccurrence = array_count_values(array_merge([], ...array_map('array_keys', $chunkData)));
                $foundDataInconsistency = count(array_unique($keyOccurrence)) > 1;
                $this->logger->error(
                    $logErrorMessage,
                    [
                        'exception' => $e,
                        'account' => $account,
                        'data_inconsistency_found' => (int)$foundDataInconsistency,
                        'key_occurrence_count' => $foundDataInconsistency ? $keyOccurrence : null,
                        'debug' => ['failed_logs' => base64_encode(json_encode($chunkData))]
                    ]
                );
            }
        }
        return $processedLogs->getProcessedIds();
    }
}
