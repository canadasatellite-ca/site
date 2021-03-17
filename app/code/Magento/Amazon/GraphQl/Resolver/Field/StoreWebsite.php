<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Field;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;

class StoreWebsite implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Websites
     */
    private $dataProvider;

    /**
     * StoreWebsite constructor.
     * @param \Magento\Amazon\GraphQl\DataProvider\Websites $dataProvider
     */
    public function __construct(\Magento\Amazon\GraphQl\DataProvider\Websites $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        // If a parent is the website - just return it back, field resolver will find a field for you
        if ($info->parentType->name === 'MagentoWebsite') {
            return $parent;
        }
        return isset($parent['website_id']) ? $this->dataProvider->getWebsite($parent['website_id']) : null;
    }
}
