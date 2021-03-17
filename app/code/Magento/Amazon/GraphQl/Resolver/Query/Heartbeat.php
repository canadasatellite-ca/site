<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;

class Heartbeat implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        return true;
    }
}
