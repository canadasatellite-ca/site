<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Query;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\EntityNotFoundException;
use Magento\Amazon\GraphQl\Resolver\ResolverInterface;

class StorePageUrl implements ResolverInterface
{
    /**
     * @var \Magento\Amazon\Ui\AdminStorePageUrl
     */
    private $adminStorePageUrl;
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;

    public function __construct(
        \Magento\Amazon\Ui\AdminStorePageUrl $adminStorePageUrl,
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores
    ) {
        $this->adminStorePageUrl = $adminStorePageUrl;
        $this->stores = $stores;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $type = $args['type'];
        $uuid = $args['uuid'];
        $fragment = $args['urlFragment'] ?? null;
        $context->stores()->ids()->add($uuid);
        return new \GraphQL\Deferred(function () use ($type, $uuid, $fragment, $context) {
            $account = $this->stores->getSingleStore($uuid, $context);
            if (!$account) {
                throw new EntityNotFoundException('Store with requested UUID does not exist: ' . $uuid);
            }
            return $this->getUrl($type, $account, $fragment);
        });
    }

    /**
     * @param string $type
     * @param AccountInterface $account
     * @param string|null $fragment
     * @return string
     * @throws EntityNotFoundException
     */
    private function getUrl(string $type, AccountInterface $account, ?string $fragment = null): string
    {
        switch ($type) {
            case 'listingRules':
                return $this->adminStorePageUrl->listingRules($account, $fragment);
            case 'listings':
                return $this->adminStorePageUrl->listings($account, $fragment);
            case 'listingsActive':
                return $this->adminStorePageUrl->listingsActive($account, $fragment);
            case 'listingsInactive':
                return $this->adminStorePageUrl->listingsInactive($account, $fragment);
            case 'listingsInProgress':
                return $this->adminStorePageUrl->listingsInProgress($account, $fragment);
            case 'settingsListings':
                return $this->adminStorePageUrl->settingsListings($account, $fragment);
            case 'settingsOrders':
                return $this->adminStorePageUrl->settingsOrders($account, $fragment);
            case 'pricingRules':
                return $this->adminStorePageUrl->pricingRules($account, $fragment);
            case 'report':
                return $this->adminStorePageUrl->report($account, $fragment);
            case 'logs':
                return $this->adminStorePageUrl->logs($account, $fragment);
        }
        throw new \Magento\Amazon\GraphQl\EntityNotFoundException('Cannot resolve URL of type ' . $type);
    }
}
