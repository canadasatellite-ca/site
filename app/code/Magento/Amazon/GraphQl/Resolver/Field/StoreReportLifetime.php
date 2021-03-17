<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Field;

use GraphQL\Deferred;
use GraphQL\Type\Definition\ResolveInfo;
use LogicException;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\DataProvider\StoreLifetimeRevenue;
use Magento\Amazon\Model\Amazon\Account;

class StoreReportLifetime implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{

    /**
     * @var StoreLifetimeRevenue
     */
    private $storeLifetimeRevenue;

    public function __construct(StoreLifetimeRevenue $storeLifetimeRevenue)
    {
        $this->storeLifetimeRevenue = $storeLifetimeRevenue;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        if (!$parent instanceof Account) {
            $type = is_object($parent) ? get_class($parent) : gettype($parent);
            throw new LogicException(
                'Cannot work with ' . $type . '. Instance of ' . Account::class . ' expected'
            );
        }
        $context->lifetimeReport()->add($parent->getId());
        $merchantId = (int)$parent->getId();
        return new Deferred(function () use ($merchantId, $context) {
            return $this->storeLifetimeRevenue->getTotalRevenueByStoreId($merchantId, $context);
        });
    }
}
