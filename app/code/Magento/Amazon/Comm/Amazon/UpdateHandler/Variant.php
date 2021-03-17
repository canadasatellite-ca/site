<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Variant as VariantResourceModel;

class Variant implements HandlerInterface
{
    /**
     * @var VariantResourceModel
     */
    private $variantResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(VariantResourceModel $variantResourceModel, ChunkedHandler $chunkedHandler)
    {
        $this->variantResourceModel = $variantResourceModel;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->variantResourceModel->insert($chunkData);
            },
            $updates,
            $account,
            'Cannot process logs for updates with listing variants. Please report an error.'
        );
    }
}
