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
