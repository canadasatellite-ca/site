<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;

class Store implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;

    /**
     * Stores constructor.
     * @param \Magento\Amazon\GraphQl\DataProvider\Stores $stores
     */
    public function __construct(
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores
    ) {
        $this->stores = $stores;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $uuid = $args['uuid'];
        $context->stores()->addSingle($uuid, $info);
        return new Deferred(function () use ($uuid, $context) {
            return $this->stores->getSingleStore($uuid, $context);
        });
    }
}
