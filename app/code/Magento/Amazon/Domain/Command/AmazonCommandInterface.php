<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

/**
 * Class AmazonCommandInterface
 */
interface AmazonCommandInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return array
     */
    public function getBody(): array;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}
