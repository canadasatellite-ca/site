<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Field;

use GraphQL\Type\Definition\ResolveInfo;
use LogicException;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\Amazon\Account;
use Magento\Amazon\Model\ApiClient;

class StoreIrpUrl implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var string[]
     */
    private $irpUrls = [];

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        if (!$parent instanceof Account) {
            throw new LogicException(
                'Cannot work with instance of ' . get_class($parent) . '. Instance of ' . Account::class . ' expected'
            );
        }
        if (!isset($this->irpUrls[$parent->getId()])) {
            $this->irpUrls[$parent->getId()] = $this->apiClient->getIrpUrl($parent);
        }
        return $this->irpUrls[$parent->getId()];
    }
}
