<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

class RemoveDefect implements HandlerInterface
{
    /**
     * @var \Magento\Amazon\Model\ResourceModel\Amazon\Defect
     */
    private $resource;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(
        \Magento\Amazon\Model\ResourceModel\Amazon\Defect $resource,
        ChunkedHandler $chunkedHandler
    ) {
        $this->resource = $resource;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, \Magento\Amazon\Api\Data\AccountInterface $account): array
    {
        $merchantId = (int)$account->getMerchantId();
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData) use ($merchantId): void {
                foreach ($chunkData as $log) {
                    $sellerSku = (isset($log['seller_sku'])) ? $log['seller_sku'] : '';

                    if (!$sellerSku) {
                        continue;
                    }

                    $alertType = (isset($log['alert_type'])) ? $log['alert_type'] : '';

                    if (!$alertType) {
                        continue;
                    }

                    $this->resource->remove($sellerSku, $alertType, $merchantId);
                }
            },
            $updates,
            $account,
            'Cannot process logs for defect removals. Please report an error.',
            1 // keeping chunk size super low to get handling of errors on each log
        );
    }
}
