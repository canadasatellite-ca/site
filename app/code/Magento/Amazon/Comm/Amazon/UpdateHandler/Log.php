<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as LogResourceModel;

class Log implements HandlerInterface
{
    use AddMerchantIdToUpdatesTrait;

    /**
     * @var LogResourceModel
     */
    private $logResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(LogResourceModel $logResourceModel, ChunkedHandler $chunkedHandler)
    {
        $this->logResourceModel = $logResourceModel;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $logs = $this->addMerchantIdToUpdates($updates, $account);
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->logResourceModel->insert($chunkData);
            },
            $logs,
            $account,
            'Cannot process logs with listing updates. Please report an error.'
        );
    }
}
