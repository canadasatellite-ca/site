<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

/**
 * Class GetProductsByQuery
 */
class GetProductsByQuery implements AmazonCommandInterface
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
        return 'get_products_list_by_query';
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
     * listing_id   - Id of listing
     * query        - Search query
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }
}
