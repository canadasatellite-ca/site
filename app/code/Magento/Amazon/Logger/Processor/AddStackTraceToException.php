<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

class AddStackTraceToException implements ProcessorInterface
{
    /**
     * @param array $records
     * @return array
     */
    public function __invoke(array $records)
    {
        $exception = $records['context']['exception'] ?? null;
        if ($exception instanceof \Exception || $exception instanceof \Throwable) {
            $records['extra']['stack_trace'] = $exception->getTraceAsString();
        }
        return $records;
    }
}
