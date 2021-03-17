<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\ResultTransformer;

use Magento\Amazon\Model\Amazon\Definitions;

class Marketplace implements \Magento\Amazon\GraphQl\Resolver\ResultTransformerInterface
{
    public function resolve(
        $value,
        $parent,
        array $args,
        \Magento\Amazon\GraphQl\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $info
    ) {
        $countryCode = null;
        if (is_string($value)) {
            $countryCode = $value;
        } elseif (is_array($parent) || (is_object($parent) && $parent instanceof \ArrayAccess)) {
            if (isset($parent['marketplace'])) {
                $countryCode = (string)$parent['marketplace'];
            }
        }
        return $countryCode
            ? Definitions::getMarketplaceByCountryCode($countryCode)
            : $value;
    }
}
