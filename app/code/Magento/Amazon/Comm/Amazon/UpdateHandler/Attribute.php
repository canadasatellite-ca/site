<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\DeadlockRetriesTrait;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute as AttributeResourceModel;

class Attribute implements HandlerInterface
{
    use DeadlockRetriesTrait;

    /**
     * @var AttributeResourceModel
     */
    private $attributeResource;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(AttributeResourceModel $attributeResource, ChunkedHandler $chunkedHandler)
    {
        $this->attributeResource = $attributeResource;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->attributeResource->insert($chunkData);
            },
            $updates,
            $account,
            'Cannot process logs with attributes. Please report an error.'
        );
    }
}
