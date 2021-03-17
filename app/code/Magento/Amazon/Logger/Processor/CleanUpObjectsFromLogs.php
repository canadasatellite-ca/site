<?php

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

class CleanUpObjectsFromLogs implements ProcessorInterface
{
    /**
     * @param array $records
     * @return array The processed records
     */
    public function __invoke(array $records)
    {
        return $this->filterArray($records);
    }

    private function filterArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                if (!method_exists($value, '__toString') && !$value instanceof \DateTimeInterface) {
                    unset($data[$key]);
                }
            } elseif (is_array($value)) {
                $data[$key] = $this->filterArray($data[$key]);
            }
        }
        return $data;
    }
}
