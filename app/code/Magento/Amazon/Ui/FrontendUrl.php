<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Ui;

class FrontendUrl
{
    /**
     * @var \Magento\Amazon\Service\BuildBackendUrl
     */
    private $buildBackendUrl;

    public function __construct(\Magento\Amazon\Service\BuildBackendUrl $buildBackendUrl)
    {
        $this->buildBackendUrl = $buildBackendUrl;
    }

    public function getStoreDetailsUrl(\Magento\Amazon\Api\Data\AccountInterface $account): string
    {
        return $this->getStoreDetailsUrlByUuid((string)$account->getUuid());
    }

    public function getStoreDetailsUrlByUuid(string $uuid): string
    {
        $fragment = $uuid ? "/store-details/{$uuid}" : null;
        return $this->getHomeUrl($fragment);
    }

    public function getOrdersGridUrlByUuid(string $uuid): string
    {
        $fragment = $uuid ? "/store-details/{$uuid}/orders" : null;
        return $this->getHomeUrl($fragment);
    }

    public function getHomeUrl(?string $fragment = null): string
    {
        return $this->buildBackendUrl->getUrl('channel/amazon', [], [], $fragment);
    }

    public function getOrderDetailsUrl(\Magento\Amazon\Api\Data\AccountInterface $account, \Magento\Amazon\Api\Data\OrderInterface $order): string
    {
        return $this->getOrderDetailsUrlByUuidAndOrderId((string)$account->getUuid(), (string)$order->getOrderId());
    }

    public function getOrderDetailsUrlByUuidAndOrderId(string $uuid, string $orderId): string
    {
        $fragment = ($uuid && $orderId) ? "/store-details/{$uuid}/orders/{$orderId}" : null;
        return $this->getHomeUrl($fragment);
    }
}
