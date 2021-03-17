<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

class AddErrorsForAggregatedException implements ProcessorInterface
{
    /**
     * @param array $records
     * @return array
     */
    public function __invoke(array $records)
    {
        $exception = $records['context']['exception'] ?? null;
        if ($exception instanceof \Magento\Framework\Exception\AggregateExceptionInterface) {
            $errors = array_map(
                function (\Throwable $e) {
                    return $e->getMessage();
                },
                $exception->getErrors()
            );
            $records['extra']['exception_errors'] = $errors;
        }
        return $records;
    }
}
