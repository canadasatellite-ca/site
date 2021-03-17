<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ListingResourceModel;

class Listing implements HandlerInterface
{
    use AddMerchantIdToUpdatesTrait;

    /**
     * @var ListingResourceModel
     */
    private $listingResourceModel;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;
    /**
     * @var AccountListingRepositoryInterface
     */
    private $accountListingRepository;

    /**
     * @var bool[]
     */
    private $isImportThirdPartyListings = [];

    public function __construct(
        ListingResourceModel $listingResourceModel,
        ChunkedHandler $chunkedHandler,
        AccountListingRepositoryInterface $accountListingRepository
    ) {
        $this->listingResourceModel = $listingResourceModel;
        $this->chunkedHandler = $chunkedHandler;
        $this->accountListingRepository = $accountListingRepository;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $listings = $this->addMerchantIdToUpdates($updates, $account);
        $merchantId = (int)$account->getMerchantId();
        $isImportThirdPartyListings = $this->isImportThirdPartyListingsForAccount($merchantId);
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function ($chunkData) use ($merchantId, $isImportThirdPartyListings): void {
                $this->listingResourceModel->insert(
                    $chunkData,
                    $merchantId,
                    $isImportThirdPartyListings
                );
            },
            $listings,
            $account,
            'Cannot process logs with defects. Please report an error.'
        );
    }

    public function isImportThirdPartyListingsForAccount(int $merchantId): bool
    {
        if (!isset($this->isImportThirdPartyListings[$merchantId])) {
            $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);
            $this->isImportThirdPartyListings[$merchantId] = (bool)$accountListing->getThirdpartyIsActive();
        }
        return $this->isImportThirdPartyListings[$merchantId];
    }
}
