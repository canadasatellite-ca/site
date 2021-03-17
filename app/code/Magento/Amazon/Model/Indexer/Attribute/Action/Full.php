<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Indexer\Attribute\Action;

use Magento\Amazon\Model\Indexer\Attribute\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Full
 */
class Full extends AbstractAction
{
    /**
     * Execute full reindex
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        try {
            $this->reindexAll();
        } catch (\Exception $e) {
            $phrase = __($e->getMessage());
            throw new LocalizedException($phrase, $e);
        }
    }
}
