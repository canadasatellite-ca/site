<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Indexer;

use Magento\Framework\Indexer\AbstractProcessor;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class AttributeProcessor
 */
class AttributeProcessor extends AbstractProcessor
{
    const INDEXER_ID = 'channel_amazon_attribute';

    /** @var IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        parent::__construct($indexerRegistry);
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Updates mview mode (if applicable)
     */
    public function updateMode()
    {

        // get indexer object
        if ($indexer = $this->indexerRegistry->get(self::INDEXER_ID)) {
            // if not enabled
            if (!$indexer->getView()->isEnabled()) {
                $indexer->setScheduled(true);
            }
        }
    }
}
