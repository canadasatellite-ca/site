<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\ResultTransformer;

class MagentoWebsite implements \Magento\Amazon\GraphQl\Resolver\ResultTransformerInterface
{
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Websites
     */
    private $websites;

    public function __construct(\Magento\Amazon\GraphQl\DataProvider\Websites $websites)
    {
        $this->websites = $websites;
    }

    public function resolve(
        $value,
        $parent,
        array $args,
        \Magento\Amazon\GraphQl\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $info
    ) {
        $websiteId = null;
        if (is_numeric($value)) {
            $websiteId = (string)$value;
        } elseif (is_array($parent) || (is_object($parent) && $parent instanceof \ArrayAccess)) {
            if (isset($parent['website_id'])) {
                $websiteId = (string)$parent['website_id'];
            }
        }
        return $this->websites->getWebsite($websiteId) ?: $value;
    }
}
