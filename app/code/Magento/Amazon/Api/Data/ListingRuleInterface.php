<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Api\Data;

/**
 * @deprecated this interface is no longer a part of the module API and will be removed in the next major release
 */
interface ListingRuleInterface
{
    /**
     * Get listing rule id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get listing rule merchant id
     *
     * @return int|null
     */
    public function getMerchantId();

    /**
     * Set listing rule merchant id
     *
     * @param int $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId);

    /**
     * Get listing rule website id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Set listing rule website id
     *
     * @param int $websiteId
     * @return void
     */
    public function setWebsiteId(int $websiteId);

    /**
     * Get assigned rule conditions
     *
     * @return string
     */
    public function getConditionsSerialized();

    /**
     * Set assigned rule conditions
     *
     * @param string $conditions
     * @return $this
     */
    public function setConditionsSerialized($conditions);
}
