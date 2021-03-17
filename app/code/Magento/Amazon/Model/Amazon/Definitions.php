<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

/**
 * Class Definitions
 *
 * Contains all global definitions relative to the Amazon
 * marketplace and associated API calls
 */
class Definitions
{
    /**
     * Base URL for User Guide
     */
    const UG_URL = 'https://docs.magento.com/m2/ee/user_guide/sales-channels/asc/';

    /**
     * URL links for User Guide Main Page Links
     */
    const MAIN_MENU_URL = 'managing-stores.html'; // store grid
    const MAIN_MENU_ORDER_URL = 'managing-orders.html';  // main menu order grid
    const MAIN_MENU_ATTRIBUTE_URL = 'managing-attributes.html'; // main menu attribute grid
    const MAIN_MENU_ACTIVITY_URL = 'listing-changes-log.html'; // main menu listing change log
    const MAIN_MENU_ERROR_URL = 'communication-errors-log.html';  // main menu communication error log
    const CREDENTIAL_SETTINGS_URL = 'store-integration.html';
    const LISTING_SETTINGS_URL = 'ob-listing-settings.html';
    const ORDER_SETTINGS_URL = 'ob-order-settings.html';
    const LISTING_RULES_URL = 'listing-rules.html';
    const LISTING_RULES_INELIGIBLE_URL = 'listing-rule-preview.html';
    const LISTING_RULES_ADDITIONS_URL = 'listing-rule-preview.html';
    const PRICING_RULES_URL = 'ob-pricing-rules.html';
    const PRICING_RULES_CREATE_URL = 'add-pricing-rule.html';
    const LISTING_ACTIVE_URL = 'active-listings.html';
    const LISTING_INACTIVE_URL = 'inactive-listings.html';
    const LISTING_INCOMPLETE_URL = 'incomplete-listings.html';
    const LISTING_IN_PROGRESS_URL = 'ready-to-list.html';
    const LISTING_THIRDPARTY_URL = 'new-third-party-listings.html';
    const LISTING_INELIGIBLE_URL = 'ineligible-listings.html';
    const LISTING_OVERRIDES_URL = 'overrides.html';
    const LISTING_ENDED_URL = 'ended-listings.html';
    const LISTING_DETAIL_ACTIVITY_URL = 'product-listing-details.html#listing-activity-log';
    const LISTING_DETAIL_BESTBUYBOX_URL = 'product-listing-details.html#buy-box-competitor-pricing';
    const LISTING_DETAIL_LOWEST_URL = 'product-listing-details.html#lowest-competitor-pricing';
    const LISTING_OVERRIDES_FORM_URL = 'overrides.html';
    const LISTING_UPDATE_FORM_URL = 'amazon-manually-update-incomplete-listing.html';
    const LISTING_ALIAS_FORM_URL = 'create-alias-seller-sku.html';
    const LISTING_THIRDPARTY_CREATE_URL = 'creating-assigning-catalog-products.html';
    const LISTING_THIRDPARTY_MANUAL_URL = 'creating-assigning-catalog-products.html';
    const LISTING_REPORT_PRICING_URL = 'store-reports.html';
    const LISTING_REPORT_DEFECT_URL = 'listing-improvements.html';
    const ATTRIBUTE_PRODUCTS_URL = 'amazon-matching-attributes-values.html';
    const ATTRIBUTE_ACTIONS_URL = 'creating-attributes.html';
    const ORDER_DETAILS_URL = 'amazon-order-details.html';
    const ORDER_ITEMS_URL = 'amazon-order-details.html';
    const ORDER_TRACKING_URL = 'amazon-order-details.html';
    const ORDER_CANCELLATION_URL = 'amazon-order-details.html';

    /**
     * URL links for form field tooltips
     *
     * SECTION SETTINGS -> LISTINGS
     */
    const UG_AUTO_LIST = 'ob-product-listing-actions.html';
    const UG_HANDLING_TIME = 'ob-product-listing-actions.html';
    const UG_THIRDPARTY_IS_ACTIVE = 'ob-third-party-listings.html';
    const UG_THIRDPARTY_SKU_FIELD = 'ob-third-party-listings.html';
    const UG_THIRDPARTY_ASIN_FIELD = 'ob-third-party-listings.html';
    const UG_PRICE_FIELD = 'ob-listing-price.html';
    const UG_MAP_PRICE_FIELD = 'ob-listing-price.html';
    const UG_STRIKE_PRICE_FIELD = 'ob-listing-price.html';
    const UG_VAT_IS_ACTIVE = 'ob-listing-price.html';
    const UG_VAT_PERCENTAGE = 'ob-listing-price.html';
    const UG_MANAGE_PTC = 'ob-listing-price.html';
    const UG_DEFAULT_PTC = 'ob-listing-price.html';
    const UG_CC_IS_ACTIVE = 'ob-listing-price.html';
    const UG_MIN_QTY = 'ob-stock-quantity.html';
    const UG_MAX_QTY = 'ob-stock-quantity.html';
    const UG_CUSTOM_QTY = 'ob-stock-quantity.html';
    const UG_FULFILLED_BY = 'ob-fulfilled-by.html';
    const UG_FULFILLED_BY_FIELD = 'ob-fulfilled-by.html';
    const UG_FULFILLED_BY_SELLER_SELECT = 'ob-fulfilled-by.html';
    const UG_FULFILLED_BY_AMAZON_SELECT = 'ob-fulfilled-by.html';
    const UG_FULFILLED_BY_SELLER_TEXT = 'ob-fulfilled-by.html';
    const UG_FULFILLED_BY_AMAZON_TEXT = 'ob-fulfilled-by.html';
    const UG_ASIN_MAPPING_FIELD = 'ob-catalog-search.html';
    const UG_EAN_MAPPING_FIELD = 'ob-catalog-search.html';
    const UG_GCID_MAPPING_FIELD = 'ob-catalog-search.html';
    const UG_ISBN_MAPPING_FIELD = 'ob-catalog-search.html';
    const UG_UPC_MAPPING_FIELD = 'ob-catalog-search.html';
    const UG_GENERAL_MAPPING_FIELD = 'ob-catalog-search.html';
    const UG_LIST_CONDITION = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_FIELD = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_NEW_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_REFURBISHED_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_LIKENEW_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_VERYGOOD_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_GOOD_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_ACCEPTABLE_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_COLLECTIBLE_LIKENEW_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_COLLECTIBLE_VERYGOOD_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_COLLECTIBLE_GOOD_SELECT = 'ob-product-listing-condition.html';
    const UG_LIST_CONDITION_COLLECTIBLE_ACCEPTABLE_SELECT = 'ob-product-listing-condition.html';

    /**
     * URL links for form field tooltips
     *
     * SECTION SETTINGS -> ORDERS
     */
    const UG_ORDER_IS_ACTIVE = 'ob-order-settings.html';
    const UG_DEFAULT_STORE = 'ob-order-settings.html';
    const UG_CUSTOMER_IS_ACTIVE = 'ob-order-settings.html';
    const UG_IS_EXTERNAL_ORDER_ID = 'ob-order-settings.html';
    const UG_RESERVE = 'ob-order-settings.html';
    const UG_CUSTOM_STATUS_IS_ACTIVE = 'ob-order-settings.html';

    /**
     * URL links for form field tooltips
     *
     * SECTION LISTING RULES
     */
    const UG_LISTING_RULE_WEBSITE_IDS = 'ob-listing-rules.html';

    /**
     * URL links for form field tooltips
     *
     * SECTION PRICING RULES
     */
    const UG_PRICING_RULE_PRIORITY = 'pricing-rule-general-settings.html';
    const UG_PRICING_RULE_STOP_RULES_PROCESSING = 'pricing-rule-general-settings.html';
    const UG_PRICING_RULE_AUTO = 'ob-pricing-rules.html';
    const UG_PRICING_RULE_AUTO_SOURCE = 'intelligent-repricing-rules.html';
    const UG_PRICING_RULE_AUTO_MINIMUM_FEEDBACK = 'intelligent-repricing-rules.html';
    const UG_PRICING_RULE_AUTO_FEEDBACK_COUNT = 'intelligent-repricing-rules.html';
    const UG_PRICING_RULE_AUTO_CONDITION = 'competitor-conditional-variances.html';
    const UG_PRICING_RULE_PRICE_MOVEMENT = 'price-adjustment.html'; // increase or decrease
    const UG_PRICING_RULE_PRICE_MOVEMENT_TWO = 'price-adjustment.html'; // increase, decrease, or match
    const UG_PRICING_RULE_FLOOR = 'floor-price.html';
    const UG_PRICING_RULE_FLOOR_PRICE_MOVEMENT = 'floor-price.html';
    const UG_PRICING_RULE_CEILING = 'optional-ceiling-price.html';
    const UG_PRICING_RULE_CEILING_PRICE_MOVEMENT = 'optional-ceiling-price.html';

    /**
     * URL links for form field tooltips
     *
     * SECTION LISTING OVERRIDES
     */
    const UG_LIST_PRICE_OVERRIDE = 'overrides.html';
    const UG_HANDLING_TIME_OVERRIDE = 'overrides.html';
    const UG_CONDITION_OVERRIDE = 'overrides.html';
    const UG_CONDITION_NOTES_OVERRIDE = 'overrides.html';

    /**
     * URL links for form field tooltips
     *
     * SECTION MAIN MENU ATTRIBUTE CREATION FROM
     */
    const UG_CATALOG_ATTRIBUTE = 'creating-attributes.html';

    /**
     * Constants containing Amazon account status
     */
    /** @var int Manually disabled account */
    const ACCOUNT_STATUS_INACTIVE = 0;
    /** @var int Active account which syncs to remote service */
    const ACCOUNT_STATUS_ACTIVE = 1;
    /** @var int Account didn't finish all required setup steps to list products on Amazon */
    const ACCOUNT_STATUS_INCOMPLETE = 2;

    const ACCOUNT_AUTH_STATUS_AUTHENTICATED = 'authenticated';
    const ACCOUNT_AUTH_STATUS_PENDING_AUTHENTICATION = 'pending_authentication';

    /**
     * Constants containing Amazon product id type codes
     */
    const TYPE_ASIN = 1;

    const AMAZON_ID_TYPES = [
        'asin',
        'ean',
        'gcid',
        'isbn',
        'upc',
        'general'
    ];

    /**
     * Constants containing Amazon list status codes.
     * If listing shown under Product Listings tabs, also reflect which tab shown under.
     */
    const VALIDATE_ASIN_LIST_STATUS = 1;
    const MISSING_CONDITION_LIST_STATUS = 2;
    const REMOVE_IN_PROGRESS_LIST_STATUS = 3;
    const READY_LIST_STATUS = 4;
    const NOMATCH_LIST_STATUS = 5;
    const MULTIPLE_LIST_STATUS = 6;
    const VARIANTS_LIST_STATUS = 7;
    const LIST_IN_PROGRESS_LIST_STATUS = 8;
    const ENDED_LIST_STATUS = 9;
    const TOBEENDED_LIST_STATUS = 10;
    const CONDITION_OVERRIDE_LIST_STATUS = 11;
    const ERROR_LIST_STATUS = 12;
    const GENERAL_SEARCH_LIST_STATUS = 13;
    const NO_LONGER_ELIGIBLE_STATUS = 14;
    const THIRDPARTY_LIST_STATUS = 98;
    const ACTIVE_LIST_STATUS = 99;
    /**
     * Constants containing attributes to exclude in dropdown options
     */
    const ATTRIBUTE_EXCLUSION = [
        'custom_design',
        'custom_layout',
        'meta_title',
        'msrp_display_actual_price_type',
        'options_container',
        'page_layout',
        'price_view',
        'shipment_type',
        'status',
        'tax_class_id',
        'tier_price',
        'gift_message_available',
        'url_key',
        'visibility',
        'allow_open_amount',
        'gift_wrapping_available',
        'is_returnable',
        'image_label',
        'small_image_label',
        'allow_message',
        'email_template',
        'is_redeemable',
        'upsell_tgtr_position_behavior',
        'upsell_tgtr_position_limit',
        'related_tgtr_position_behavior',
        'related_tgtr_position_limit',
        'use_config_allow_message',
        'use_config_email_template',
        'use_config_is_redeemable',
        'use_config_lifetime'
    ];

    /**
     * Constants containing Amazon listing condition codes
     */
    const EMPTY_CONDITION_CODE = 0;
    const NEW_CONDITION_TEXT = 'New';
    const NEW_CONDITION_CODE = 11;
    const REFURBISHED_CONDITION_TEXT = 'Refurbished';
    const REFURBISHED_CONDITION_CODE = 10;
    const USEDLIKENEW_CONDITION_TEXT = 'UsedLikeNew';
    const USEDLIKENEW_CONDITION_CODE = 1;
    const USEDVERYGOOD_CONDITION_TEXT = 'UsedVeryGood';
    const USEDVERYGOOD_CONDITION_CODE = 2;
    const USEDGOOD_CONDITION_TEXT = 'UsedGood';
    const USEDGOOD_CONDITION_CODE = 3;
    const USEDACCEPTABLE_CONDITION_TEXT = 'UsedAcceptable';
    const USEDACCEPTABLE_CONDITION_CODE = 4;
    const COLLECTIBLELIKENEW_CONDITION_TEXT = 'CollectibleLikeNew';
    const COLLECTIBLELIKENEW_CONDITION_CODE = 5;
    const COLLECTIBLEVERYGOOD_CONDITION_TEXT = 'CollectibleVeryGood';
    const COLLECTIBLEVERYGOOD_CONDITION_CODE = 6;
    const COLLECTIBLEGOOD_CONDITION_TEXT = 'CollectibleGood';
    const COLLECTIBLEGOOD_CONDITION_CODE = 7;
    const COLLECTIBLEACCEPTABLE_CONDITION_TEXT = 'CollectibleAcceptable';
    const COLLECTIBLEACCEPTABLE_CONDITION_CODE = 8;

    /**
     * Marketplace data
     */
    const MARKETPLACES = [
        'US' => [
            'countryCode' => 'US',
            'currency' => 'USD',
            'fulfillmentCode' => 'AMAZON_NA',
            'name' => 'United States',
            'region' => 'NA',
            'enabled' => true
        ],
        'CA' => [
            'countryCode' => 'CA',
            'currency' => 'CAD',
            'fulfillmentCode' => 'AMAZON_NA',
            'name' => 'Canada',
            'region' => 'NA',
            'enabled' => true
        ],
        'CN' => [
            'countryCode' => 'CN',
            'currency' => 'CNY',
            'fulfillmentCode' => 'AMAZON_CN',
            'name' => 'China',
            'region' => 'CN',
            'enabled' => false
        ],
        'FR' => [
            'countryCode' => 'FR',
            'currency' => 'EUR',
            'fulfillmentCode' => 'AMAZON_EU',
            'name' => 'France',
            'region' => 'EU',
            'enabled' => false
        ],
        'DE' => [
            'countryCode' => 'DE',
            'currency' => 'EUR',
            'fulfillmentCode' => 'AMAZON_EU',
            'name' => 'Germany',
            'region' => 'EU',
            'enabled' => false
        ],
        'IN' => [
            'countryCode' => 'IN',
            'currency' => 'INR',
            'fulfillmentCode' => 'AMAZON_IN',
            'name' => 'India',
            'region' => 'EU',
            'enabled' => false
        ],
        'IT' => [
            'countryCode' => 'IT',
            'currency' => 'EUR',
            'fulfillmentCode' => 'AMAZON_EU',
            'name' => 'Italy',
            'region' => 'EU',
            'enabled' => false
        ],
        'JP' => [
            'countryCode' => 'JP',
            'currency' => 'JPY',
            'fulfillmentCode' => 'AMAZON_JP',
            'name' => 'Japan',
            'region' => 'FE',
            'enabled' => false
        ],
        'MX' => [
            'countryCode' => 'MX',
            'currency' => 'MXN',
            'fulfillmentCode' => 'AMAZON_NA',
            'name' => 'Mexico',
            'region' => 'NA',
            'enabled' => true
        ],
        'GB' => [
            'countryCode' => 'GB',
            'currency' => 'GBP',
            'fulfillmentCode' => 'AMAZON_EU',
            'name' => 'United Kingdom',
            'region' => 'EU',
            'enabled' => true
        ],
        'BR' => [
            'countryCode' => 'BR',
            'currency' => 'BRL',
            'fulfillmentCode' => 'DEFAULT',
            'name' => 'Brazil',
            'region' => 'NA',
            'enabled' => false
        ],
        'AU' => [
            'countryCode' => 'AU',
            'currency' => 'AUD',
            'fulfillmentCode' => 'AMAZON_JP',
            'name' => 'Australia',
            'region' => 'FE',
            'enabled' => false
        ],
        'ES' => [
            'countryCode' => 'ES',
            'currency' => 'EUR',
            'fulfillmentCode' => 'AMAZON_EU',
            'name' => 'Spain',
            'region' => 'EU',
            'enabled' => false
        ],
        'TR' => [
            'countryCode' => 'TR',
            'currency' => 'TRY',
            'fulfillmentCode' => 'DEFAULT',
            'name' => 'Turkey',
            'region' => 'EU',
            'enabled' => false
        ],
    ];

    /**
     * Constants containing Amazon listing attribute mapping
     */
    const EMPTY_ATTRIBUTE = 0;
    const ASIN_ATTRIBUTE = 1;
    const ISBN_ATTRIBUTE = 2;
    /**
     * Constants containing Amazon order statuses
     */
    const DO_NOT_BUILD_ORDER_STATUS = [
        self::PENDING_AVAILABILITY_ORDER_STATUS,
        self::PENDING_ORDER_STATUS,
        self::CANCELED_ORDER_STATUS,
        self::ERROR_ORDER_STATUS,
    ];

    const DO_NOT_CANCEL_ORDER_STATUS = [
        self::CANCELED_ORDER_STATUS,
        self::SHIPPED_ORDER_STATUS,
        self::PARTIALLY_SHIPPED,
    ];

    /**
     * @var array
     */
    const PROCESSABLE_STATUSES = [
        self::UNSHIPPED_ORDER_STATUS,
        self::PARTIALLY_SHIPPED,
        self::SHIPPED_ORDER_STATUS,
    ];

    const PENDING_ORDER_STATUS = 'Pending';
    const PENDING_AVAILABILITY_ORDER_STATUS = 'PendingAvailability';
    const CANCELED_ORDER_STATUS = 'Canceled';
    const ERROR_ORDER_STATUS = 'Error';
    const UNSHIPPED_ORDER_STATUS = 'Unshipped';
    const PARTIALLY_SHIPPED = 'PartiallyShipped';
    const SHIPPED_ORDER_STATUS = 'Shipped';
    const COMPLETED_ORDER_STATUS = 'Completed';

    /**
     * Constants containing Amazon carrier codes
     */
    const FEDEX_CODE = 'fedex';
    const UPS_CODE = 'ups';
    const USPS_CODE = 'usps';
    const FEDEX_AMAZON_CODE = 'FedEx';
    const UPS_AMAZON_CODE = 'UPS';
    const USPS_AMAZON_CODE = 'USPS';
    const CARRIER_TYPE = 'CarrierCode';
    const CARRIER_NAME = 'CarrierName';

    /**
     * Constants containing Amazon order fulfillment channel
     */
    const ORDER_FULFILLED_BY_AMAZON = 'AFN';
    const ORDER_FULFILLED_BY_MERCHANT = 'MFN';
    const UPC_ATTRIBUTE = 3;
    const EAN_ATTRIBUTE = 4;
    const GCID_ATTRIBUTE = 5;
    const GENERAL_ATTRIBUTE = 6;

    /**
     * Holds conversion of product id type setting
     */
    const PRODUCT_ID_TYPES = [
        '1' => 'ASIN',
        '2' => 'ISBN',
        '3' => 'UPC',
        '4' => 'EAN',
        '5' => 'GCID',
        '6' => 'SellerSKU'
    ];

    /**
     * Holds labels (names) of API Types
     */
    const LISTING_API = 'Listing';
    const BBB_PRICE_API = 'BbbPrice';
    const LOWEST_PRICE_API = 'LowestPrice';
    const DEFECT_API = 'Defect';
    const ATTRIBUTE_API = 'Attribute';
    const ATTRIBUTE_VALUE_API = 'AttributeValue';
    const ORDER_API = 'Order';
    const ORDER_ITEM_API = 'OrderItem';
    const ERROR_API = 'Error';
    const LOG_API = 'Log';
    const LIST_STATUS_API = 'ListStatus';
    const MULTIPLE_API = 'Multiple';
    const VARIANT_API = 'Variant';
    const REPORT_API = 'ReportRun';

    // phpcs:disable Magento2.Functions.StaticFunction

    /**
     * @return array
     */
    public static function getCountryCodes(): array
    {
        return array_keys(self::MARKETPLACES);
    }

    /**
     * @param string $countryCode
     * @param string $default
     * @return string
     */
    public static function getMarketplaceName(string $countryCode, $default = ''): string
    {
        return self::MARKETPLACES[$countryCode]['name'] ?? $default;
    }

    /**
     * @param string $countryCode
     * @param string $default
     * @return string
     */
    public static function getCurrencyCode(string $countryCode, string $default = 'USD')
    {
        return self::MARKETPLACES[$countryCode]['currency'] ?? $default;
    }

    /**
     * @param string $countryCode
     * @param string $default
     * @return string
     */
    public static function getFulfillmentCode(string $countryCode, string $default = 'AMAZON_NA'): string
    {
        return self::MARKETPLACES[$countryCode]['fulfillmentCode'] ?? $default;
    }

    /**
     * @param string $countryCode
     * @param string $default
     * @return string
     */
    public static function getRegionName(string $countryCode, $default = 'NA'): string
    {
        return self::MARKETPLACES[$countryCode]['region'] ?? $default;
    }

    public static function getMarketplaceByCountryCode(string $countryCode): ?array
    {
        return self::MARKETPLACES[$countryCode] ?? null;
    }

    public static function getEnabledMarketplaces(): array
    {
        return array_filter(self::MARKETPLACES, static function (array $data) {
            return !empty($data['enabled']);
        });
    }

    public static function getAmazonIdTypes(): array
    {
        return self::AMAZON_ID_TYPES;
    }
    // phpcs:enable Magento2.Functions.StaticFunction
}
