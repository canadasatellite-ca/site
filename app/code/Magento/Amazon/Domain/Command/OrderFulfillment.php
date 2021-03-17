<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

/**
 * Class OrderFulfillment
 */
class OrderFulfillment implements AmazonCommandInterface
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
        return 'order_fulfillment';
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
     * order_id         - Amazon Order Id,
     * fulfillment_date - Data of fulfillment,
     * carrier_type     - Carrier Type,
     * carrier_name     - Carrier Name,
     * shipping_method  - Shipping Method
     * tracking         - Tracking Number
     * order_item_id    - Order item id,
     * quantity         - Quantity
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
