<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Store\Api\Data\WebsiteInterface;

class CreateAccountListingRule
{
    /**
     * @var ListingRuleRepositoryInterface
     */
    private $listingRuleRepository;

    public function __construct(ListingRuleRepositoryInterface $listingRuleRepository)
    {
        $this->listingRuleRepository = $listingRuleRepository;
    }

    /**
     * @param AccountInterface $account
     * @param WebsiteInterface $website
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createForAccount(AccountInterface $account, WebsiteInterface $website): void
    {
        $rule = $this->listingRuleRepository->getByMerchantId($account->getMerchantId());
        $rule->setWebsiteId((int)$website->getId());
        $rule->setMerchantId((int)$account->getMerchantId());
        $this->listingRuleRepository->save($rule);
    }
}
