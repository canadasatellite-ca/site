<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Lowest as LowestResourceModel;

class LowestPrice implements HandlerInterface
{
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;
    /**
     * @var LowestResourceModel
     */
    private $lowestResourceModel;

    public function __construct(ChunkedHandler $chunkedHandler, LowestResourceModel $lowestResourceModel)
    {
        $this->chunkedHandler = $chunkedHandler;
        $this->lowestResourceModel = $lowestResourceModel;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $removalsByCountryCode = [];
        $insertions = [];

        foreach ($updates as $logId => $log) {
            $asin = $log['asin'] ?? '';

            if (!$asin) {
                continue;
            }

            $countryCode = $log['country_code'] ?? '';

            if (!$countryCode) {
                continue;
            }

            if (!isset($log['landed_price'])) {
                $log['landed_price'] = 0.00;
            }
            if (!isset($log['list_price'])) {
                $log['list_price'] = 0.00;
            }
            if (!isset($log['shipping_price'])) {
                $log['shipping_price'] = 0.00;
            }
            $removalsByCountryCode[$countryCode][] = $asin;
            $insertions[$logId] = $log;
        }

        foreach ($removalsByCountryCode as $countryCode => $asins) {
            if ($asins) {
                $this->lowestResourceModel->removeAsinsByCountryCode($countryCode, $removalsByCountryCode);
            }
        }

        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->lowestResourceModel->insert($chunkData);
            },
            $insertions,
            $account,
            'Cannot process logs with the lowest prices. Please report an error.'
        );
    }
}
