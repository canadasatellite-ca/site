<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Multiple as MultipleResourceModel;

class Multiple implements HandlerInterface
{
    /**
     * @var MultipleResourceModel
     */
    private $multipleResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(MultipleResourceModel $multipleResourceModel, ChunkedHandler $chunkedHandler)
    {
        $this->multipleResourceModel = $multipleResourceModel;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->multipleResourceModel->insert($chunkData);
            },
            $updates,
            $account,
            'Cannot process logs for listing updates with multiple matches. Please report an error.'
        );
    }
}
