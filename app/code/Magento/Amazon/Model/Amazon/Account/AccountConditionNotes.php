<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Amazon\Account;

use Magento\Amazon\Api\AccountListingRepositoryInterface;

/**
 * Class AccountConditionNotes
 */
class AccountConditionNotes
{
    /**
     * @var AccountListingRepositoryInterface
     */
    private $accountListingRepository;

    /**
     * @param AccountListingRepositoryInterface $accountListingRepository
     */
    public function __construct(
        AccountListingRepositoryInterface $accountListingRepository
    ) {
        $this->accountListingRepository = $accountListingRepository;
    }

    /**
     * Get account condition notes
     *
     * @param int $merchantId
     * @param int $condition
     * @return string
     */
    public function getNotes(int $merchantId, int $condition): string
    {
        $listConditionText = '';
        $notes = '';

        /** @var \Magento\Amazon\Api\Data\AccountListingInterface $account */
        $account = $this->accountListingRepository->getByMerchantId($merchantId);
        if ($account->getListCondition() > 0 && $account->getSellerNotes()) {
            $listConditionText = $account->getSellerNotes();
        }

        switch ($condition) {
            case 1:
                $notes = $account->getSellerNotesLikenew();
                break;
            case 2:
                $notes = $account->getSellerNotesVerygood();
                break;
            case 3:
                $notes = $account->getSellerNotesGood();
                break;
            case 4:
                $notes = $account->getSellerNotesAcceptable();
                break;
            case 5:
                $notes = $account->getSellerNotesCollectibleLikenew();
                break;
            case 6:
                $notes = $account->getSellerNotesCollectibleVerygood();
                break;
            case 7:
                $notes = $account->getSellerNotesCollectibleGood();
                break;
            case 8:
                $notes = $account->getSellerNotesCollectibleAcceptable();
                break;
            case 10:
                $notes = $account->getSellerNotesRefurbished();
                break;
        }

        return $notes ?: $listConditionText;
    }
}
