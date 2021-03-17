<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Indexer\Pricing\Action;

use Magento\Amazon\Model\Indexer\Pricing\AbstractAction;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Partial
 */
class Partial extends AbstractAction
{
    /**
     * Execute partial reindex
     *
     * @param null|array $ids
     * @return void
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        try {
            $this->reindexPartial(array_unique($ids));
        } catch (\Exception $e) {
            $phrase = __($e->getMessage());
            throw new LocalizedException($phrase, $e);
        }
    }
}
