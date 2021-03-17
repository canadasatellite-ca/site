<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Logger\Processor;

/**
 * Trim Magento Base Directory from all possible paths to minimize data written in the logs
 */
class TrimMagentoBaseDirFromLog implements \Monolog\Processor\ProcessorInterface
{
    /**
     * @param array $records
     * @return array
     */
    public function __invoke(array $records)
    {
        return $this->trimArray($records);
    }

    private function trimArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = str_replace(BP, '', $value);
            } elseif (is_array($value)) {
                $data[$key] = $this->trimArray($data[$key]);
            }
        }

        return $data;
    }
}
