<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Indexer\Stock\Action;

use Magento\Amazon\Model\Indexer\Stock\AbstractAction;

/**
 * Class Partial
 */
class Partial extends AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param null|array $ids
     * @return void
     */
    public function execute($ids)
    {
        try {
            $this->reindexPartial(array_unique($ids));
        } catch (\Exception $e) {
            $this->ascClientLogger->critical($e);
        }
    }
}
