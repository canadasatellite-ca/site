<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

class LogPreviousException implements \Monolog\Processor\ProcessorInterface
{
    /**
     * @param array $records
     * @return array The processed records
     */
    public function __invoke(array $records)
    {
        $exception = $records['context']['exception'] ?? null;
        if ($exception instanceof \Exception && $exception->getPrevious()) {
            $previous = $exception->getPrevious();
            $records['extra']['previous_exception'] = $previous->getMessage();
            $records['extra']['previous_stacktrace'] = $previous->getTraceAsString();
        }
        return $records;
    }
}
