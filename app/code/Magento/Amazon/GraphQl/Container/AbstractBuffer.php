<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Container;

abstract class AbstractBuffer
{
    protected $data;

    public function count(): int
    {
        return count($this->data);
    }

    public function isEmpty(): bool
    {
        return null === $this->data;
    }

    public function getAll(): array
    {
        return (array) $this->data;
    }
}
