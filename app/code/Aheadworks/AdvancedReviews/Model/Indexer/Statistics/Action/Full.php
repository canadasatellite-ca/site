<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action;

use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\AbstractAction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\CacheContext;
use Magento\Catalog\Model\Product;

/**
 * Class Full
 * @package Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action
 */
class Full extends AbstractAction
{
    /**
     * Execute Full reindex
     *
     * {@inheritdoc}
     * @throws LocalizedException
     */
    protected function doExecute($ids = null)
    {
        try {
            $this->resourceStatisticsIndexer->reindexAll();
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheContext($ids = null)
    {
        /** @var CacheContext $cacheContext */
        $cacheContext = $this->cacheContextFactory->create();
        $cacheContext->registerTags([Product::CACHE_TAG]);
        return $cacheContext;
    }
}
