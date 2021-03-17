<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action;

use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\AbstractAction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Indexer\CacheContext;
use Magento\Catalog\Model\Product;

/**
 * Class Rows
 * @package Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action
 */
class Rows extends AbstractAction
{
    /**
     * {@inheritdoc}
     * @throws InputException
     * @throws LocalizedException
     */
    protected function doExecute($ids = null)
    {
        if (empty($ids)) {
            throw new InputException(__('Bad value was supplied.'));
        }
        try {
            $this->resourceStatisticsIndexer->reindexRows($ids);
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
        if (!empty($ids)) {
            $cacheContext->registerEntities(Product::CACHE_TAG, $ids);
        }
        return $cacheContext;
    }
}
