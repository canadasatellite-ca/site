<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Settings\Listings;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Directory\Model\Currency as StoreCurrency;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class Modifier implements ModifierInterface
{
    /** Container fieldset prefix */
    const CONTAINER_PREFIX = 'container_';

    /** @var ArrayManager $arrayManager */
    protected $arrayManager;
    /** @var StoreManagerInterface $storeManager */
    protected $storeManager;
    /** @var StoreCurrency $currency */
    protected $currency;
    /** @var Http $request */
    protected $request;
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var ProductAttributeRepositoryInterface $productAttributeRepository */
    protected $productAttributeRepository;

    /**
     * @param ArrayManager $arrayManager
     * @param StoreManagerInterface $storeManager
     * @param StoreCurrency $currency
     * @param Http $request
     * @param AccountRepositoryInterface $accountRepository
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(
        ArrayManager $arrayManager,
        StoreManagerInterface $storeManager,
        StoreCurrency $currency,
        Http $request,
        AccountRepositoryInterface $accountRepository,
        AccountListingRepositoryInterface $accountListingRepository,
        ProductAttributeRepositoryInterface $productAttributeRepository
    ) {
        $this->arrayManager = $arrayManager;
        $this->storeManager = $storeManager;
        $this->currency = $currency;
        $this->request = $request;
        $this->accountRepository = $accountRepository;
        $this->accountListingRepository = $accountListingRepository;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        /** @var AccountInterface */
        $account = $this->getAccount();
        /** @var AccountListingInterface */
        $accountListing = $this->getAccountListing();
        $countryCode = $this->getCountryCode();

        $meta = $this->prepareMerchantData($meta);
        $meta = $this->prepareThirdpartyFields($meta, $accountListing);
        $meta = $this->prepareBusinessPriceFields($meta, $account, $accountListing);
        $meta = $this->prepareCurrencyFields($meta, $accountListing, $countryCode);
        $meta = $this->prepareVatFields($meta, $accountListing);
        $meta = $this->preparePtcFields($meta, $accountListing, $account);
        $meta = $this->prepareFulfilledByFields($meta, $accountListing);
        $meta = $this->prepareConditionFields($meta, $accountListing);
        $meta = $this->prepareTooltipLinks($meta);

        return $meta;
    }

    /**
     * Prepare merchant data
     *
     * @param array $meta
     * @return array $meta
     */
    private function prepareMerchantData(array $meta)
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');

        $meta = array_replace_recursive(
            $meta,
            [
                'listing_settings' => [
                    'children' => [
                        'merchant_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $merchantId
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Prepare tooltip links
     *
     * @param array $meta
     * @return array
     */
    private function prepareTooltipLinks(array $meta)
    {
        $autoListTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_AUTO_LIST
        ];

        $handlingTimeTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_HANDLING_TIME
        ];

        $thirdpartyIsActiveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_THIRDPARTY_IS_ACTIVE
        ];

        $thirdpartySkuFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_THIRDPARTY_SKU_FIELD
        ];

        $thirdpartyAsinFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_THIRDPARTY_ASIN_FIELD
        ];

        $priceFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICE_FIELD
        ];

        $mapPriceFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_MAP_PRICE_FIELD
        ];

        $strikePriceFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_STRIKE_PRICE_FIELD
        ];

        $vatIsActiveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_VAT_IS_ACTIVE
        ];

        $vatPercentageTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_VAT_PERCENTAGE
        ];

        $managePtcToolTip = [
            'link' => Definitions::UG_URL . Definitions::UG_MANAGE_PTC
        ];

        $minQtyTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_MIN_QTY
        ];

        $maxQtyTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_MAX_QTY
        ];

        $customQtyTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CUSTOM_QTY
        ];

        $fulfilledByTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_FULFILLED_BY
        ];

        $fulfilledByFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_FULFILLED_BY_FIELD
        ];

        $fulfilledBySellerSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_FULFILLED_BY_SELLER_SELECT
        ];

        $fulfilledByAmazonSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_FULFILLED_BY_AMAZON_SELECT
        ];

        $fulfilledBySellerTextTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_FULFILLED_BY_SELLER_TEXT
        ];

        $fulfilledByAmazonTextTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_FULFILLED_BY_AMAZON_TEXT
        ];

        $asinMappingFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_ASIN_MAPPING_FIELD
        ];

        $eanMappingFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_EAN_MAPPING_FIELD
        ];

        $isbnMappingFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_ISBN_MAPPING_FIELD
        ];

        $upcMappingFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_UPC_MAPPING_FIELD
        ];

        $gcidMappingFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_GCID_MAPPING_FIELD
        ];

        $generalMappingFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_GENERAL_MAPPING_FIELD
        ];

        $listConditionTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION
        ];

        $listConditionFieldTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_FIELD
        ];

        $listConditionNewSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_NEW_SELECT
        ];

        $listConditionRefurbishedSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_REFURBISHED_SELECT
        ];

        $listConditionLikenewSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_LIKENEW_SELECT
        ];

        $listConditionVerygoodSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_VERYGOOD_SELECT
        ];

        $listConditionGoodSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_GOOD_SELECT
        ];

        $listConditionAcceptableSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_ACCEPTABLE_SELECT
        ];

        $listConditionCollectibleLikenewSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_COLLECTIBLE_LIKENEW_SELECT
        ];

        $listConditionCollectibleVerygoodSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_COLLECTIBLE_VERYGOOD_SELECT
        ];

        $listConditionCollectibleGoodSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_COLLECTIBLE_GOOD_SELECT
        ];

        $listConditionCollectibleAcceptableSelectTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_CONDITION_COLLECTIBLE_ACCEPTABLE_SELECT
        ];

        $ccIsActiveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CC_IS_ACTIVE
        ];

        // @codingStandardsIgnoreStart Magento2.Files.LineLength.MaxExceeded
        $meta = array_replace_recursive(
            $meta,
            [
                'listing_settings' => [
                    'children' => [
                        'auto_list' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/auto-list',
                                        'tooltip' => $autoListTooltip
                                    ]
                                ]
                            ]
                        ],
                        'handling_time' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/handling-time',
                                        'tooltip' => $handlingTimeTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'thirdparty' => [
                    'children' => [
                        'thirdparty_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/thirdparty-is-active',
                                        'tooltip' => $thirdpartyIsActiveTooltip
                                    ]
                                ]
                            ]
                        ],
                        'thirdparty_sku_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/thirdparty-sku-field',
                                        'tooltip' => $thirdpartySkuFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'thirdparty_asin_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/thirdparty-asin-field',
                                        'tooltip' => $thirdpartyAsinFieldTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'pricing' => [
                    'children' => [
                        'price_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/price-field',
                                        'tooltip' => $priceFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'map_price_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/map-price-field',
                                        'tooltip' => $mapPriceFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'strike_price_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/strike-price-field',
                                        'tooltip' => $strikePriceFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'vat_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/vat-is-active',
                                        'tooltip' => $vatIsActiveTooltip
                                    ]
                                ]
                            ]
                        ],
                        'vat_percentage' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/vat-percentage',
                                        'tooltip' => $vatPercentageTooltip
                                    ]
                                ]
                            ]
                        ],
                        'manage_ptc' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/manage-ptc',
                                        'tooltip' => $managePtcToolTip
                                    ]
                                ]
                            ]
                        ],
                        'cc_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/cc-is-active',
                                        'tooltip' => $ccIsActiveTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'listing_quantity' => [
                    'children' => [
                        'min_qty' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/min-qty',
                                        'tooltip' => $minQtyTooltip
                                    ]
                                ]
                            ]
                        ],
                        'max_qty' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/max-qty',
                                        'tooltip' => $maxQtyTooltip
                                    ]
                                ]
                            ]
                        ],
                        'custom_qty' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/custom-qty',
                                        'tooltip' => $customQtyTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'fulfilled_by' => [
                    'children' => [
                        'fulfilled_by' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/fulfilled-by',
                                        'tooltip' => $fulfilledByTooltip
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/fulfilled-by-field',
                                        'tooltip' => $fulfilledByFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_seller_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/fulfilled-by-seller-select',
                                        'tooltip' => $fulfilledBySellerSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_amazon_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/fulfilled-by-amazon-select',
                                        'tooltip' => $fulfilledByAmazonSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_seller_text' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/fulfilled-by-seller-text',
                                        'tooltip' => $fulfilledBySellerSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_amazon_text' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/fulfilled-by-amazon-text',
                                        'tooltip' => $fulfilledByAmazonTextTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'search_settings' => [
                    'children' => [
                        'asin_mapping_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/asin-mapping-field',
                                        'tooltip' => $asinMappingFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'ean_mapping_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/ean-mapping-field',
                                        'tooltip' => $eanMappingFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'isbn_mapping_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/isbn-mapping-field',
                                        'tooltip' => $isbnMappingFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'gcid_mapping_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/gcid-mapping-field',
                                        'tooltip' => $gcidMappingFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'upc_mapping_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/upc-mapping-field',
                                        'tooltip' => $upcMappingFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'general_mapping_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/general-mapping-field',
                                        'tooltip' => $generalMappingFieldTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'list_condition_fieldset' => [
                    'children' => [
                        'list_condition' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition',
                                        'tooltip' => $listConditionTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-field',
                                        'tooltip' => $listConditionFieldTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_new_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-new-select',
                                        'tooltip' => $listConditionNewSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_refurbished_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-refurbished-select',
                                        'tooltip' => $listConditionRefurbishedSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_likenew_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-likenew-select',
                                        'tooltip' => $listConditionLikenewSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_verygood_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-verygood-select',
                                        'tooltip' => $listConditionVerygoodSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_good_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-good-select',
                                        'tooltip' => $listConditionGoodSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_acceptable_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-acceptable-select',
                                        'tooltip' => $listConditionAcceptableSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_collectible_likenew_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-collectible-likenew-select',
                                        'tooltip' => $listConditionCollectibleLikenewSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_collectible_verygood_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-collectible-verygood-select',
                                        'tooltip' => $listConditionCollectibleVerygoodSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_collectible_good_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-collectible-good-select',
                                        'tooltip' => $listConditionCollectibleGoodSelectTooltip
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_collectible_acceptable_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-condition-collectible-acceptable-select',
                                        'tooltip' => $listConditionCollectibleAcceptableSelectTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        // @codingStandardsIgnoreEnd Magento2.Files.LineLength.MaxExceeded

        return $meta;
    }

    /**
     * Create third party import form field dependencies
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @return array
     */
    private function prepareThirdpartyFields(array $meta, AccountListingInterface $account)
    {
        // set default values
        if (!$merchantId = $account->getMerchantId()) {
            $account->setThirdpartyIsActive(true);
        }

        /** @var bool */
        $flag = ($account->getThirdpartyIsActive()) ? false : true;

        $meta = array_replace_recursive(
            $meta,
            [
                'thirdparty' => [
                    'children' => [
                        'thirdparty_sku_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => $flag
                                    ]
                                ]
                            ]
                        ],
                        'thirdparty_asin_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => $flag
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Create business price form field dependencies
     *
     * @param array $meta
     * @param AccountInterface $account
     * @param AccountListingInterface $accountListing
     * @return array
     */
    private function prepareBusinessPriceFields(
        array $meta,
        AccountInterface $account,
        AccountListingInterface $accountListing
    ): array {
        $tierFlag = true;
        $tierIsActiveFlag = true;
        $enabledMarkets = [
            'US',
            'FR',
            'DE',
            'GB'
        ];
        $notice = 'Business pricing is not available within this marketplace.';
        $countryCode = $account->getCountryCode();

        if (!in_array($countryCode, $enabledMarkets, true)) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'business_pricing' => [
                        'children' => [
                            'business_is_active' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true,
                                            'value' => false,
                                            'notice' => $notice
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        } elseif ($accountListing->getBusinessIsActive()) {
            // if business pricing is enabled
            $tierIsActiveFlag = false;
            $tierFlag = ($accountListing->getTierIsActive()) ? false : true;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'business_pricing' => [
                    'children' => [
                        'tier_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => $tierIsActiveFlag
                                    ]
                                ]
                            ]
                        ],
                        'tier_group_one' => [
                            'children' => [
                                'qty_price_one' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ],
                                'lower_bound_one' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'tier_group_two' => [
                            'children' => [
                                'qty_price_two' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ],
                                'lower_bound_two' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'tier_group_three' => [
                            'children' => [
                                'qty_price_three' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ],
                                'lower_bound_three' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'tier_group_four' => [
                            'children' => [
                                'qty_price_four' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ],
                                'lower_bound_four' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'tier_group_five' => [
                            'children' => [
                                'qty_price_five' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ],
                                'lower_bound_five' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => $tierFlag
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Create VAT form field dependencies
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @return array
     */
    private function prepareVatFields(array $meta, AccountListingInterface $account)
    {
        /** @var bool */
        $flag = true;

        if ($account->getVatIsActive()) {
            $flag = false;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'pricing' => [
                    'children' => [
                        'vat_percentage' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => $flag
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Create PTC form field dependencies
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @param AccountInterface
     * @return array
     */
    private function preparePtcFields(
        array $meta,
        AccountListingInterface $accountListing,
        AccountInterface $account
    ): array {
        $managePtcVisible = false;
        $defaultPtcVisible = false;

        $countryCode = $account->getCountryCode();
        $region = Definitions::getRegionName($countryCode);

        if ($region === 'EU') {
            $managePtcVisible = true;
        }

        if ($managePtcVisible && $accountListing->getManagePtc()) {
            $defaultPtcVisible = true;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'pricing' => [
                    'children' => [
                        'manage_ptc' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $managePtcVisible
                                    ]
                                ]
                            ]
                        ],
                        'default_ptc' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $defaultPtcVisible
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Create listing condition form field dependencies
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @return array
     */
    private function prepareConditionFields(array $meta, AccountListingInterface $account)
    {
        /** @var int */
        $listCondition = $account->getListCondition();

        if (!$account->getMerchantId()) {
            $listCondition = Definitions::NEW_CONDITION_CODE;
        }

        /** @var string */
        $value = $account->getListConditionField();
        /** @var array */
        $options = $this->generateAttributeOptions($value);
        /** @var bool */
        $sellerNotesFlag = false;
        /** @var bool */
        $listConditionFieldFlag = false;
        /** @var bool */
        $toggleConditionFlag = false;

        // condition source
        if ($listCondition) {
            // if new condition
            if ($listCondition != Definitions::NEW_CONDITION_CODE) {
                $sellerNotesFlag = true;
            }
        } else {
            $listConditionFieldFlag = true;
            $toggleConditionFlag = true;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'list_condition_fieldset' => [
                    'children' => [
                        'seller_notes' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $sellerNotesFlag
                                    ]
                                ]
                            ]
                        ],
                        'list_condition_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $listConditionFieldFlag
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $meta = $this->toggleConditionFieldsets($meta, $toggleConditionFlag);
        $meta = $this->loadConditionOptions($meta, $account, $options);
        return $meta;
    }

    /**
     * Loads the options by option type for listing condition
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @param array $options
     * @return array
     */
    private function loadConditionOptions(array $meta, AccountListingInterface $account, $options)
    {
        // text field
        if (empty($options)) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'list_condition_fieldset' => [
                        'children' => [
                            'attribute_new_group' => [
                                'children' => [
                                    'list_condition_new_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'attribute_refurbished_group' => [
                                'children' => [
                                    'list_condition_refurbished_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'attribute_likenew_group' => [
                                'children' => [
                                    'list_condition_likenew_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'attribute_verygood_group' => [
                                'children' => [
                                    'list_condition_verygood_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'attribute_good_group' => [
                                'children' => [
                                    'list_condition_good_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'attribute_acceptable_group' => [
                                'children' => [
                                    'list_condition_acceptable_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'collectible_likenew_group' => [
                                'children' => [
                                    'list_condition_collectible_likenew_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'collectible_verygood_group' => [
                                'children' => [
                                    'list_condition_collectible_verygood_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'collectible_good_group' => [
                                'children' => [
                                    'list_condition_collectible_good_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            'collectible_acceptable_group' => [
                                'children' => [
                                    'list_condition_collectible_acceptable_select' => [
                                        'arguments' => [
                                            'data' => [
                                                'config' => [
                                                    'visible' => false
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            );
            return $meta;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'list_condition_fieldset' => [
                    'children' => [
                        'attribute_new_group' => [
                            'children' => [
                                'list_condition_new_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_new_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionNew(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'attribute_refurbished_group' => [
                            'children' => [
                                'list_condition_refurbished_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_refurbished_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionRefurbished(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ],
                            ]
                        ],
                        'attribute_likenew_group' => [
                            'children' => [
                                'list_condition_likenew_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_likenew_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionLikenew(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'attribute_verygood_group' => [
                            'children' => [
                                'list_condition_verygood_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_verygood_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionVerygood(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'attribute_good_group' => [
                            'children' => [
                                'list_condition_good_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_good_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionGood(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'attribute_acceptable_group' => [
                            'children' => [
                                'list_condition_acceptable_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_acceptable_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionAcceptable(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'collectible_likenew_group' => [
                            'children' => [
                                'list_condition_collectible_likenew_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_collectible_likenew_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionCollectibleLikenew(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'collectible_verygood_group' => [
                            'children' => [
                                'list_condition_collectible_verygood_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_collectible_verygood_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionCollectibleVerygood(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'collectible_good_group' => [
                            'children' => [
                                'list_condition_collectible_good_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_collectible_good_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionCollectibleGood(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'collectible_acceptable_group' => [
                            'children' => [
                                'list_condition_collectible_acceptable_text' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'list_condition_collectible_acceptable_select' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'value' => $account->getListConditionCollectibleAcceptable(),
                                                'options' => $options
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Show / hide condition fieldsets
     *
     * @param array $meta
     * @param bool $visible
     * @return array
     */
    private function toggleConditionFieldsets(array $meta, $visible)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'list_condition_fieldset' => [
                    'children' => [
                        'attribute_new_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'attribute_refurbished_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'attribute_likenew_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'attribute_verygood_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'attribute_good_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'attribute_acceptable_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'collectible_likenew_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'collectible_verygood_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'collectible_good_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                        'collectible_acceptable_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $visible
                                    ]
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Create fulfilled by form field dependencies
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @return array
     */
    private function prepareFulfilledByFields(array $meta, AccountListingInterface $account)
    {
        // set default values
        if (!$merchantId = $account->getMerchantId()) {
            $account->setFulfilledBy(1);
        }

        /** @var string */
        $value = $account->getFulfilledByField();
        /** @var array */
        $options = $this->generateAttributeOptions($value);
        /** @var bool */
        $flag = ($account->getFulfilledBy()) ? false : true;

        $meta = array_replace_recursive(
            $meta,
            [
                'fulfilled_by' => [
                    'children' => [
                        'fulfilled_by_field' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $flag
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_seller_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $flag
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_amazon_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $flag
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_seller_text' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $flag
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_amazon_text' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => $flag
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $meta = $this->loadFulfilledByOptions($meta, $account, $options);
        return $meta;
    }

    /**
     * Loads options by option type
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @param array $options
     * @return array
     */
    private function loadFulfilledByOptions(array $meta, AccountListingInterface $account, $options)
    {
        if (empty($options)) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'fulfilled_by' => [
                        'children' => [
                            'fulfilled_by_seller_select' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'visible' => false
                                        ]
                                    ]
                                ]
                            ],
                            'fulfilled_by_amazon_select' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'visible' => false
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            return $meta;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'fulfilled_by' => [
                    'children' => [
                        'fulfilled_by_seller_text' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => false
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_amazon_text' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => false
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_seller_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $account->getFulfilledBySeller(),
                                        'options' => $options
                                    ]
                                ]
                            ]
                        ],
                        'fulfilled_by_amazon_select' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $account->getFulfilledByAmazon(),
                                        'options' => $options
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Create currency form field dependencies
     *
     * @param array $meta
     * @param AccountListingInterface $account
     * @param string $countryCode
     * @return array
     * @throws NoSuchEntityException
     */
    private function prepareCurrencyFields(array $meta, AccountListingInterface $account, string $countryCode): array
    {
        /** @var array */
        $ccIsActiveContainer = $this->arrayManager->set(
            'arguments/data/config',
            [],
            [
                'formElement' => 'container',
                'componentType' => 'container'
            ]
        );

        // get currency conversion options
        $store = $this->storeManager->getStore();
        $codes = $store->getAvailableCurrencyCodes(true);
        $base = $store->getBaseCurrency();
        $baseCode = $base->getData('currency_code');
        $rates = $this->currency->getCurrencyRates($base, $codes);
        $ccIsActive = ($account->getCcIsActive()) ? true : false;
        $defaultCurrency = Definitions::getCurrencyCode($countryCode);

        $conversions = [];
        $count = 0;

        foreach ($rates as $symbol => $rate) {
            // if same as base skip
            if ($symbol == $baseCode) {
                continue;
            }
            $count++;
            $conversions[] = [
                'value' => $symbol,
                'label' => $baseCode . ' to ' . $symbol . ':   ' . round($rate, 6, PHP_ROUND_HALF_UP)
            ];
        }

        // if currency conversions exist
        if ($count) {
            $ccIsActiveField['arguments']['data']['config'] = [
                'dataType' => 'boolean',
                'componentType' => 'field',
                'component' => 'Magento_Amazon/js/form/element/price-currency-conversion',
                'formElement' => 'select',
                'visible' => 1,
                'label' => __('Currency Conversion'),
                'code' => 'cc_is_active',
                'default' => 0,
                'sort_order' => '60',
                'options' => [
                    ['value' => '0', 'label' => __('Disabled')],
                    ['value' => '1', 'label' => __('Enabled')]
                ],
                'notice' => __('The current default currencies are as follows:  Magento = ') .
                    $baseCode .
                    __(', Amazon = ') .
                    $defaultCurrency .
                    __('.')
            ];
            $ccRateField['arguments']['data']['config'] = [
                'dataType' => 'text',
                'componentType' => 'field',
                'formElement' => 'select',
                'visible' => $ccIsActive,
                'label' => __('Currency Conversion Rate'),
                'code' => 'cc_rate',
                'sort_order' => '70',
                'options' => $conversions
            ];

            $ccIsActiveContainer['children']['cc_is_active'] = $ccIsActiveField;
            $ccIsActiveContainer['children']['cc_rate'] = $ccRateField;
        } else {
            $ccIsActiveField['arguments']['data']['config'] = [
                'dataType' => 'boolean',
                'componentType' => 'field',
                'formElement' => 'select',
                'visible' => 1,
                'label' => __('Currency Conversion'),
                'code' => 'cc_is_active',
                'sort_order' => '60',
                'options' => [
                    ['value' => '0', 'label' => __('Disabled')]
                ],
                'notice' => __('There are no active currency conversion rates setup within Magento.  ') .
                    __('To setup a currency conversion rate, please see Stores => Currency Rates.')
            ];
            $ccIsActiveContainer['children']['cc_is_active'] = $ccIsActiveField;
        }

        $fieldMeta[static::CONTAINER_PREFIX . 'currency'] = $ccIsActiveContainer;

        // set fieldset
        $meta['pricing']['children'] = $fieldMeta;

        return $meta;
    }

    /**
     * Get account
     *
     * @return AccountInterface|bool
     */
    private function getAccount()
    {
        /** @var int $merchantId */
        $merchantId = $this->request->getParam('merchant_id');

        /** @var AccountInterface $account */
        return $this->accountRepository->getByMerchantId($merchantId);
    }

    /**
     * Get account listing settings
     *
     * @return AccountListingInterface|bool
     */
    private function getAccountListing()
    {
        /** @var int $merchantId */
        $merchantId = $this->request->getParam('merchant_id');

        /** @var AccountListingInterface $account */
        return $this->accountListingRepository->getByMerchantId($merchantId);
    }

    /**
     * Get account country code
     *
     * @return string
     */
    private function getCountryCode()
    {
        /** @var int $merchantId */
        $merchantId = $this->request->getParam('merchant_id');

        /** @var AccountListingInterface $account */
        return $this->accountRepository->getByMerchantId($merchantId, true)->getCountryCode();
    }

    /**
     * Generates dynamic form field options
     *
     * @param string $selectedValue
     * @return array
     */
    private function generateAttributeOptions($selectedValue)
    {
        /** @var array */
        $response = [];

        try {
            /** @var ProductAttributeInterface */
            $attribute = $this->productAttributeRepository->get($selectedValue);
        } catch (NoSuchEntityException $e) {
            // no attribute found
            return $response;
        }

        // special handling for select type
        if ($options = $attribute->getOptions()) {
            foreach ($options as $option) {
                if (!$value = $option->getValue()) {
                    continue;
                }

                // add label to select list
                if ($label = $option->getLabel()) {
                    $response[] = [
                        'value' => $value,
                        'label' => $label,
                        'labelTitle' => $label
                    ];
                }
            }
        }

        return $response;
    }
}
