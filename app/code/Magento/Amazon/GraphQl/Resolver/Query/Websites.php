<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\DataProvider\Websites as WebsitesDataProvider;

class Websites implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var WebsitesDataProvider
     */
    private $dataProvider;

    /**
     * Website constructor.
     * @param WebsitesDataProvider $dataProvider
     */
    public function __construct(WebsitesDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        return $this->dataProvider->getAllWebsites();
    }
}
