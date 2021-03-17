<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

class ReimportAttributes implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var \Magento\Amazon\Service\Account\ReimportAttributeValues
     */
    private $reimportAttributeValues;

    public function __construct(\Magento\Amazon\Service\Account\ReimportAttributeValues $reimportAttributeValues)
    {
        $this->reimportAttributeValues = $reimportAttributeValues;
    }

    public function resolve(
        $parent,
        array $args,
        \Magento\Amazon\GraphQl\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $info
    ) {
        $ids = $args['ids'] ?? [];
        if (!$ids) {
            return false;
        }
        $this->reimportAttributeValues->reimport($ids);
        return true;
    }
}
