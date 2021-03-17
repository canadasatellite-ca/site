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
use Magento\Amazon\GraphQl\DataProvider\StoreRevenue;
use Magento\Amazon\Model\Amazon\Account;

class StoreReportRevenue implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var StoreRevenue
     */
    private $storeRevenue;

    public function __construct(StoreRevenue $storeRevenue)
    {
        $this->storeRevenue = $storeRevenue;
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

        $days = (int)$args['days'];

        $filterEmpty = ($args['filterEmpty'] ?? false);
        $merchantId = (int)$parent->getId();

        $context->revenueReportDays()->add($days);
        $context->revenueReportIds()->add($merchantId);
        return new Deferred(function () use ($merchantId, $context, $days, $filterEmpty) {
            return $this->storeRevenue->getRevenueByStoreId($merchantId, $days, $context, $filterEmpty);
        });
    }
}
