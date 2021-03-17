<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Container;

class IdsContainer extends UniqueContainer
{
    private $fetchAll = false;

    public function fetchAll(): void
    {
        $this->fetchAll = true;
    }

    public function isFetchAll(): bool
    {
        return $this->fetchAll;
    }
}
