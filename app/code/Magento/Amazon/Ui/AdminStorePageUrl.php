<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Ui;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\PricingRuleInterface;

class AdminStorePageUrl
{
    /**
     * @var \Magento\Amazon\Service\BuildBackendUrl
     */
    private $url;

    public function __construct(\Magento\Amazon\Service\BuildBackendUrl $buildBackendUrl)
    {
        $this->url = $buildBackendUrl;
    }

    public function listingRules(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_listing_rules_index/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function listings(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_listing_index/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function manageListingsPage(AccountInterface $account, ?string $tab = null, ?string $fragment = null): string
    {
        $urlParameters = ['merchant_id' => $account->getMerchantId()];
        if ($tab) {
            $urlParameters['active_tab'] = $tab;
        }
        return $this->url->getUrl(
            'channel/amazon/account_listing_index/',
            $urlParameters,
            [],
            $fragment
        );
    }

    public function listingsActive(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->manageListingsPage($account, 'listing_view_active', $fragment);
    }

    public function listingsInactive(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->manageListingsPage($account, 'listing_view_inactive', $fragment);
    }

    public function listingsInProgress(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->manageListingsPage($account, 'listing_view_published', $fragment);
    }

    public function settingsListings(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_settings_listings_index/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function settingsOrders(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_settings_orders_index/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function magentoOrderUrl(string $magentoOrderId, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'sales/order/view',
            ['order_id' => $magentoOrderId],
            [],
            $fragment
        );
    }

    public function orderDetails(AccountInterface $account, int $orderId, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/order_details_index/',
            ['merchant_id' => $account->getMerchantId(), 'id' => $orderId],
            [],
            $fragment
        );
    }

    public function pricingRules(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_pricing_rules_index/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function report(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_report_index/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function logs(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/logs/',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function listingSave(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'listingSave',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function listingRulesPreview(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_listing_rules_preview',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function listingRulesSave(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_listing_rules_save',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function listingRulesIndex(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_listing_rules_index',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function settingsOrdersSave(AccountInterface $account, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/account_settings_orders_save',
            ['merchant_id' => $account->getMerchantId()],
            [],
            $fragment
        );
    }

    public function attributePage(string $attributeId, ?string $fragment = null): string
    {
        return $this->url->getUrl(
            'channel/amazon/attribute_value_index',
            [
                'id' => $attributeId,
                // do not remove parent_id unless you want to deal with internals of UI components
                'parent_id' => $attributeId
            ],
            [],
            $fragment
        );
    }

    public function pricingRulesCreate(
        AccountInterface $account,
        ?PricingRuleInterface $pricingRule = null,
        ?string $fragment = null
    ): string {
        $urlParameters = ['merchant_id' => $account->getMerchantId()];
        if ($pricingRule && $pricingRule->getId()) {
            $urlParameters['id'] = $pricingRule->getId();
        }
        return $this->url->getUrl(
            'channel/amazon/account_pricing_rules_create',
            $urlParameters,
            [],
            $fragment
        );
    }
}
