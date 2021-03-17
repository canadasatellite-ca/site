<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\Definitions;

class ListStatus implements HandlerInterface
{
    /**
     * @var \Magento\Amazon\Model\ResourceModel\Amazon\Listing
     */
    private $listingResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(
        \Magento\Amazon\Model\ResourceModel\Amazon\Listing $listingResourceModel,
        ChunkedHandler $chunkedHandler
    ) {
        $this->listingResourceModel = $listingResourceModel;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $listingUpdates = [];
        $excludedListingIdsByStatus = [];
        $notExcludedListingIdsByStatus = [];

        $excludeStatuses = [
            Definitions::NOMATCH_LIST_STATUS,
            Definitions::VARIANTS_LIST_STATUS,
            Definitions::MULTIPLE_LIST_STATUS,
        ];

        foreach ($updates as $logId => $log) {
            $listingId = $log['listing_id'] ?? '';

            if (!$listingId) {
                continue;
            }

            $listStatus = $log['list_status'] ?? '';

            if (!$listStatus) {
                continue;
            }

            $asin = $log['asin'] ?? $log['ASIN'] ?? '';
            if ($asin) {
                $listingUpdates[$listingId] = $log;
            }
            if (in_array($listStatus, $excludeStatuses, true)) {
                $excludedListingIdsByStatus[$listStatus][$logId] = $listingId;
            } else {
                $notExcludedListingIdsByStatus[$listStatus][$logId] = $listingId;
            }
        }

        $this->handleListingUpdates($listingUpdates, $account);
        $this->handleListingStatusUpdates(
            $excludedListingIdsByStatus,
            $account,
            [
                Definitions::VALIDATE_ASIN_LIST_STATUS,
                Definitions::GENERAL_SEARCH_LIST_STATUS
            ]
        );
        $this->handleListingStatusUpdates($notExcludedListingIdsByStatus, $account, []);

        // todo: we should be more specific about which updates were successfully processed
        return array_keys($updates);
    }

    /**
     * @param array $listingUpdates
     * @param AccountInterface $account
     */
    private function handleListingUpdates(array $listingUpdates, AccountInterface $account): void
    {
        $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData): void {
                $this->listingResourceModel->updateListingInfo($chunkData);
            },
            $listingUpdates,
            $account,
            'Cannot process logs with listing updates. Please report an error.'
        );
    }

    /**
     * @param array $listingIdsToUpdateByStatus
     * @param AccountInterface $account
     * @param array $statusesToExclude
     * @return void
     */
    private function handleListingStatusUpdates(
        array $listingIdsToUpdateByStatus,
        AccountInterface $account,
        $statusesToExclude = []
    ): void {
        $logMessage = $statusesToExclude
            ? 'Cannot process listing status updates with excluded statuses. Please report an error.'
            : 'Cannot process listing status updates without excluded statuses. Please report an error.';
        foreach ($listingIdsToUpdateByStatus as $status => $listingIds) {
            $this->chunkedHandler->handleUpdatesWithChunks(
                function ($chunkData) use ($status, $statusesToExclude): void {
                    $this->listingResourceModel->scheduleListStatusUpdate(
                        array_values($chunkData),
                        $status,
                        $statusesToExclude
                    );
                },
                $listingIds,
                $account,
                $logMessage
            );
        }
    }
}
