<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Indexer\Stock\Action;

use Magento\Amazon\Model\Indexer\Stock\AbstractAction;

/**
 * Class Full
 */
class Full extends AbstractAction
{
    /**
     * Execute Full reindex
     * @return void
     */
    public function execute()
    {
        try {
            $this->reindexAll();
        } catch (\Exception $e) {
            $this->ascClientLogger->critical($e);
        }
    }
}
