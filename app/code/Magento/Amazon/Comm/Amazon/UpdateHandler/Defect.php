<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Defect as DefectResourceModel;

class Defect implements HandlerInterface
{
    use AddMerchantIdToUpdatesTrait;

    /**
     * @var DefectResourceModel
     */
    private $defectResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(DefectResourceModel $defectResourceModel, ChunkedHandler $chunkedHandler)
    {
        $this->defectResourceModel = $defectResourceModel;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $merchantId = (int)$account->getMerchantId();
        $defects = $this->addMerchantIdToUpdates($updates, $account);
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData) use ($merchantId): void {
                $this->defectResourceModel->insert($chunkData, $merchantId);
            },
            $defects,
            $account,
            'Cannot process logs with defects. Please report an error.'
        );
    }
}
