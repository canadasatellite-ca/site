<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ProsAndConsFields
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier
 */
class ProsAndConsFields extends AbstractModifier
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param Config $config
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        Config $config,
        StoreResolver $storeResolver
    ) {
        $this->config = $config;
        $this->storeResolver = $storeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $storeId = isset($data[ReviewInterface::STORE_ID]) ? $data[ReviewInterface::STORE_ID] : null;
        $data['areProsConsEnabledForReviewStore'] = $this->areProsAndConsFieldsEnabled($storeId);
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Check if need to add pros and cons fields to the review form
     *
     * @param int|null $storeId
     * @return bool
     */
    private function areProsAndConsFieldsEnabled($storeId)
    {
        $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);
        return $this->config->areProsAndConsEnabled($websiteId);
    }
}
