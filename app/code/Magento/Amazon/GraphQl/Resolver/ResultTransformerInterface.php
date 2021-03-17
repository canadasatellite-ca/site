<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;

interface ResultTransformerInterface
{
    public function resolve($value, $parent, array $args, Context $context, ResolveInfo $info);
}
