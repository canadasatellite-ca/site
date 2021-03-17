<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Container;

class ListBuffer extends AbstractBuffer
{
    public function add($value): void
    {
        $this->data[] = $value;
    }

    public function addAll(array $values): void
    {
        if ($values) {
            $this->data = array_merge($this->data, $values);
        }
    }
}
