<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Error\Log as ErrorResourceModel;

class Error implements HandlerInterface
{
    use AddMerchantIdToUpdatesTrait;

    /**
     * @var ErrorResourceModel
     */
    private $errorResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(ErrorResourceModel $errorResourceModel, ChunkedHandler $chunkedHandler)
    {
        $this->errorResourceModel = $errorResourceModel;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $errors = $this->addMerchantIdToUpdates($updates, $account);
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->errorResourceModel->insert($chunkData);
            },
            $errors,
            $account,
            'Cannot process logs with the errors. Please report an error.'
        );
    }
}
