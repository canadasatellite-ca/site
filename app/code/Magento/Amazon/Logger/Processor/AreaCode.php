<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;

class AreaCode implements \Monolog\Processor\ProcessorInterface
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
        try {
            $areaCode = $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            //thrown if area code not set e.g. happens if running CLI command
            $areaCode = $e->getMessage();
        }

        $records['extra']['area_code'] = $areaCode;

        return $records;
    }
}
