<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\ListingManagement;

class RemoveListing implements HandlerInterface
{
    /**
     * @var ListingManagement
     */
    private $listingManagement;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(ListingManagement $listingManagement, ChunkedHandler $chunkedHandler)
    {
        $this->listingManagement = $listingManagement;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $merchantId = (int)$account->getMerchantId();
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData) use ($merchantId): void {
                foreach ($chunkData as $log) {
                    $sellerSku = $log['seller_sku'] ?? '';
                    if (!$sellerSku) {
                        continue;
                    }

                    $this->listingManagement->removeListing($sellerSku, $merchantId);
                }
            },
            $updates,
            $account,
            'Cannot process logs for listing removals. Please report an error.',
            1 // keeping chunk size super low to get handling of errors on each log
        );
    }
}
