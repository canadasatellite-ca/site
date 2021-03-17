<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

/**
 * Class RemoveProduct
 */
class RemoveProduct implements AmazonCommandInterface
{
    /**
     * @var array
     */
    private $body;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @param array $body
     * @param string $identifier
     */
    public function __construct(
        array $body,
        string $identifier
    ) {
        $this->body = $body;
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'remove_product';
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Command body must contain the following:
     *
     * sku - Seller Sku
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
