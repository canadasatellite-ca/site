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

class Stores implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
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
        $uuids = !empty($args['uuids']) ?: [];
        if (!$uuids) {
            $context->stores()->ids()->fetchAll();
        }
        $context->stores()->addSet($uuids, $info);
        return new Deferred(function () use ($uuids, $context) {
            return $this->stores->getStores($uuids, $context);
        });
    }
}
