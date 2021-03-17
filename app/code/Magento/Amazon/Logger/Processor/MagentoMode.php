<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Magento\Framework\App\State as AppState;
use Monolog\Processor\ProcessorInterface;

class MagentoMode implements ProcessorInterface
{
    /**
     * @var AppState
     */
    private $state;

    public function __construct(AppState $state)
    {
        $this->state = $state;
    }

    /**
     * @param array $records
     * @return array The processed records
     */
    public function __invoke(array $records)
    {
        $records['extra']['mode'] = $this->state->getMode();

        return $records;
    }
}
