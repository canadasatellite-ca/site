<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Indexer\Statistics;

use Aheadworks\AdvancedReviews\Model\ResourceModel\Indexer\Statistics as ResourceStatisticsIndexer;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Indexer\CacheContextFactory;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class AbstractAction
 * @package Aheadworks\AdvancedReviews\Model\Indexer\Statistics
 */
abstract class AbstractAction
{
    /**
     * @var ResourceStatisticsIndexer
     */
    protected $resourceStatisticsIndexer;

    /**
     * @var CacheContextFactory
     */
    protected $cacheContextFactory;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param ResourceStatisticsIndexer $resourceStatisticsIndexer
     * @param CacheContextFactory $cacheContextFactory
     * @param ManagerInterface $eventManager,
     */
    public function __construct(
        ResourceStatisticsIndexer $resourceStatisticsIndexer,
        CacheContextFactory $cacheContextFactory,
        ManagerInterface $eventManager
    ) {
        $this->resourceStatisticsIndexer = $resourceStatisticsIndexer;
        $this->cacheContextFactory = $cacheContextFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int|null $ids
     * @return void
     */
    public function execute($ids = null)
    {
        $this->doExecute($ids);
        $this->flushRelatedEntitiesCache($ids);
    }

    /**
     * Inner logic of execution process
     *
     * @param array|int|null $ids
     * @return void
     */
    abstract protected function doExecute($ids = null);

    /**
     * Flush cache for given ids
     *
     * @param array|int|null $ids
     * @return void
     */
    protected function flushRelatedEntitiesCache($ids = null)
    {
        $cacheContext = $this->getCacheContext($ids);
        $this->dispatchEventToFlushCache($cacheContext);
    }

    /**
     * Retrieve prepared for given ids cache context
     *
     * @param array|int|null $ids
     * @return CacheContext
     */
    abstract protected function getCacheContext($ids = null);

    /**
     * Dispatch event to flush cache for tags specified in cache context
     *
     * @param CacheContext $cacheContext
     * @return $this
     */
    private function dispatchEventToFlushCache($cacheContext)
    {
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $cacheContext]);
        return $this;
    }
}
