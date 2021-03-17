<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

/**
 * Class UpdateInventoryQty
 */
class UpdateInventoryQty implements AmazonCommandInterface
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
        return 'update_inventory_qty';
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
     * id
     * sku
     * qty
     * handling
     * fulfilled_by
     * fulfilled_by_update
     * condition_override_listing
     *
     * @return array
     * @see \Magento\Amazon\Model\Indexer\Stock\AbstractAction::prepareCommandBody
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
