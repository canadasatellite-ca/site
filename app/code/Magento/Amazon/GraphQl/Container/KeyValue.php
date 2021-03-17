<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Container;

class KeyValue extends AbstractBuffer
{
    public function add($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function addAll(array $data): void
    {
        $this->data = array_replace((array)$this->data, $data);
    }

    public function get($name)
    {
        return $this->data[$name] ?? null;
    }
}
