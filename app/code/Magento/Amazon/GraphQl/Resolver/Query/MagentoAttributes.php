<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\DataProvider\MagentoAttributes as MagentoAttributesDataProvider;
use Magento\Amazon\GraphQl\Resolver\ResolverInterface;

class MagentoAttributes implements ResolverInterface
{
    /**
     * @var MagentoAttributesDataProvider
     */
    private $dataProvider;

    /**
     * @param MagentoAttributesDataProvider $dataProvider
     */
    public function __construct(MagentoAttributesDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        return $this->dataProvider->getMagentoAttributes();
    }
}
