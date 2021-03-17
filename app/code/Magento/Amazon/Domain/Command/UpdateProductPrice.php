<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

/**
 * Class UpdateProductPrice
 */
class UpdateProductPrice implements AmazonCommandInterface
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
        return 'update_product_price';
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
     * sku            - Seller sku,
     * standard_price - Standard Product Price,
     * map_price      - Minimum advertised price,
     * business_price - Amazon Business Price,
     * price_type     - Quantity Price Type (percent),
     * tier_prices => [
     *      qty_price_1   - Quantity Price Discount 1,
     *      qty_lower_bound_1 - Minimum quantity for discount qty_price_1,
     *      qty_price_2   - Quantity Price Discount 2,
     *      qty_lower_bound_2 - Minimum quantity for discount qty_price_2,
     *      qty_price_3   - Quantity Price Discount 3,
     *      qty_lower_bound_3 - Minimum quantity for discount qty_price_3,
     *      qty_price_4   - Quantity Price Discount 4,
     *      qty_lower_bound_4 - Minimum quantity for discount qty_price_4,
     *      qty_price_5   - Quantity Price Discount 5,
     *      qty_lower_bound_5 - Minimum quantity for discount qty_price_5,
     * ],
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
