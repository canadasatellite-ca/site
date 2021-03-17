<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class MagentoSettings implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * Admin session lifetime config path
     */
    private const XML_PATH_ADMIN_SESSION_LIFETIME = 'admin/security/session_lifetime';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    private $resolvers;

    private $settings = [];

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $data = [];
        foreach (array_keys($info->getFieldSelection()) as $field) {
            $data[$field] = $this->getValue($field);
        }
        return $data;
    }

    private function getValue(string $field)
    {
        if (!array_key_exists($field, $this->settings)) {
            $resolvers = $this->getResolvers();
            $this->settings[$field] = isset($resolvers[$field]) ? $resolvers[$field]() : null;
        }
        return $this->settings[$field];
    }

    private function getResolvers(): array
    {
        if (null === $this->resolvers) {
            $this->resolvers = [
                'adminSessionLifetime' => function () {
                    return (int)$this->scopeConfig->getValue(self::XML_PATH_ADMIN_SESSION_LIFETIME);
                }
            ];
        }
        return $this->resolvers;
    }
}
