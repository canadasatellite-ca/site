<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api;

use Magento\Amazon\Api\Data\PricingRuleInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface PricingRuleRepositoryInterface
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface PricingRuleRepositoryInterface
{
    /**
     * Create and/or update pricing rules
     *
     * @param PricingRuleInterface $rule
     * @return PricingRuleInterface
     * @throws CouldNotSaveException
     */
    public function save(PricingRuleInterface $rule);

    /**
     * Delete pricing rule by rule id
     *
     * @param int $id
     * @return void
     */
    public function deleteById($id);

    /**
     * Get pricing rule object by id
     *
     * @param int id
     * @return PricingRuleInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);
}
