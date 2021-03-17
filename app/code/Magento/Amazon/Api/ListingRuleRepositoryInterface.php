<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\ListingRuleInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ListingRuleRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ListingRuleRepositoryInterface
{
    /**
     * Create and/or update listing rules
     *
     * @param ListingRuleInterface $rule
     * @return ListingRuleInterface
     * @throws CouldNotSaveException
     */
    public function save(ListingRuleInterface $rule);

    /**
     * Get listing rule object by merchant id
     *
     * @param int $merchantId
     * @return ListingRuleInterface
     * @throws NoSuchEntityException
     */
    public function getByMerchantId($merchantId);
}
