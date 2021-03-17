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
 * Class Row
 * @package Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action
 */
class Row extends AbstractAction
{
    /**
     * {@inheritdoc}
     * @throws InputException
     * @throws LocalizedException
     */
    protected function doExecute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new InputException(
                __('We can\'t rebuild the index for an undefined entity.')
            );
        }
        try {
            $this->resourceStatisticsIndexer->reindexRows([$id]);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getCacheContext($id = null)
    {
        /** @var CacheContext $cacheContext */
        $cacheContext = $this->cacheContextFactory->create();
        if (!empty($id)) {
            $cacheContext->registerEntities(Product::CACHE_TAG, [$id]);
        }
        return $cacheContext;
    }
}
