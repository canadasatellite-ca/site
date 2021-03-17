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
use Magento\Amazon\Model\Amazon\Definitions;

class Marketplaces implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    private $marketplaces;

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        if (null === $this->marketplaces) {
            $this->marketplaces = Definitions::getEnabledMarketplaces();
        }
        $codes = $args['countryCodes'] ?? null;
        return new Deferred(function () use ($codes) {
            return null === $codes
                ? $this->marketplaces
                : array_intersect_key(array_flip($codes), $this->marketplaces);
        });
    }
}
